<?php

class PublicDesire_GiftCard_Helper_Options extends AW_Giftcard2_Helper_Options
{
    protected function _getOptions(array $customOptions, $store = null)
    {
        $options = array();

        $type = $this->_getOptionValue(
            $customOptions,
            AW_Giftcard2_Model_Product_Type_Giftcard::ATTRIBUTE_CODE_TYPE
        );
        if ($type) {
            $options[] = array(
                'label' => Mage::helper('aw_giftcard2')->__('Gift Card Type'),
                'value' => $this->escapeHtml(
                    Mage::getModel('aw_giftcard2/source_entity_attribute_giftcard_type')->getOptionText($type)
                )
            );
        }

        $amount = $this->_getOptionValue(
            $customOptions,
            AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_AMOUNT
        );
        if ($amount) {
            $options[] = array(
                'label' => Mage::helper('aw_giftcard2')->__('Gift Card Amount'),
                'value' => $this->escapeHtml(Mage::helper('core')->currencyByStore($amount, $store, true, false))
            );

        }

        $template = $this->_getOptionValue(
            $customOptions,
            AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_EMAIL_TEMPLATE_NAME
        );
        if ($template) {
            $options[] = array(
                'label' => Mage::helper('aw_giftcard2')->__('Gift Card Design'),
                'value' => $this->escapeHtml($template)
            );
        }

        $senderName = $this->_getOptionValue(
            $customOptions,
            AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_SENDER_NAME
        );
        if ($senderName) {
            $senderEmail = $this->_getOptionValue(
                $customOptions,
                AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_SENDER_EMAIL
            );
            $options[] = array(
                'label' => Mage::helper('aw_giftcard2')->__('Gift Card Sender'),
                'value' => $this->escapeHtml($senderEmail ? "{$senderName} &lt;{$senderEmail}&gt;" : $senderName)
            );
        }

        $recipientName = $this->_getOptionValue(
            $customOptions,
            AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_RECIPIENT_NAME
        );
        if ($recipientName) {
            $recipientEmail = $this->_getOptionValue(
                $customOptions,
                AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_RECIPIENT_EMAIL
            );
            $options[] = array(
                'label' => Mage::helper('aw_giftcard2')->__('Gift Card Recipient'),
                'value' => $this->escapeHtml($recipientEmail ? "{$recipientName} &lt;{$recipientEmail}&gt;" : $recipientName)
            );
        }

        $headline = $this->_getOptionValue(
            $customOptions,
            AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_HEADLINE
        );
        if ($headline) {
            $headline = trim($headline);
            $options[] = array(
                'label' => Mage::helper('aw_giftcard2')->__('Gift Card Headline'),
                'value' => $this->escapeHtml($headline)
            );
        }

        $message = $this->_getOptionValue(
            $customOptions,
            AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_MESSAGE
        );
        if ($message) {
            $message = trim($message);
            $options[] = array(
                'label' => Mage::helper('aw_giftcard2')->__('Gift Card Message'),
                'value' => $this->escapeHtml($message)
            );
        }

        $emailSent = $this->_getOptionValue(
            $customOptions,
            AW_Giftcard2_Model_Product_Type_Giftcard::ORDER_ITEM_CODE_EMAIL_SENT
        );
        if ($emailSent) {
            $options[] = array (
                'label' =>  Mage::helper('aw_giftcard2')->__('Email Sent'),
                'value' => $emailSent ?
                    Mage::helper('aw_giftcard2')->__('Yes') :
                    Mage::helper('aw_giftcard2')->__('No')
            );
        }

        $codes = $this->_getOptionValue(
            $customOptions,
            AW_Giftcard2_Model_Product_Type_Giftcard::ORDER_ITEM_CODE_CREATED_CODES
        );
        if ($codes) {
            if (is_array($codes) && count($codes) > 0) {
                $options[] = array(
                    'label' => Mage::helper('aw_giftcard2')->__('Gift Card Codes'),
                    'value' => implode('<br/>', $this->escapeHtml($codes))
                );
            }
        }

        $deliveryDate = $this->_getOptionValue(
            $customOptions,
            PublicDesire_GiftCard_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_DELIVERY_DATE
        );
        if ($deliveryDate) {
            $options[] = array(
                'label' => Mage::helper('aw_giftcard2')->__('Delivery Date'),
                'value' => $this->escapeHtml($deliveryDate)
            );
        }

        return $options;
    }
}