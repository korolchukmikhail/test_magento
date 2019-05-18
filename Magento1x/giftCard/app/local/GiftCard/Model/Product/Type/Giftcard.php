<?php

class PublicDesire_GiftCard_Model_Product_Type_Giftcard extends AW_Giftcard2_Model_Product_Type_Giftcard
{

    const BUY_REQUEST_ATTR_CODE_DELIVERY_DATE             = 'delivery_date';

    protected $_buyRequestOptionCodes = array(
        self::BUY_REQUEST_ATTR_CODE_AMOUNT,
        self::BUY_REQUEST_ATTR_CODE_CUSTOM_AMOUNT,
        self::BUY_REQUEST_ATTR_CODE_EMAIL_TEMPLATE,
        self::BUY_REQUEST_ATTR_CODE_SENDER_NAME,
        self::BUY_REQUEST_ATTR_CODE_SENDER_EMAIL,
        self::BUY_REQUEST_ATTR_CODE_RECIPIENT_NAME,
        self::BUY_REQUEST_ATTR_CODE_RECIPIENT_EMAIL,
        self::BUY_REQUEST_ATTR_CODE_HEADLINE,
        self::BUY_REQUEST_ATTR_CODE_DELIVERY_DATE,
        self::BUY_REQUEST_ATTR_CODE_MESSAGE
    );

    protected function _addGiftcardOptionsFromBuyRequest($product, $buyRequest, $amount)
    {
        $giftcardOptions = array();

        if (isset($amount)) {
            $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_AMOUNT] = $amount;
        }

        $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_SENDER_NAME] =
            $buyRequest->getData(self::BUY_REQUEST_ATTR_CODE_SENDER_NAME);

        $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_RECIPIENT_NAME] =
            $buyRequest->getData(self::BUY_REQUEST_ATTR_CODE_RECIPIENT_NAME);

        if (!$this->isPhysicalGCType($product)) {

            $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_SENDER_EMAIL] =
                $buyRequest->getData(self::BUY_REQUEST_ATTR_CODE_SENDER_EMAIL);

            $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_RECIPIENT_EMAIL] =
                $buyRequest->getData(self::BUY_REQUEST_ATTR_CODE_RECIPIENT_EMAIL);

            $emailTemplateId = $buyRequest->getData(self::BUY_REQUEST_ATTR_CODE_EMAIL_TEMPLATE);
            $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_EMAIL_TEMPLATE] = $emailTemplateId;

            $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_EMAIL_TEMPLATE_NAME] =
                Mage::getModel('aw_giftcard2/source_giftcard_email_template')->getOptionText($emailTemplateId);
        }

        if ($product->getData(self::ATTRIBUTE_CODE_ALLOW_MESSAGE)) {

            $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_HEADLINE] =
                $buyRequest->getData(self::BUY_REQUEST_ATTR_CODE_HEADLINE);

            $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_MESSAGE] =
                $buyRequest->getData(self::BUY_REQUEST_ATTR_CODE_MESSAGE);
        }

        $giftcardOptions[self::BUY_REQUEST_ATTR_CODE_DELIVERY_DATE] =
            $buyRequest->getData(self::BUY_REQUEST_ATTR_CODE_DELIVERY_DATE);

        $product->addCustomOption($this->getCustomOptionsCode(), serialize($giftcardOptions), $product);

        return $this;
    }
}
