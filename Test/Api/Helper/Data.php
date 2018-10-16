<?php

namespace Test\Api\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    const XML_PATH = 'testapi_settings/';
    protected $_configWriter;
    protected $_timezone;

    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        WriterInterface $configWriter
    ) {
        $this->_configWriter = $configWriter;
        $this->_timezone     = $timezone;
        parent::__construct( $context );
    }

    public function canApplyDiscount( $storeId = 0 ): bool
    {
        if ( ! $this->getGeneralConfig( 'enabled', $storeId ) || $this->getGeneralConfig( 'discount', $storeId ) == 0 ) {
            return false;
        }

        $startTime = explode( ',', $this->getGeneralConfig( 'time_from', $storeId ) );
        $endTime   = explode( ',', $this->getGeneralConfig( 'time_till', $storeId ) );
        $startMark = $startTime[0] * 3600 + $startTime[1] * 60 + $startTime[2];
        $endMark   = $endTime[0] * 3600 + $endTime[1] * 60 + $endTime[2];

        if ( $startMark == 0 && $endMark == 0 ) {
            return true;
        }

        if ( $endMark == 0 && $endMark < $startMark ) {
            return false;
        }

        $dateTime         = $this->_timezone->date();
        $currentStoreTime = explode( ':', $dateTime->format( 'H:i:s' ) );
        $currentStoreMark = $currentStoreTime[0] * 3600 + $currentStoreTime[1] * 60 + $currentStoreTime[2];

        if ( $currentStoreMark > $startMark && $currentStoreMark < $endMark ) {
            return true;
        }

        if ( $endMark == 0 && $currentStoreMark > $startMark ) {
            return true;
        }

        return false;
    }

    public function getConfigValue( $field, $storeId = 0 )
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig( $code, $storeId = 0 )
    {
        return $this->getConfigValue( self::XML_PATH . 'general/' . $code, $storeId );
    }

    public function setConfigValue( $field, $value, $storeId = 0 )
    {
        $scope = ScopeInterface::SCOPE_STORE;
        if ( ! $storeId ) {
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        $this->_configWriter->save( $field, $value, $scope, $storeId );
    }

    public function setGeneralConfig( $code, $value, $storeId = 0 )
    {
        $this->setConfigValue( self::XML_PATH . 'general/' . $code, $value, $storeId );
    }

}