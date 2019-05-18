<?php

class PublicDesire_GiftCard_Model_Giftcard extends AW_Giftcard2_Model_Giftcard
{
    protected $_eventPrefix = 'aw_giftcard';

    protected function _beforeSave()
    {
        $_result = parent::_beforeSave();

        if ($_result->getDeliveryDate()) {
            $DeliveryDate = new Zend_Date(
                $this->getDeliveryDate(),
                Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
            );
            $_result->setDeliveryDate($DeliveryDate->toString(Varien_Date::DATE_INTERNAL_FORMAT));
            if($_result->getDeliveryDate() != $_result->getOrigData('delivery_date')){
                $_result->setSent(0);
            }
        }

        return $_result;
    }
}
