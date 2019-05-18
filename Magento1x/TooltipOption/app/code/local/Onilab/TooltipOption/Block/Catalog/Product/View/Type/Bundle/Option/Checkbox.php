<?php

class Onilab_TooltipOption_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox
{

    public function __construct()
    {
        parent::__construct();
        $this->setSkipGenerateContent(true);
        $this->setTemplate('onilab/tooltipoption/bundle/catalog/product/view/type/bundle/option/checkbox.phtml');
    }

    public function getTooltip($option_id){
        $model = Mage::getModel('tooltipoption/tooltip');
        $store_id = Mage::app()->getStore()->getStoreId();
        $tooltip_content = '';
        $tooltip_content_default = '';

        $collection = $model->getCollection()
            ->addFieldToFilter('option_id', $option_id);
        $tooltips = $collection->toArray();
        if(!empty($tooltips['items'])){
            foreach($tooltips['items'] as $item){
                if((int)$item['store_id'] == $store_id){
                    $tooltip_content = $item['tooltip'];
                    break;
                }elseif($item['store_id'] === "0"){
                    $tooltip_content_default = $item['tooltip'];
                }
            }

            if(!$tooltip_content){
                $tooltip_content = $tooltip_content_default;
            }
        }
        return $tooltip_content;
    }
}