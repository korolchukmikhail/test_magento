<?php

class ROI_Cin7_Model_Product
{
    /** @var ROI_Cin7_Helper_Data */
    protected $_helper;
    protected $_categoryMap;
    protected $_defaultWebsiteId;
    protected $_attributeSetId;
    protected $_defaultRootCategoryId;
    protected $_attributeMap;

    public function __construct()
    {
        $this->_helper = Mage::helper('roi_cin7');
        $this->_defaultWebsiteId = Mage::app()->getWebsite(true)->getId();
        $this->_attributeSetId = $this->_helper->getAttributeSetId();
        $this->_defaultRootCategoryId = Mage::getModel('roi_cin7/category')->getRootCategoryIdForCin7();
        //TODO:: if empty map, then generate new
        $this->_attributeMap = $this->_helper->getAttributeMap();
    }

    public function getAllOptions()
    {
        /** @var ROI_Cin7_Model_Api_Product_Option_Import $model */
        $model = Mage::getModel('roi_cin7/api_product_option_import');
        $options = $model->getAllOptions();

        return $options;
    }

    public function updateAttributes($options)
    {
        $this->_helper->unsetAttributeMap();
        /** @var ROI_Cin7_Model_Attribute $model */
        $model = Mage::getModel('roi_cin7/attribute');
        $map = [];

        foreach ($options as $optionLabel => $option) {
            $mappedOption = $model->updateAttribute([
                'name' => $optionLabel,
                'values' => $option['values']
            ]);

            $map[$mappedOption['code']] = [
                'name' => $mappedOption['name'],
                'value' => $mappedOption['value']
            ];
        }

        $this->_helper->setAttributeMap($map);
    }

    public function getAllProducts($ids = [])
    {
        /** @var ROI_Cin7_Model_Api_Product_Import $model */
        $model = Mage::getModel('roi_cin7/api_product_import');
        $products = $model->importAllProducts($ids);

        return $products;
    }

