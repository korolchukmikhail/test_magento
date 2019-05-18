<?php

namespace Test\Api\Model\Api;

class Noveogroup
{
    protected $_helper;

    public function __construct(
        \Test\Api\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function getDiscount( $storeId = 0 )
    {
        $data = $this->getData( $storeId );
        if ( ! $this->isJson( $data ) ) {
            return 0;
        }

        $data = json_decode( $data );

        if ( isset( $data->discount ) ) {
            return $data->discount;
        }

        return 0;
    }

    protected function getData( $storeId )
    {
        $apiUrl = $this->_helper->getGeneralConfig( 'api_url', $storeId );

        $ctx = stream_context_create( [
            'http' =>
                [
                    'timeout' => 15
                ]
        ] );

        $data = @file_get_contents( $apiUrl, false, $ctx );

        return $data;
    }

    private function isJson( $str )
    {
        json_decode( $str );

        return ( json_last_error() == JSON_ERROR_NONE );
    }
}