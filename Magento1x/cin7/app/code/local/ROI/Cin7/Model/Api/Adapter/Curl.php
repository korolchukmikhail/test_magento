<?php

class ROI_Cin7_Model_Api_Adapter_Curl
{
    protected $_config;
    private $_curlHandler;

    public function __construct()
    {
        $this->setConfig();
    }

    protected function setConfig()
    {
        /** @var ROI_Cin7_Helper_Data $helper */
        $helper = Mage::helper('roi_cin7');
        $this->_config['base_url'] = $helper->getBaseUrl();
        $this->_config['username'] = $helper->getUsername();
        $this->_config['password'] = $helper->getPassword();
        $this->_config['rows'] = $helper->getCountRows();
        $this->_config['tmp_dir'] = $helper->getTempDir();
    }

    /**
     * @param $feed_url
     * @return mixed
     * @throws Exception
     */
    public function getData($feed_url)
    {
        $curlHandler = $this->getCurlHandler();

        $this->setFeedUrl($feed_url);
        $response = curl_exec($curlHandler);
        $data = json_decode($response, true);

        if (isset($data['message'])) {
            throw new Exception($data['message']);
        }

        sleep(1);

        if (is_string($data)) {
            throw new Exception($data);
        }

        return $data;
    }

    public function setFeedUrl($feed_url)
    {
        curl_setopt($this->_curlHandler, CURLOPT_URL, $this->_config['base_url'] . $feed_url);
    }

    public function getCurlHandler()
    {
        if (!$this->_curlHandler) {
            $this->initCurlHandler();
        }

        return $this->_curlHandler;
    }

    protected function initCurlHandler()
    {
        $this->_curlHandler = curl_init();
        curl_setopt($this->_curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_curlHandler, CURLOPT_HEADER, false);

        curl_setopt($this->_curlHandler, CURLOPT_COOKIEJAR, $this->_config['tmp_dir'] . '/cookies.txt');
        curl_setopt($this->_curlHandler, CURLOPT_COOKIEFILE, $this->_config['tmp_dir'] . '/cookies.txt');

        $credentials = $this->_config['username'] . ":" . $this->_config['password'];
        curl_setopt($this->_curlHandler, CURLOPT_USERPWD, $credentials);
    }

    public function __destruct()
    {
        if ($this->_curlHandler) {
            curl_close($this->_curlHandler);
        }

        unlink($this->_config['tmp_dir'] . '/cookies.txt');
    }
}