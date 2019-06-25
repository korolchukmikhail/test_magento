<?php


class ROI_Cin7_Model_Source_System_Config_Attribute_Group
{
    public function toOptionArray()
    {
        $groups = [];
        /** @var Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection $collectionAttributesSets */
        $collectionAttributesSets = Mage::getResourceModel('eav/entity_attribute_set_collection');
        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getId();
        $collectionAttributesSets->setEntityTypeFilter($entityTypeId);
        $attributesSets = $collectionAttributesSets->getItems();

        /** @var Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection $collectionAttributeGroups */
        $collectionAttributeGroups = Mage::getResourceModel('eav/entity_attribute_group_collection');
        $groups[] = [
            'value' => '', 'name' => ''
        ];

        foreach ($attributesSets as $attributesSet) {
            $collectionAttributeGroups->setAttributeSetFilter($attributesSet->getId());

            foreach ($collectionAttributeGroups as $attributeGroup) {
                $groups[] = [
                    'value' => $attributeGroup->getId(),
                    'label' => $attributeGroup->getAttributeGroupName() . ' (' . $attributesSet->getAttributeSetName() . ')'
                ];
            }

            $collectionAttributeGroups->clear();
            $collectionAttributeGroups->getSelect()->reset(\Zend_Db_Select::WHERE);
        }

        return $groups;
    }
}