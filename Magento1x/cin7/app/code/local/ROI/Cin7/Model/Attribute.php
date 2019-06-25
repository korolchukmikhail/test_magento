<?php

class ROI_Cin7_Model_Attribute
{
    /** @var ROI_Cin7_Helper_Data */
    protected $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('roi_cin7');
    }

    public function getMappedOptions($store = null)
    {
        $cin7Options = $this->getCin7Options();
        $storedOptions = $this->getStoredOptions($store);

        $options = array_merge($cin7Options, $storedOptions);

        return $options;
    }

    /**
     * @return array
     */
    public function getCin7Options()
    {
        $options = $this->_helper->getCin7OptionsFromFile();
        $formattedOptions = [];
        if ($options) {
            foreach ($options as $optionLabel => $optionProp) {
                $optionCode = $this->_helper->prepareOptionName($optionLabel);

                $formattedOptions[$optionCode] = [
                    'name' => $optionLabel,
                    'code' => $optionCode,
                    'value' => ''
                ];
            }
        }

        return $formattedOptions;
    }

    public function getStoredOptions($store = null)
    {
        $storedAttributes = [];
        if ($storedOptions = $this->_helper->getConfig('roi_cin7config/attribute/map', $store)) {
            $storedOptions = unserialize($storedOptions);

            foreach ($storedOptions as $storedOption) {
                if ($storedOption['value']) {
                    $storedAttributes[$storedOption['value']] = [
                        'name' => $storedOption['name'],
                        'code' => $storedOption['value'],
                        'value' => $storedOption['value']
                    ];
                }
            }
        }

        return $storedAttributes;
    }

    public function updateAttribute($option)
    {
        $optionName = $option['name'];
        $optionCode = $this->_helper->prepareOptionName($optionName);
        $optionValues = $option['values'];
        $entityType = Mage_Catalog_Model_Product::ENTITY;

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeModel */
        $attributeModel = Mage::getModel('eav/config')->getAttribute($entityType, $optionCode);

        if (!$attributeModel->getId()) {
            $attributeModel = $this->createAttribute($optionCode, $optionName, 'select', $entityType);
        }

        if (!empty($optionValues)) {
            $currentValues = $attributeModel->getSource()->getAllOptions(true, true);

            $newValues = array('attribute_id' => $attributeModel->getId());

            for ($i = 0; $i < count($optionValues); $i++) {
                if (!$this->checkExistsOption($currentValues, $optionValues[$i])) {
                    $newValues['value']['option' . $i][0] = $optionValues[$i];
                }
            }

            if (isset($newValues['value']) && count($newValues['value']) > 0) {
                /** @var Mage_Eav_Model_Entity_Setup $setupModel */
                $setupModel = Mage::getModel('eav/entity_setup', 'core_setup');
                $setupModel->addAttributeOption($newValues);
            }
        }

        return [
            'name' => $optionName,
            'value' => $optionCode,
            'code' => $optionCode
        ];
    }

    private function checkExistsOption($currentValues, $newOption)
    {
        if (!empty($currentValues)) {
            foreach ($currentValues as $currentValue) {
                if ($currentValue['label'] == $newOption) {
                    return true;
                }
            }
        }

        return false;
    }

    private function createAttribute($code, $label, $attribute_type, $entityType)
    {
        $_attribute_data = [
            'attribute_code' => $code,
            'is_global' => '1',
            'frontend_input' => $attribute_type,
            'default_value_text' => '',
            'default_value_yesno' => '0',
            'default_value_date' => '',
            'default_value_textarea' => '',
            'is_unique' => '0',
            'is_required' => '0',
            'apply_to' => array('simple', 'grouped', 'bundle', 'configurable'),
            'is_configurable' => '1',
            'is_searchable' => '1',
            'is_visible_in_advanced_search' => '0',
            'is_comparable' => '1',
            'is_filterable' => '1',
            'is_filterable_in_search' => '1',
            'is_used_for_price_rules' => '1',
            'is_wysiwyg_enabled' => '0',
            'is_html_allowed_on_front' => '1',
            'is_visible_on_front' => '1',
            'used_in_product_listing' => '1',
            'used_for_sort_by' => '0',
            'frontend_label' => $label
        ];

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $model */
        $model = Mage::getModel('catalog/resource_eav_attribute');

        if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
            $_attribute_data['backend_type'] = $model->getBackendTypeByInput($_attribute_data['frontend_input']);
        }

        $defaultValueField = $model->getDefaultValueByInput($_attribute_data['frontend_input']);
        if ($defaultValueField) {
            $_attribute_data['default_value'] = $this->getRequest()->getParam($defaultValueField);
        }


        $model->addData($_attribute_data);
        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType($entityType)->getId();
        $model->setEntityTypeId($entityTypeId);
        $model->setIsUserDefined(1);

        $model->save();

        $attributeGroups = $this->_helper->getConfig('roi_cin7config/attribute/group');
        if (strlen($attributeGroups) > 0) {
            /** @var Mage_Eav_Model_Entity_Setup $setupModel */
            $setupModel = Mage::getModel('eav/entity_setup', 'core_setup');
            /** @var Mage_Eav_Model_Entity_Attribute_Group $groupModel */
            $groupModel = Mage::getModel('eav/entity_attribute_group');
            $attributeGroups = explode(',', $attributeGroups);

            foreach ($attributeGroups as $attributeGroupId) {
                $attributeGroup = $groupModel->load($attributeGroupId);

                if ($attributeGroup->getId()) {
                    $attributeSetId = $attributeGroup->getAttributeSetId();
                    $setupModel->addAttributeToSet($entityType, $attributeSetId, $attributeGroupId, $model->getId());
                }
            }
        }

        return $model;
    }
}