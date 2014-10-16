<?php

abstract class Baz_Controller_JsonAction extends Zend_Controller_Action {

    /**
     * Obsahuje JSON data pozadavku jako asociativni PHP pole.
     * Pole je plneno v metode preDispatch
     * @var array
     */
    protected $_request_data = array();

    /**
     * Pole pro ulozeno navratovych hodnot odpovedi.
     * Pole je prevedeno na JSON odpoved v metode postDispatch
     * @var array
     */
    protected $_response_data = array();

    /**
     * Cislo HTTP kodu odpovedi
     * @var int
     */
    protected $_response_code = 404;


    /**
     * Pokusi se rozparsovat telo pozadavku a ulozit jej
     */
    public function preDispatch() {
        $body = $this->getRequest()->getRawBody();
        try {
            $data = Zend_Json::decode($body);
            $this->_request_data = $data;
        }
        catch (Exception $e) {
            $this->_forward('invalid-params');
            return;
        }
    }


    /**
     * Post dispatch resi sestaveni json odpovedi u vsech akci
     */
    public function postDispatch() {
        $this->getResponse()->setHttpResponseCode($this->_response_code);
        $this->_helper->json($this->_response_data);
    }


    /**
     * Pokud uzivatel specifikoval neplatne parametry metody, obslouzim ho zde
     */
    public function invalidParamsAction() {
        $this->_invalidResponse('Invalid params specified', 400);
    }


    /**
     * Pokud uzivatel specifikoval neplatnou pristupove udaje, obslouzim ho zde
     */
    public function deniedAction() {
        $this->_invalidResponse('Invalid AccessKey', 401);
    }


    /**
     * Pomocna funkce pro sestaveni not 200 odpovedi
     * @param string $error popis chyby
     * @param int $code Cislo HTTP odpovedi
     */
    private function _invalidResponse($error, $code = 400) {
        $this->_response_data = array('error' => $error);
        $this->_response_code = $code;
    }

}
