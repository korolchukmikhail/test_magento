<?php

namespace Test\Api\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Test\Api\Helper\Data;

class Discount extends Action
{
    protected $_resultJsonFactory;
    protected $_helper;
    protected $_apiModel;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $helper,
        \Test\Api\Model\Api\Noveogroup $apiModel
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_helper            = $helper;
        $this->_apiModel          = $apiModel;
        parent::__construct( $context );
    }

    public function execute()
    {
        $message = null;
        try {
            $message = (float) $this->_apiModel->getDiscount();
            $success = true;
        } catch ( \Exception $e ) {
            $this->_objectManager->get( 'Psr\Log\LoggerInterface' )->critical( $e );
            $success = false;
        }

        $result = $this->_resultJsonFactory->create();

        return $result->setData( [ 'success' => $success, 'message' => $message ] );
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed( 'Test_Api::testapi' );
    }
}