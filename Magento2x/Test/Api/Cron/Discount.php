<?php

namespace Test\Api\Cron;

class Discount
{
    protected $_logger;
    protected $_api;
    protected $_model;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Test\Api\Model\Api\Noveogroup $api,
        \Test\Api\Model\Discount $model
    ) {
        $this->_logger = $logger;
        $this->_api    = $api;
        $this->_model  = $model;
    }

    public function execute()
    {
        $this->_logger->info( 'Start Update Discount Test' );
        $discount = (float) $this->_api->getDiscount();
        $this->_model->set( $discount );
        $this->_logger->info( 'Discount is set to - ' . $discount );
        $this->_logger->info( 'End Update Discount Test' );
    }

}