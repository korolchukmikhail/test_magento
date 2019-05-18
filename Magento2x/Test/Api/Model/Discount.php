<?php

namespace Test\Api\Model;

class Discount
{
    protected $_helper;

    public function __construct(
        \Test\Api\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function get( $storeId = 0 )
    {
        $discount = $this->_helper->getGeneralConfig( 'discount', $storeId );

        return (float) $discount;
    }

    public function set( float $discount, $storeId = 0 )
    {
        $this->_helper->setGeneralConfig( 'discount', $discount, $storeId );
    }

}