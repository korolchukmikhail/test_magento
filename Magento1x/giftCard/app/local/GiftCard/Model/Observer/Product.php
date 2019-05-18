<?php

class PublicDesire_GiftCard_Model_Observer_Product extends AW_Giftcard2_Model_Observer_Product
{

    public function invoicePay(Varien_Event_Observer $observer)
    {
        $invoice = $observer->getInvoice();

        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->getProductType() == AW_Giftcard2_Model_Product_Type_Giftcard::TYPE_CODE) {
                $options = $orderItem->getProductOptions();
                $giftCardProductOptions = $this->_getGiftCardProductOptions($orderItem);
                $qty = $this->_getQtyToCreate($orderItem);
                $expireAt = null;
                $createdCodes = array();

                while ($qty-- > 0) {
                    try {
                        $giftCard = $this->_createGiftCard(
                            $invoice->getOrder(),
                            $orderItem->getProductId(),
                            $giftCardProductOptions
                        );

                        $createdCodes[] = $giftCard->getCode();

                        if ($giftCard->getOrderId()) {
                            Mage::helper('aw_giftcard2/statistics')->updateStatistics(
                                $giftCard->getProductId(),
                                $giftCard->getOrderId(),
                                $invoice->getStoreId(),
                                array (
                                    'purchased_qty'     => 1,
                                    'purchased_amount'  => $giftCard->getBalance()
                                )
                            );
                        }
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }

                $createdCodesKey = AW_Giftcard2_Model_Product_Type_Giftcard::ORDER_ITEM_CODE_CREATED_CODES;
                $giftCardProductOptions[$createdCodesKey] =
                    isset($giftCardProductOptions[$createdCodesKey]) ?
                        array_merge($giftCardProductOptions[$createdCodesKey], $createdCodes) :
                        $createdCodes
                ;
                $options[$orderItem->getProduct()->getTypeInstance()->getCustomOptionsCode()] = $giftCardProductOptions;
                $orderItem
                    ->setProductOptions($options)
                    ->save()
                ;
            }
        }
        return $this;
    }

    protected function _createGiftCard($order, $productId, $giftCardProductOptions)
    {
        $senderName = '';
        if (isset($giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_SENDER_NAME])) {
            $senderName = $giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_SENDER_NAME];
        }
        $senderEmail = '';
        if (isset($giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_SENDER_EMAIL])) {
            $senderEmail = $giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_SENDER_EMAIL];
        }
        $recipientName = '';
        if (isset($giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_RECIPIENT_NAME])) {
            $recipientName = $giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_RECIPIENT_NAME];
        }
        $recipientEmail = '';
        if (isset($giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_RECIPIENT_EMAIL])) {
            $recipientEmail = $giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_RECIPIENT_EMAIL];
        }
        $emailTemplate = AW_Giftcard2_Model_Source_Giftcard_Email_Template::DO_NOT_SEND_VALUE;
        if (isset($giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_EMAIL_TEMPLATE])) {
            $emailTemplate = $giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_EMAIL_TEMPLATE];
        }
        $deliveryDate = null;
        if (isset($giftCardProductOptions[PublicDesire_GiftCard_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_DELIVERY_DATE])) {
            $deliveryDate = $giftCardProductOptions[PublicDesire_GiftCard_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_DELIVERY_DATE];
        }

        $giftCard = Mage::getModel('aw_giftcard2/giftcard');
        $giftCard
            ->setType($giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::ATTRIBUTE_CODE_TYPE])
            ->setWebsiteId($order->getStore()->getWebsiteId())
            ->setExpireAfter($giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::ATTRIBUTE_CODE_EXPIRE])
            ->setInitialBalance($giftCardProductOptions[AW_Giftcard2_Model_Product_Type_Giftcard::BUY_REQUEST_ATTR_CODE_AMOUNT])
            ->setSenderName($senderName)
            ->setSenderEmail($senderEmail)
            ->setRecipientName($recipientName)
            ->setRecipientEmail($recipientEmail)
            ->setEmailTemplate($emailTemplate)
            ->setOrder($order)
            ->setOrderId($order->getId())
            ->setProductId($productId)
            ->setDeliveryDate($deliveryDate)
            ->save()
        ;
        return $giftCard;
    }

}