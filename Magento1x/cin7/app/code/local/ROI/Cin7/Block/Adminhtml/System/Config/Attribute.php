<?php

class ROI_Cin7_Block_Adminhtml_System_Config_Attribute extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('roicin7/adminhtml_index/importAttribute');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Update')
            ->setOnClick("setLocation('$url')")
            ->toHtml();

        return $html;
    }
}