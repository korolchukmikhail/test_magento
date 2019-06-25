<?php

class ROI_Cin7_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfig($path, $store = null)
    {
        if ($path == 'temp_dir') {
            $tmp_dir = Mage::getBaseDir('var');

            if (!is_dir($tmp_dir . '/cin7')) {
                mkdir($tmp_dir . '/cin7');
            }

            return $tmp_dir . '/cin7';
        }

        return Mage::getStoreConfig($path, $store);
    }

    public function unsetAttributeMap($storeId = null)
    {
        $this->saveConfig('roi_cin7config/attribute/map', '', $storeId);
    }

    public function setAttributeMap($map, $storeId = null)
    {
        if (is_array($map) || is_object($map)) {
            $map = serialize($map);
        }
        $this->saveConfig('roi_cin7config/attribute/map', $map, $storeId);
    }

    public function getAttributeMap()
    {
        $map = [];
        $configValue = $this->getConfig('roi_cin7config/attribute/map');
        if ($configValue) {
            $map = unserialize($configValue);
        }

        return $map;
    }

    public function saveConfig($path, $value, $storeId = null)
    {
        if (!is_null($storeId)) {
            Mage::getConfig()->saveConfig($path, $value, 'store', $storeId);
        } else {
            Mage::getConfig()->saveConfig($path, $value);
        }
    }

    public function isEnabled($store = null)
    {
        if ($this->isModuleEnabled() && $this->getConfig('roi_cin7config/general/enabled', $store)) {
            return true;
        }

        return false;
    }

    public function getBaseUrl($store = null)
    {
        return $this->getConfig('roi_cin7config/general/base_url', $store);
    }

    public function getUsername($store = null)
    {
        return $this->getConfig('roi_cin7config/general/username', $store);
    }

    public function getPassword($store = null)
    {
        return $this->getConfig('roi_cin7config/general/password', $store);
    }

    public function getTempDir($store = null)
    {
        return $this->getConfig('temp_dir', $store);
    }

    public function getCountRows($store = null)
    {
        return $this->getConfig('roi_cin7config/general/rows', $store);
    }


    public function getCin7OptionsFromFile()
    {
        $options = [];
        $tempDir = $this->getConfig('temp_dir');
        $productOptionsFile = $tempDir . '/productOptions.json';
        if (file_exists($productOptionsFile)) {
            $options = json_decode(file_get_contents($productOptionsFile), true);
        }

        return $options;
    }

    public function prepareOptionName($optionName)
    {
        if (is_string($optionName)) {
            $optionName = strtolower($optionName);
            $optionName = str_replace(' ', '_', $optionName);
        }

        return $optionName;
    }

    public function getAttributeSetId()
    {
        return Mage::getModel('catalog/product')->getDefaultAttributeSetId();
    }

    public function prepareProductData($cin7rawData)
    {
        $productData = [];
        $productType = Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;

        if (!$cin7rawData['optionLabel1'] && !$cin7rawData['optionLabel2'] && !$cin7rawData['optionLabel3']) {
            if (count($cin7rawData['productOptions']) > 1) {
                $productType = 'unknown';
            } else {
                $productType = Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;
            }
        }

        $productData['product_type'] = $productType;
        $productData['status'] = $cin7rawData['status'];
        $productData['sku'] = $cin7rawData['styleCode'];
        $productData['name'] = $cin7rawData['name'];
        $productData['description'] = $cin7rawData['description'];
        $productData['images'] = $cin7rawData['images'];
        $productData['pdfUpload'] = $cin7rawData['pdfUpload'];
        $productData['pdfDescription'] = $cin7rawData['pdfDescription'];
        $productData['supplierId'] = $cin7rawData['supplierId'];
        $productData['brand'] = $cin7rawData['brand'];
        $productData['categoryIdArray'] = $cin7rawData['categoryIdArray'];

        if ($cin7rawData['optionLabel1']) {
            $productData['optionLabel1'] = $cin7rawData['optionLabel1'];
        }
        if ($cin7rawData['optionLabel2']) {
            $productData['optionLabel2'] = $cin7rawData['optionLabel2'];
        }
        if ($cin7rawData['optionLabel3']) {
            $productData['optionLabel3'] = $cin7rawData['optionLabel3'];
        }

        $productData['weight'] = $cin7rawData['weight'];
        $productData['height'] = $cin7rawData['height'];
        $productData['width'] = $cin7rawData['width'];
        $productData['length'] = $cin7rawData['length'];
        $productData['volume'] = $cin7rawData['volume'];
        $productData['productOptions'] = [];

        foreach ($cin7rawData['productOptions'] as $productOption) {
            $productOptions = [
                'id' => $productOption['id'],
                'code' => $productOption['code'],
                'barcode' => $productOption['barcode'],
                'supplierCode' => $productOption['supplierCode'],
                'weight' => $productOption['optionWeight'],
                'size' => $productOption['size'],
                'retailPrice' => $productOption['retailPrice'],
                'wholesalePrice' => $productOption['wholesalePrice'],
                'vipPrice' => $productOption['vipPrice'],
                'specialPrice' => $productOption['specialPrice'],
                'specialsStartDate' => $productOption['specialsStartDate'],
                'specialDays' => $productOption['specialDays'],
                'stockAvailable' => $productOption['stockAvailable'],
                'stockOnHand' => $productOption['stockOnHand'],
                'image' => $productOption['image'],

                't1ResellerAUD' => $productOption['priceColumns']['vipPrice'],
                't2WholesaleAUD' => $productOption['priceColumns']['vipPrice'],
                't3RetailAUD' => $productOption['priceColumns']['vipPrice'],
                'costAUD' => $productOption['priceColumns']['costAUD'],
                'costUSDUSD' => $productOption['priceColumns']['costUSDUSD'],
                'costEUROEUR' => $productOption['priceColumns']['costEUROEUR']
            ];

            if (isset($productData['optionLabel1'])) {
                $productOptions['option1'] = $productOption['option1'];
            }
            if (isset($productData['optionLabel2'])) {
                $productOptions['option2'] = $productOption['option2'];
            }
            if (isset($productData['optionLabel3'])) {
                $productOptions['option3'] = $productOption['option3'];
            }

            $productData['productOptions'][] = $productOptions;
        }


        return $productData;
    }
}