    public function updateAllProducts($ids = [])
    {
        $productsFromCin7 = $this->getAllProducts($ids);
        $count = 0;

        try {
            /** @var ROI_Cin7_Model_Category $categoryModel */
            $categoryModel = Mage::getModel('roi_cin7/category');
            $this->_categoryMap = $categoryModel->getMapCin7MagentoIds();

            foreach ($productsFromCin7 as $cin7Product) {
                $productData = $this->_helper->prepareProductData($cin7Product);
                $sku = $cin7Product['sku'];

                /** @var Mage_Catalog_Model_Product $productModel */
                $productModel = Mage::getModel('catalog/product');
                $productId = (int)$productModel->getIdBySku($sku);


                if ($productId > 0) {
                    $this->updateProduct($productId, $productData);
                } else {
                    $this->newProduct($productData);
                }
                $count++;
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        return $count;
    }

    public function updateProduct($productId, $productData)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $productType = $product->getTypeId();

        if ($productData['product_type'] == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $productType == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
            $configProduct = $this->createConfigProduct($productData);
            $this->assignSimpleToConfig($configProduct, $product);
        }
    }

    public function newProduct($productData)
    {
        switch ($productData['product_type']) {
            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                $this->createConfigProduct($productData);
                break;
            case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
                $this->createSimpleProduct($productData, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
                break;
            default:
                Mage::log('Incorrect type product: CIN7_ID=' . $productData['sku'] . ', SKU=' . $productData['sku'], null, 'cin7_unknown_product.log', true);
                break;
        }
    }

    public function createConfigProduct($productData)
    {
        return;
        /** @var Mage_Catalog_Model_Product_Type_Configurable $configProduct */
        $configProduct = Mage::getModel('catalog/product');
        try {
            $categoryIds = $this->getCategoryIds($productData['categoryIdArray']);

            $configProduct
                //->setStoreId(1) //you can set data in store scope
                ->setWebsiteIds([$this->_defaultWebsiteId])
                ->setAttributeSetId($this->_attributeSetId)//ID of a attribute set named 'default'
                ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
                ->setCreatedAt(strtotime('now'))
                ->setSku($productData['sku'])
                ->setName($productData['name'])
                ->setWeight($productData['weight'])
                ->setStatus(1)//product status (1 - enabled, 2 - disabled)
                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->setCategoryIds($categoryIds)
                ->setDescription($productData['description'])
                ->setShortDescription($productData['description'])
                ->setTaxClassId(2)//tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                ->setMetaTitle($productData['name'])
                ->setStockData([
                    'use_config_manage_stock' => 0, //'Use config settings' checkbox
                    'manage_stock' => 1, //manage stock
                    'is_in_stock' => 1, //Stock Availability
                ]);

            $this->setMediaGallery($configProduct, $productData['images']);

            $configProduct->save();
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        return $configProduct;
    }

    public function getCategoryIds($cin7CategoryArray)
    {
        $categoryIds = [];

        foreach ($cin7CategoryArray as $cin7categoryId) {
            if (isset($this->_categoryMap[$cin7categoryId]) && $this->_categoryMap[$cin7categoryId]) {
                $categoryIds[] = $this->_categoryMap[$cin7categoryId];
            }
        }

        if (empty($categoryIds)) {
            $categoryIds[] = $this->_defaultRootCategoryId;
        }

        return $categoryIds;
    }

    public function setMediaGallery(Mage_Catalog_Model_Product $product, $images)
    {
        if (count($images) > 0) {
            $image = 0;
            foreach ($images as $imgUrl) {
                $image_location = $this->getDownloadImage("product", $imgUrl['link']);
                $mediaAttribute = [
                    'thumbnail',
                    'small_image',
                    'image'
                ];
                if ($image > 0) {
                    $mediaAttribute = null;
                }
                if ($image_location != "" && file_exists($image_location)) {
                    $product->addImageToMediaGallery($image_location, $mediaAttribute, true, false);
                }
                $image++;
            }
        }
    }

    public function getDownloadImage($type, $urlImage)
    {
        if ($urlImage == "") {
            return "";
        }
        $path = Mage::getBaseDir('base');
        $import_location = $path . '/media/catalog/' . $type . '/cin7/';
        if (!file_exists($import_location)) {
            mkdir($import_location, 0755);
        }

        // todo check if last character has /
        $file_source = $urlImage;
        $patch = pathinfo($urlImage);
        $file = $patch['filename'] . '.' . $patch['extension'];
        $file_target = $import_location . basename($file);

        $file_path = $file_target;
        if ($file != '' && !file_exists($file_target)) {
            $rh = fopen($file_source, 'rb');
            $wh = fopen($file_target, 'wb');
            if ($rh === false || $wh === false) {
                // error reading or opening file
                $file_path = "";
            } else {
                while (!feof($rh)) {
                    if (fwrite($wh, fread($rh, 1024)) === false) {
                        $file_path = $file_target;
                    }
                }
            }
            fclose($rh);
            fclose($wh);
        }
        return $file_path;
    }

    public function assignSimpleToConfig($configProduct, $simpleProduct)
    {
        /**/
        /** assigning associated product to configurable */
        /**/
        $configProduct->getTypeInstance()->setUsedProductAttributeIds(array(92)); //attribute ID of attribute 'color' in my store
        $configurableAttributesData = $configProduct->getTypeInstance()->getConfigurableAttributesAsArray();

        $configProduct->setCanSaveConfigurableAttributes(true);
        $configProduct->setConfigurableAttributesData($configurableAttributesData);

        $configurableProductsData = [];
        $configurableProductsData[$simpleProduct->getId()][] = [
            'label' => 'Green', //attribute label
            'attribute_id' => '92', //attribute ID of attribute 'color' in my store
            'value_index' => '24', //value of 'Green' index of the attribute 'color'
            'is_percent' => '0', //fixed/percent price for this option
            'pricing_value' => '21' //value for the pricing
        ];

        $configProduct->setConfigurableProductsData($configurableProductsData);

        $configProduct->save();
    }

    public function createSimpleProduct($productData, $visibility = Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $simpleProduct = Mage::getModel('catalog/product');

        try {
            $categoryIds = $this->getCategoryIds($productData['categoryIdArray']);
            $weight = $productData['weight'] ? $productData['weight'] : $productData['productOptions'][0]['weight'];
            $isInStock = (int)($productData['productOptions'][0]['stockAvailable'] > 0);

            $simpleProduct
                //->setStoreId(1) //you can set data in store scope
                ->setWebsiteIds([$this->_defaultWebsiteId])
                ->setAttributeSetId($this->_attributeSetId)
                ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
                ->setCreatedAt(strtotime('now'))
                ->setSku($productData['sku'])
                ->setName($productData['name'])
                ->setVisibility($visibility)
                ->setStatus(1)//product status (1 - enabled, 2 - disabled)
                ->setTaxClassId(2)//tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                ->setMetaTitle($productData['name'])
                ->setDescription($productData['description'])
                ->setShortDescription($productData['description'])
                ->setCategoryIds($categoryIds)
                ->setWeight($weight)
                ->setStockData([
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'min_sale_qty' => 1,
                        'max_sale_qty' => 100000,
                        'is_in_stock' => $isInStock,
                        'qty' => $productData['productOptions'][0]['stockAvailable']
                    ]
                );

            if (isset($productData['optionLabel1'])) {
                $this->setOptionAttribute($simpleProduct, $this->_helper->prepareOptionName($productData['optionLabel1']), $productData['ProductOptions'][0]['option1']);
            }
            if (isset($productData['optionLabel1'])) {
                $this->setOptionAttribute($simpleProduct, $this->_helper->prepareOptionName($productData['optionLabel1']), $productData['ProductOptions'][0]['option2']);
            }
            if (isset($productData['optionLabel1'])) {
                $this->setOptionAttribute($simpleProduct, $this->_helper->prepareOptionName($productData['optionLabel1']), $productData['ProductOptions'][0]['option3']);
            }

            if ($productData['ProductOptions'][0]['size']) {
                $simpleProduct->setSize($productData['ProductOptions'][0]['size']);
            }

            $simpleProduct->setPrice($productData['ProductOptions'][0]['retailPrice']);
            $simpleProduct->setCost($productData['ProductOptions'][0]['costAUD']);

            $groupPrice = [];

            if ($productData['productOptions'][0]['wholesalePrice']) {
                $groupPrice[] = [
                    'website_id' => $this->_defaultWebsiteId,
                    'all_groups' => false,
                    'cust_group' => 3,
                    'price' => $productData['ProductOptions'][0]['wholesalePrice']
                ];
            }

            if ($productData['productOptions'][0]['vipPrice']) {
                $groupPrice[] = [
                    'website_id' => $this->_defaultWebsiteId,
                    'all_groups' => false,
                    'cust_group' => 4,
                    'price' => $productData['ProductOptions'][0]['vipPrice']
                ];
            }
            if (count($groupPrice) > 0) {
                $simpleProduct->setData('group_price', $groupPrice);
            }

            if ($productData['ProductOptions'][0]['specialPrice']) {
                $simpleProduct->setSpecialPrice($productData['ProductOptions'][0]['image']);

                if ($productData['ProductOptions'][0]['specialsStartDate']) {
                    $strtime = strtotime($productData['ProductOptions'][0]['specialsStartDate']);
                    $simpleProduct->setSpecialFromDate(date('m-d-Y', $strtime));

                    if ($productData['ProductOptions'][0]['specialDays']) {
                        $strtime += 24 * 3600 * (int)$productData['ProductOptions'][0]['specialDays'];
                        $simpleProduct->setSpecialToDate(date('m-d-Y', $strtime));
                    }
                }
            }

            if (!empty($productData['images'])) {
                $this->setMediaGallery($simpleProduct, $productData['images']);
            }
            if ($productData['ProductOptions'][0]['image']) {
                $this->setMediaGallery($simpleProduct, [$productData['ProductOptions'][0]['image']]);
            }

            $simpleProduct->save();
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        return $simpleProduct;
    }

    private function setOptionAttribute($product, $attrCode, $attrValue)
    {
        $optionValue = '';
        if (isset($this->_attributeMap[$attrCode]) && isset($this->_attributeMap[$attrCode]['value'])) {
            $attrCode = $this->_attributeMap[$attrCode]['value'];
        }

        $attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode);

        if ($attribute && $attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
        }

        if (!empty($options)) {
            foreach ($options as $option) {
                if ($option['label'] == $attrValue) {
                    $optionValue = $option['value'];
                    break;
                }
            }
        }

        if ($attrCode) {
            $product->setData($attrCode, $optionValue);
        }
    }

}