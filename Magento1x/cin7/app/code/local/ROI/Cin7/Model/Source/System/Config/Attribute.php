<?php

class ROI_Cin7_Model_Source_System_Config_Attribute
{
    public function getAttributes()
    {
        $preparedAttributes = [];
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $collectionAttributes */
        $collectionAttributes = Mage::getResourceModel('catalog/product_attribute_collection');
        $collectionAttributes->setOrder('frontend_label', 'ASC');

        $attributeGroups = Mage::helper('roi_cin7')->getConfig('roi_cin7config/attribute/group');

        if ($attributeGroups) {
            $attributeGroups = explode(',', $attributeGroups);
            $attributes = [];

            foreach ($attributeGroups as $attributeGroupId) {
                $attributes = array_merge($attributes, $collectionAttributes->setAttributeGroupFilter($attributeGroupId)->getItems());
                $collectionAttributes->clear();
                $collectionAttributes->getSelect()->reset(\Zend_Db_Select::WHERE);
            }

        } else {
            $attributes = $collectionAttributes->getItems();
        }

        $attributesUnique = [];
        foreach ($attributes as $attribute) {
            if (!isset($attributesUnique[$attribute->getAttributeCode()])) {
                if($attribute->getFrontendLabel()){
                    $attributesUnique[$attribute->getAttributeCode()] = $attribute;
                }
            }
        }

        foreach ($attributesUnique as $attribute) {
            $preparedAttributes[] = [
                'code' => $attribute->getAttributeCode(),
                'name' => $attribute->getFrontendLabel()
            ];
        }

        return $preparedAttributes;
    }
}