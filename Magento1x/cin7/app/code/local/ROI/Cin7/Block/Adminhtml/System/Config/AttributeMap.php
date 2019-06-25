<?php

class ROI_Cin7_Block_Adminhtml_System_Config_AttributeMap extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_options = [];
    protected $_template = 'roi_cin7/system/config/attribute_map.phtml';

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        /** @var ROI_Cin7_Model_Attribute $attributeModel */
        $attributeModel = Mage::getModel('roi_cin7/attribute');
        $this->_options = $attributeModel->getMappedOptions();

        return $this->_toHtml();
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    protected function getAttributes()
    {
        /** @var ROI_Cin7_Model_Source_System_Config_Attribute $attributeModel */
        $attributeModel = Mage::getModel('roi_cin7/source_system_config_attribute');
        $attributes = $attributeModel->getAttributes();

        return $attributes;
    }
}