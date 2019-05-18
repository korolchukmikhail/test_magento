<?php

class Onilab_TooltipOption_Model_Observer
{
    function SaveTooltipAfterProductSave($observer)
    {
        $model = Mage::getModel('tooltipoption/tooltip'); // loads module's model

        $product = $observer->getEvent()->getProduct(); //get current product

        if($product->getTypeId() == "bundle"){
            //$optionCollection use to get ids of new options.
            //$bundleOption does not contain option_id of new option
            //$optionCollection does not contain module's field values. This we can obtain from $bundleOptions
            $optionCollection = $product->getTypeInstance(TRUE)->getOptionsCollection($product);
            $bundleOptions = $product->getBundleOptionsData(); //@data : array which conatains array
            //get all option data such as title, tooltip, type etc.

            if (!empty($bundleOptions)) {
                $store_id = (int)$product->getData('store_id'); //@data : integer

                foreach ($bundleOptions as $option) {
                    $option_id = (int)$option['option_id'];
                    $tooltip_id = (int)$option['tooltip_id'];

                    //use to set option_id for new options in our module
                    if ($option_id <= 0 || is_null($option_id)) {
                        foreach ($optionCollection as $new) {
                            if ($new['type'] == $option['type'] && ($new['title'] == $option['title'] || $new['default_title'] == $option['title'])) {
                                $option_id = $new['option_id'];
                            }
                        }
                    }

                    $data = array(
                        'option_id' => $option_id,
                        'store_id' => $store_id,
                        'tooltip' => $option['tooltip']
                    );

                    //tooltip id exist means already there is an entry
                    if ($tooltip_id > 0) {
                        $model->load($tooltip_id);
                        $model->addData($data);
                    } else {
                        $model->setData($data);
                    }
                    $model->save();
                }

            }
        }

        return $this;
    }

}