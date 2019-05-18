<?php

namespace Test\Api\Model;

class Total extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    protected $_discountModel;
    protected $_helper;

    public function __construct(
        Discount $discountModel,
        \Test\Api\Helper\Data $helper
    ) {
        $this->_discountModel = $discountModel;
        $this->_helper        = $helper;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect( $quote, $shippingAssignment, $total );

        if ( $this->_helper->canApplyDiscount() ) {
            $label               = __( 'Test Discount' );
            $discountProcent     = ( (float) $this->_discountModel->get( $quote->getStoreId() ) ) / 100;
            $discountAmount      = - $total->getSubtotal() * $discountProcent;
            $appliedCartDiscount = 0;
            if ( $total->getDiscountAmount() ) {
                $appliedCartDiscount = $total->getDiscountAmount();
                $discountAmount      = $total->getDiscountAmount() + $discountAmount;
                if ( $total->getDiscountDescription() ) {
                    $label = $total->getDiscountDescription() . ', ' . $label;
                }
            }

            $total->setDiscountDescription( $label );
            $total->setDiscountAmount( $discountAmount );
            $total->setBaseDiscountAmount( $discountAmount );
            $total->setSubtotalWithDiscount( $total->getSubtotal() + $discountAmount );
            $total->setBaseSubtotalWithDiscount( $total->getBaseSubtotal() + $discountAmount );

            if ( isset( $appliedCartDiscount ) ) {
                $total->addTotalAmount( $this->getCode(), $discountAmount - $appliedCartDiscount );
                $total->addBaseTotalAmount( $this->getCode(), $discountAmount - $appliedCartDiscount );
            } else {
                $total->addTotalAmount( $this->getCode(), $discountAmount );
                $total->addBaseTotalAmount( $this->getCode(), $discountAmount );
            }

        }

        return $this;
    }
}