<?php

class ROI_Cin7_Model_Category extends Mage_Core_Model_Abstract
{
    /** @var ROI_Cin7_Helper_Data */
    protected $_helper;
    protected $_cin7NameRootCategory = 'Products Cin7';

    public function __construct()
    {
        parent::__construct();
        $this->_helper = Mage::helper('roi_cin7');
    }

    public function _construct()
    {
        $this->_init('roi_cin7/category');
    }

    public function updateCategories()
    {
        $categories = $this->getNewCategories();
        $this->saveCategories($categories);
        $this->updateMagentoCategories();
    }

    public function updateMagentoCategories()
    {
        $cin7Categories = $this->getAllCin7Categories();

        $rootCin7CategoryId = $this->getRootCategoryIdForCin7();
        if (!$rootCin7CategoryId) {
            $rootCin7CategoryId = $this->createRootCategoryForCin7();
        }

        $rootCin7Category = Mage::getModel('catalog/category')->load($rootCin7CategoryId);
        $categoryTree = $this->createTree($cin7Categories);

        $childrenCategories = $rootCin7Category->getChildrenCategories();

        if ($childrenCategories->getSize() == 0) {
            $this->saveMagentoCategories($categoryTree, $rootCin7CategoryId);
        }

        //TODO::UPDATE EXISTS CATEGORIES
    }

    public function saveMagentoCategories($categoryTree, $parentId)
    {
        foreach ($categoryTree as $category) {
            $parentIdForChild = $this->createCategory($category['name'], $parentId);

            if (!$category['magento_id']) {
                $cin7Category = $this->load($category['id']);
                $cin7Category->setData('magento_id', $parentIdForChild);
                $cin7Category->save();
                $this->unsetData();
            }

            if (count($category['subcategory'])) {
                $this->saveMagentoCategories($category['subcategory'], $parentIdForChild);
            }
        }
    }

    public function getRootCategoryIdForCin7()
    {
        $categoryId = 0;
        $_category = Mage::getResourceModel('catalog/category_collection')
            ->addFieldToFilter('name', $this->_cin7NameRootCategory)
            ->getFirstItem();

        if (is_object($_category)) {
            $categoryId = $_category->getId();
        }

        return $categoryId;

    }

    public function createRootCategoryForCin7()
    {
        return $this->createCategory($this->_cin7NameRootCategory, 2, 'product');
    }

    public function createCategory($name, $parentId, $urlKey = null)
    {
        $category = Mage::getModel('catalog/category');

        $category->setName($name);
        if ($urlKey) {
            $category->setUrlKey($urlKey);
        }
        $category->setIsActive(1);
        $category->setIsAnchor(1);
        $category->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED);

        $parentCategory = Mage::getModel('catalog/category')->load($parentId);
        $category->setPath($parentCategory->getPath());

        $category->save();

        return $category->getId();
    }

    public function getAllCin7Categories()
    {
        $collection = $this->getCollection();
        $items = $collection->getItems();
        $categories = [];


        foreach ($items as $item) {
            $categories[] = [
                'id' => $item->getId(),
                'name' => $item->getData('cin7_name'),
                'image' => $item->getData('cin7_image'),
                'description' => $item->getData('cin7_description'),
                'magento_id' => (int)$item->getData('magento_id'),
                'cin7_id' => (int)$item->getData('cin7_id'),
                'parent_id' => (int)$item->getData('cin7_parentId'),
                'subcategory' => []
            ];
        }

        return $categories;
    }

    public function getMapCin7MagentoIds()
    {
        $categories = $this->getAllCin7Categories();
        $map = [];

        foreach ($categories as $category) {
            $map[$category['cin7_id']] = $category['magento_id'];
        }

        return $map;
    }

    public function createTree(&$categories, $parentId = 0)
    {
        $branch = array();

        foreach ($categories as &$category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->createTree($categories, $category['cin7_id']);
                if ($children) {
                    $category['subcategory'] = $children;
                }

                $branch[$category['id']] = $category;
                //unset($categories[$category['cin7_id']]);
            }
        }

        return $branch;
    }

    public function getNewCategories()
    {
        /** @var ROI_Cin7_Model_Api_Category_Import $model */
        $model = Mage::getModel('roi_cin7/api_category_import');
        $categories = $model->getAllCategories();

        return $categories;
    }

    public function saveCategories($cin7categories)
    {
        foreach ($cin7categories as $cin7category) {
            $this->load($cin7category['id'], 'cin7_id');
            $categoryName = trim(preg_replace('/^(\d+\.\d+\.?\d?)/', '', $cin7category['name']));

            $this->setData('cin7_id', (int)$cin7category['id']);
            $this->setData('cin7_parentId', (int)$cin7category['parentId']);
            $this->setData('cin7_sort', (int)$cin7category['sort']);
            $this->setData('cin7_isActive', (int)$cin7category['isActive']);
            $this->setData('cin7_name', $categoryName);
            $this->setData('cin7_image', $cin7category['image']);
            $this->setData('cin7_description', $cin7category['description']);

            $this->save();

            $this->unsetData();
        }
    }
}