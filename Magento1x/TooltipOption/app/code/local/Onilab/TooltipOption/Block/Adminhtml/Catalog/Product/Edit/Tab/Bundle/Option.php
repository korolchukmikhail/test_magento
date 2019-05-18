<?php

class Onilab_TooltipOption_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option
{

    public function __construct()
    {
        parent::__construct();
        $this->setSkipGenerateContent(true);
        $this->setTemplate('onilab/tooltipoption/edit_tab_bundle_option.phtml');
    }

    public function getOptions()
    {
        if (!$this->_options) {

            $customCollection = Mage::getModel('tooltipoption/tooltip')->getCollection();
            $store_id = $this->getProduct()->getData('store_id');

            //sets default values of tooltip module
            $empty = FALSE;
            if (!count($customCollection)) {
                $empty = TRUE;
            }

            $this->getProduct()->getTypeInstance(true)->setStoreFilter($this->getProduct()->getStoreId(),
                $this->getProduct());

            $optionCollection = $this->getProduct()->getTypeInstance(true)->getOptionsCollection($this->getProduct());

            $selectionCollection = $this->getProduct()->getTypeInstance(true)->getSelectionsCollection(
                $this->getProduct()->getTypeInstance(true)->getOptionsIds($this->getProduct()),
                $this->getProduct()
            );

            $this->_options = $optionCollection->appendSelections($selectionCollection);


            foreach ($this->_options as $option) {
                $option_id = $option->getData('option_id');
                $tooltip_id = 0;
                $tooltip_content = '';
                if ($empty) {
                    $option->addData(array('tooltip_id' => $tooltip_id, 'tooltip' => $tooltip_content));
                } else {
                    foreach ($customCollection as $tooltip) {
                        //finds the entry corresponds to option id, if exist
                        if ($tooltip['option_id'] == $option_id && ($tooltip['store_id'] == $store_id)) {
                            $tooltip_id = (int)$tooltip['tooltip_id'];
                            $tooltip_content = $tooltip['tooltip'];
                            break;
                        }

                    }
                    $option->addData(array('tooltip_id' => $tooltip_id, 'tooltip' => $tooltip_content));
                }
            }


            if ($this->getCanReadPrice() === false) {
                foreach ($this->_options as $option) {
                    if ($option->getSelections()) {
                        foreach ($option->getSelections() as $selection) {
                            $selection->setCanReadPrice($this->getCanReadPrice());
                            $selection->setCanEditPrice($this->getCanEditPrice());
                        }
                    }
                }
            }
        }
        return $this->_options;
    }
}