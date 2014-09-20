<?php

require_once(__DIR__ . '/Mtgim_Api_Exception.php');

/**
 * Class Mtg_Api_Client
 */
class Mtgim_Api_Client {
    /** @var  string $_accessKey */
    private $_accessKey;

    /** @var  string $_secret */
    private $_secret;

    /** @var int $_timeout CURL calls timeout */
    private $_timeout = 30;

    /** @var string $_apiUrl base URL of service */
    protected $_apiUrl = 'http://mtgim.cz/mtg-api/';


    /**
     * @param string $accessKey
     * @param string $secret
     */
    public function __construct($accessKey, $secret) {
        $this->_accessKey = $accessKey;
        $this->_secret = $secret;

        //TODO overit, ze je k dispozici curl
        //TODO overit, ze je k dispozici sha1
    }

    public function getAccessKey() {
        return $this->_accessKey;
    }

    public function getSecret() {
        return $this->_secret;
    }

    /**
     * Set timeout for curl calls.
     * @param int $timeout Timeout for curl calls in seconds. Should be positive integer.
     * @throws Mtg_Api_Exception
     */
    public function setTimeout($timeout) {
        if (is_int($timeout) && $timeout > 0) {
            $this->_timeout = $timeout;
        }
        else {
            throw new Mtg_Api_Exception('Invalid timeout specified');
        }
    }

    public function getTimeout() {
        return $this->_timeout;
    }

    public function getShopsAvailable() {
        //TODO
    }

    public function findPrize($search, $shopId, $foilType) {
        if (empty($search) || empty($shopId) || empty($foilType)) {
            throw new Mtg_Api_Client_Exception('Invalid Params specified!');
        }

        if (!preg_match('/^[FRA]$/', $foilType)) {
            throw new Mtg_Api_Client_Exception('Invalid FoilType specified!');
        }

        return $this->_callApi(
            'find-prize',
            array(
                'CardName' => $search,
                'Shop' => $shopId,
                'FoilType' => $foilType,
                'Signature' => $this->_getFindPrizeSignature($search, $shopId, $foilType),
            )
        );
    }

    /**
     * todo tohle chci dekomponovatdo samostatneho objektu
     * @param $method
     * @param $params
     * @throws Mtgim_Api_Server_Exception
     */
    private function _callApi($method, $params) {
        $params['AccessKey'] = $this->_accessKey;

        $c = curl_init($this->_apiUrl . '/' . $method);
        curl_setopt_array($c, array(
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_TIMEOUT => $this->_timeout,
            CURLOPT_RETURNTRANSFER => TRUE,
        ));

        $data = curl_exec($c);
        $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        curl_close($c);
        if (FALSE !== $data) {
            try {
                $ret = json_decode($data);
                if (200 != $http_status) {
                    if (!isset($data['error'])) {
                        if (500 == $http_status) {
                            throw new Mtgim_Api_Server_Exception('Invalid response returned (Status ' . $http_status . ')');
                        }
                        else {
                            throw new Mtgim_Api_Exception('Unexpected response returned (Status ' . $http_status . ')');
                        }
                    }
                    if (500 >= $http_status) {
                        throw new Mtgim_Api_Server_Exception($data['error']);
                    }
                    elseif (400 >= $http_status) {
                        throw new Exception($data['error']);
                    }
                    else {
                        throw new Mtgim_Api_Exception($data['error']);
                    }
                }
                return $ret;
            }
            catch (Exception $e) {
                throw new Mtgim_Api_Server_Exception('Invalid response');
            }
        }
        else {

        }
        //TODO
    }


    private function _getFindPrizeSignature($cardName, $shop, $foilType) {
        return sha1(
            $this->_accessKey
            . $this->_secret
            . 'find-prize'
            . $cardName
            . $shop
            . $foilType
        );
    }
}