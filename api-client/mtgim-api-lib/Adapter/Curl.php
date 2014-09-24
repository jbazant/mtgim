<?php

require_once(__DIR__ . '/../Exception.php');
require_once(__DIR__ . '/Interface.php');

/**
 * Trida pro obsluhu samotneho curl volani
 * Class Mtgim_Api_Adapter_Curl
 */
class Mtgim_Api_Adapter_Curl implements Mtgim_Api_Adapter_Interface {

    /** @var string $_apiUrl base URL of service */
    protected $_apiUrl;

    /** @var int $_timeout CURL calls timeout */
    private $_timeout = 30;


    /**
     * @param string $apiUrl
     * @throws Mtgim_Api_Exception
     */
    public function __construct($apiUrl = 'http://mtgim.cz/mtg-api/') {

        // test that all needed extensions are available
        if (!extension_loaded('curl')) {
            throw new Mtgim_Api_Exception('Extension curl must by loaded.');
        }

        //todo check whether URL is well formatted
        $this->_apiUrl = $apiUrl;
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


    /**
     * Curl timeout getter
     * @return int
     */
    public function getTimeout() {
        return $this->_timeout;
    }


    /**
     * ApiUrl getter
     * @return string
     */
    public function getApiUrl() {
        return $this->_apiUrl;
    }

    /**
     * Calls curl on server with given params
     *
     * @param string $method Method name
     * @param array $params Method params
     * @return array parsed data from response
     * @throws Mtgim_Api_Client_Exception on client error
     * @throws Mtgim_Api_Server_Exception on server error
     */
    public function callMethod($method, $params) {
        $c = curl_init($this->_getMethodUrl($method));
        curl_setopt_array($c, $this->_getCurlParams($params));

        $data = curl_exec($c);
        $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        curl_close($c);

        return $this->_parseResponse($http_status, $data);
    }


    /**
     * Returns URL of method
     * @param string $method
     * @return string
     */
    protected function _getMethodUrl($method) {
        return $this->_apiUrl . $method;
    }


    /**
     * Returns array of curl params
     * @param array $params
     * @return array
     */
    protected function _getCurlParams($params) {
       return array(
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_TIMEOUT => $this->getTimeout(),
            CURLOPT_RETURNTRANSFER => TRUE,
        );
    }


    /**
     * @param int $http_status
     * @param string $data
     * @throws Mtgim_Api_Client_Exception
     * @throws Mtgim_Api_Server_Exception
     */
    protected function _parseResponse($http_status, $data) {
        if (FALSE !== $data) {
            // we got some data, lets try to parse them
            try {
                $ret = json_decode($data, TRUE);

                // Only 200 http status is ok. Anything else is always error.
                if (200 != $http_status) {
                    if (!isset($data['error'])) {
                        if (500 >= $http_status) {
                            throw new Mtgim_Api_Server_Exception('Invalid response returned (Status ' . $http_status . ')');
                        }
                        else {
                            throw new Mtgim_Api_Exception('Unexpected response returned (Status ' . $http_status . ')');
                        }
                    }
                    // If we know the error, throw that text
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
            // Invalid response format
            catch (Exception $e) {
                throw new Mtgim_Api_Server_Exception('Invalid response');
            }
        }
        elseif ($http_status && 500 >= $http_status) {
            throw new Mtgim_Api_Server_Exception('Unspecified server error.');
        }
        else {
            throw new Mtgim_Api_Client_Exception('Unknown cliend exception.');
        }
    }
}