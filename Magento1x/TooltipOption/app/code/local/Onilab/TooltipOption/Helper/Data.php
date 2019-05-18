<?php

class Onilab_TooltipOption_Helper_Data extends  Mage_Core_Helper_Abstract
{
    public function parseTooltipHtml($html){
        $helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        $tooltip = $processor->filter($html);

        return $tooltip;
    }

}