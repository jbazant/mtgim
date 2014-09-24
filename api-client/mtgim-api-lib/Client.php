<?php

require_once(__DIR__ . '/Exception.php');

/**
 * Class Mtg_Api_Client
 */
class Mtgim_Api_Client {

    /** @var  string $_accessKey */
    private $_accessKey;

    /** @var  string $_secret */
    private $_secret;

    /** @var Mtgim_Api_Adapter_Interface */
    private $_adapter;


    /**
     * @param string $accessKey
     * @param string $secret
     * @param NULL|Mtgim_Api_Adapter_Interface $adapter
     * @throws Mtgim_Api_Exception
     */
    public function __construct($accessKey, $secret, $adapter = NULL) {
        // test that all needed extensions are available
        if (!(extension_loaded('sha1'))) {
            throw new Mtgim_Api_Exception('Extension sha1 must by loaded.');
        }

        // set object properties
        $this->_accessKey = $accessKey;
        $this->_secret = $secret;

        if (is_null($adapter)) {
            require_once(__DIR__ . '/Adapter/Curl.php');
            $this->_adapter = new Mtgim_Api_Adapter_Curl();
        }
        else {
            $this->_curlAdapter = $adapter;
        }
    }


    /**
     * Returns access kee specified in constructor
     *
     * @return string
     */
    public function getAccessKey() {
        return $this->_accessKey;
    }


    /**
     * Returns secret specified in constructor
     *
     * @return string
     */
    public function getSecret() {
        return $this->_secret;
    }


    /**
     * Returns API http adapter
     *
     * @return Mtgim_Api_Adapter_Curl
     */
    public function getAdapter() {
        return $this->_adapter;
    }



    /**
     * Call shops-available method on server
     * Returns array of available shops as key-name pairs.
     *
     * @return array
     * @throws Mtgim_Api_Server_Exception
     */
    public function shopsAvailable() {
        return $this->_callApi('shops-available');
    }


    /**
     * Call find-prize method on server.
     *
     * @param string $search Searched word
     * @param string $shopId identified of shop
     * @param string $foilType identifier of type (A/R/F)
     * @throws Mtg_Api_Client_Exception
     * @throws Mtgim_Api_Server_Exception
     * @return array
     */
    public function findPrice($search, $shopId, $foilType) {
        if (empty($search) || empty($shopId) || empty($foilType)) {
            throw new Mtg_Api_Client_Exception('Invalid Params specified!');
        }

        if (!preg_match('/^[FRA]$/', $foilType)) {
            throw new Mtg_Api_Client_Exception('Invalid FoilType specified!');
        }

        return $this->_callApi(
            'find-price',
            array(
                'CardName' => $search,
                'Shop' => $shopId,
                'FoilType' => $foilType,
                'Signature' => $this->_getFindPriceSignature($search, $shopId, $foilType),
            )
        );
    }


    /**
     * Returns array of available card types as key-name pairs.
     * (This method is local, it does not call mtgim api.)
     *
     * @return array
     */
    public function typesAvailable() {
        return array(
            'A' => 'Vše',
            'R' => 'Obyčejné',
            'F' => 'Foilové'
        );
    }


    /**
     * Calls API method on server.
     * On error (invalid response code, invalid data, ...) throws exception
     *
     * @param string $method
     * @param array $params
     * @throws Mtgim_Api_Exception
     * @return array
     */
    protected function _callApi($method, $params = array()) {
        $params['AccessKey'] = $this->_accessKey;

        $res = $this->_adapter->callMethod($method, $params);

        if (array_key_exists('data', $res)) {
           return $res['data'];
        }
        else {
            throw new Mtgim_Api_Server_Exception('Invalid data returned.');
        }
    }


    /**
     * Sestavi podpisovy hash pro metodu find-price
     * @param $cardName
     * @param $shop
     * @param $foilType
     * @return string
     */
    protected function _getFindPriceSignature($cardName, $shop, $foilType) {
        return sha1(
            $this->_accessKey
            . $this->_secret
            . 'find-price'
            . $cardName
            . $shop
            . $foilType
        );
    }
}
