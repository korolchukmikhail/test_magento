<?php

class PublicDesire_GiftCard_Model_Observer extends Varien_Event_Observer
{
    public function sendMail($event)
    {
        $collection = Mage::getModel('aw_giftcard2/giftcard')->getCollection();
        $collection->addFieldToFilter( 'delivery_date' , [ 'lteq' => date("Y-m-d", time()) ]) ;
        $collection->addFieldToFilter( 'sent' , 0 );
        $collection->load();
        foreach ( $collection->getItems() as $giftCard ) {
            if ( $giftCard->getEmailTemplate() ) {
                if (!$giftCard->isPhysical()) {
                    try {
                        $expiredAt = $giftCard->getExpireAt() ? $giftCard->getExpireAt() : null;
                        $orderItem = Mage::getModel('sales/order')->load( $giftCard->getOrderId() );
                        $giftCardProductOptions = $orderItem->getProductOptionByCode(
                            $orderItem->getProduct()->getTypeInstance()->getCustomOptionsCode()
                        );
                        $data = array_merge(
                            $giftCardProductOptions,
                            array(
                                'store_id' => $orderItem->getStoreId(),
                                'currency_code' => $orderItem->getOrderCurrencyCode(),
                                'balance' => $giftCard->getBalance(),
                                'giftcard_codes' => $giftCard->getCode(),
                                'expire_at' => $expiredAt
                            )
                        );
                        Mage::getModel('aw_giftcard2/email_template')->prepareEmailAndSend($data);

                        Mage::log(Mage::helper('aw_giftcard2')->__('Email was successfully sent ') . $giftCard->getRecipientEmail(), Zend_Log::INFO);
                        $giftCard->setSent(1);
                        $giftCard->save();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }
        }
    }
}