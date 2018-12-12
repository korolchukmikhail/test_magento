<?php

namespace Test\Status\Model;

class Status
{
    protected $_cacheTag = 'test_status_status';
    protected $_eventPrefix = 'test_status_status';
    protected $_customerResource;
    protected $_customerFactory;
    protected $_customerSession;

    const ATTR_CODE = 'custom_status';

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Customer\Model\Session $customerSession

    ) {
        $this->_customerFactory = $customerFactory;
        $this->_customerResource = $customerResource;
        $this->_customerSession = $customerSession;
    }

    public function saveStatus( $status )
    {
        $customerId = $this->_customerSession->getId();

        if ( $customerId ) {
            $customer = $this->_customerFactory->create();
            $this->_customerResource->load( $customer, $customerId );

            $customerData = $customer->getDataModel();
            $customerData->setCustomAttribute( self::ATTR_CODE, $status );

            $customer->updateData( $customerData );

            $this->_customerResource->saveAttribute( $customer, self::ATTR_CODE );
        }
    }

    public function getStatus()
    {
        $status = '';
        $customerId = $this->_customerSession->getId();

        if ( $customerId ) {
            try {
                $customer = $this->_customerSession->getCustomer();

                $status = $customer->getData( 'custom_status' );
            } catch ( \Exception $e ) {
            }

        }

        return $status;
    }
}