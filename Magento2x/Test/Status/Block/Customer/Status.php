<?php

namespace Test\Status\Block\Customer;

class Status extends \Magento\Framework\View\Element\Template
{
    protected $_statusFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Test\Status\Model\StatusFactory $statusFactory,
        array $data = []
    ) {
        $this->_statusFactory = $statusFactory;

        parent::__construct( $context, $data );
    }

    public function getStatus()
    {
        $model = $this->_statusFactory->create();

        return $model->getStatus();
    }

    public function getFormAction()
    {
        return $this->getUrl( 'cusstatus/customer/index' );
    }
}