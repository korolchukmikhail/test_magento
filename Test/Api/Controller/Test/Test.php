<?php

namespace Test\Api\Controller\Test;

use Magento\Store\Model\ScopeInterface;

class Test extends \Magento\Framework\App\Action\Action
{
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;

        return parent::__construct( $context );
    }

    public function execute()
    {
        echo $this->scopeConfig->getValue( 'testapi_settings/general/discount', ScopeInterface::SCOPE_STORE, 0 );
        exit;
    }
}