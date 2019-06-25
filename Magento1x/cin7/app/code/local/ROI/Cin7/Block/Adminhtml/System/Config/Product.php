<?php

class ROI_Cin7_Block_Adminhtml_System_Config_Product extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('roicin7/adminhtml_index/importProduct');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Update')
            ->setOnClick("setLocation('$url?ids='+document.getElementById('roi_cin7config_cron_import_product_now_ids').value)")
            ->toHtml();

        return $html;
    }
}