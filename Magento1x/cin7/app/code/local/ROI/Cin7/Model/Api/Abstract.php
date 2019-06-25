<?php

abstract class ROI_Cin7_Model_Api_Abstract
{
    /** @var ROI_Cin7_Model_Api_Adapter_Curl */
    protected $_adapter = null;
    /** @var ROI_Cin7_Helper_Data */
    protected $_helper = null;

    public function __construct()
    {
        $this->_adapter = Mage::getModel('roi_cin7/api_adapter_curl');
        $this->_helper = Mage::helper('roi_cin7');
    }

    /**
     * @param $feedUrl
     * @return mixed
     * @throws Exception
     */
    protected function getData($feedUrl)
    {
        $data = $this->_adapter->getData($feedUrl);

        return $data;
    }
}