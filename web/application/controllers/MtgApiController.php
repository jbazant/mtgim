<?php

require_once('Baz/Controller/JsonAction.php');

/**
 * Radic implementujici JSON server, ktery umoznuje treti strane dotaz na cenu karty
 * Class MtgApiController
 */
class MtgApiController extends Baz_Controller_JsonAction {

    /**
     * Cislo HTTP kodu odpovedi
     * @var int
     */
    protected $_response_code = 200;


    /**
     * Akce pro ziskani vypisu implementovanych obchodu
     * @return array
     */
    public function shopsAvailableAction() {
        $isTest = Zend_Registry::get('config')->mtgim->isTest;

        $this->_response_data = array(
            'data' => Application_Model_Factory::getAvailableModels($isTest),
        );
    }


    /**
     * Akce pro nalezeni ceny karty - odpovida vyhledavani karty na webu
     */
    public function findPriceAction() {
        // --- nacteni vstupnich parametru ---
        $accKey = $this->_request_data['AccessKey'];
        $acc = $this->_getAccData($accKey);

        // overim, ze shop je validni i kdyz pouziju fake
        if (array_key_exists('Shop', $this->_request_data)) {
            if (!array_key_exists($this->_request_data['Shop'], Application_Model_Factory::getAvailableModels())) {
                return $this->_invalidResponse('Invalid Shop specified');
            }

            // pokud je ucet testovaci, pouzivam fake adapter nezavisle na tom, co specifikoval
            if ($acc['isTest']) {
                $shop = 'fake';
                $isTest = 1;
            } else {
                $shop = $this->_request_data['Shop'];
                $isTest = Zend_Registry::get('config')->mtgim->isTest;
            }
        }
        else {
            return $this->_invalidResponse('Missing Shop');
        }

        if (array_key_exists('FoilType', $this->_request_data)) {
            $foil = $this->_request_data['FoilType'];
            if (!in_array($foil, array('A', 'R', 'F'))) {
                return $this->_invalidResponse('Invalid FoilType specified');

            }
        }
        else {
            return $this->_invalidResponse('Missing FoilType');
        }

        if (array_key_exists('CardName', $this->_request_data)) {
            $name = $this->_request_data['CardName'];
        }
        else {
            return $this->_invalidResponse('Missing CardName');
        }

        // --- overeni podpisu ---
        // zde opravdu musi byt request data shop, protoze podpis se overuje z toho, co zaslal uzivatel
        // ne z toho co mu podstrcim ja
        if (
            !array_key_exists('Signature', $this->_request_data)
            || $this->_getFindPriceSignature($acc, $name, $this->_request_data['Shop'], $foil) != $this->_request_data['Signature']
        ) {
            return $this->_invalidResponse('Invalid Signature', 401);
        }

        // --- inicializace a pouziti adapteru ---
        $adapter = Application_Model_Factory::getModel($shop, $foil, $isTest);

        try {
            $adapter->doCardRequest($name);
            $data = array(
                'data' => $adapter->getData(),
            );
        }
        catch (Exception $e) {
            return $this->_invalidResponse('Error communicating with shop', 500);
        }

        // --- odpoved ---
        $this->_response_data = $data;
    }


    /**
     * Pokud uzivatel specifikoval neplatnou metodu, obslouzim ho zde
     */
    public function invalidMethodAction() {
        $this->_invalidResponse('Invalid method', 404);
    }


    /**
     * Pre dispatch resi smerovani neplatnych akci a zakladni validaci vstupu
     */
    public function preDispatch() {
        $actionName = $this->getRequest()->getActionName();
        if ('denied' != $actionName && !preg_match('/^invalid/', $actionName)) {
            $body = $this->getRequest()->getRawBody();
            try {
                $data = Zend_Json::decode($body);
            }
            catch (Exception $e) {
                $this->_forward('invalid-params');
                return;
            }

            // pokud na to neexistuje akce nebo akci vubec nespecifikoval presmeruji na chybovou akci
            if (!in_array($actionName, $this->_getActions())) {
                $this->_forward('invalid-method');
            }

            elseif (!$this->_isValidToken($data['AccessKey'])) {
                $this->_forward('denied');
            }

            else {
                $this->_request_data = $data;
            }
        }
    }


    /**
     * Vrati vsechny akce radice
     * @return array
     */
    private function _getActions() {
        return array('shops-available', 'find-price');

        //todo predelat
        $methods = get_class_methods($this);

        $out = array();
        foreach ($methods as $method) {
            if (preg_match('/^(.+)Action$/', $method, $matches)) {
                $out[] = $matches[1];
            }
        }

        return $out;
    }


    /**
     * Vrati udaje o uctu specifikovanem pres AccessKey
     * @param string $token AccessKey token
     * @return null
     */
    protected function _getAccData($token) {
        // data o uctech
        // pozdeji presunout do DB
        $accounts = array(
            'testKey' => array(
                'accessKey' => 'testKey',
                'secret' => 'testSecret',
                'isTest' => TRUE,
                'dayLimit' => '100',
                'active' => TRUE,
            ),
        );

        // vyhodnoceni validity uctu
        if (array_key_exists($token, $accounts) && $accounts[$token]['active']) {
            return $accounts[$token];
        }
        else {
            return NULL;
        }
    }


    /**
     * Urci zda je dany AccessKey validni
     * @param $token
     * @return bool
     */
    protected function _isValidToken($token) {
        return NULL !== $this->_getAccData($token);
    }


    /**
     * Metoda pro sestaveni signature metody find-price.
     * @param array $accData
     * @param string $cardName
     * @param string $shop
     * @param string $foilType
     * @return string sestaveny sha1 hash
     */
    protected function _getFindPriceSignature($accData, $cardName, $shop, $foilType) {
        return sha1(
              $accData['accessKey']
            . $accData['secret']
            . 'find-price'
            . $cardName
            . $shop
            . $foilType
        );
    }
}
