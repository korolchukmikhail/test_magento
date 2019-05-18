<?php

class PublicDesire_GiftCard_Block_Product_View_Type_Giftcard extends AW_Giftcard2_Block_Product_View_Type_Giftcard
{
    public function getTemplateOptions()
    {
        $templateOptions = $this->getProduct()->getTypeInstance()->getTemplateOptions(
            $this->getProduct()
        );
        foreach ($templateOptions as $key => $option) {
            $templateOptions[$key]['template_name'] =
                Mage::getModel('aw_giftcard2/source_entity_attribute_giftcard_email_template')->getOptionText($option['template']);

            if ($option['image']) {
                $templateOptions[$key]['image_url'] =
                    AW_Giftcard2_Helper_Image::resizeImage(
                        $option['image'],
                        210,
                        148
                    );
            }
        };
        return $templateOptions;
    }
}