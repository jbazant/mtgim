<?php

/**
 * Class IndexController
 * Zakladni stranky aplikace
 */
class SearchController extends Zend_Controller_Action {

    /**
     * Inicializace kontextu
     */
    public function init() {
        $this->_helper->contextSwitch()
            ->addActionContext('basic', 'json')
            ->initContext()
        ;
    }

    /**
     * Zakladni akce vyhledavani v obchode
     */
    public function basicAction() {
        $isTest = Zend_Registry::get('config')->mtgim->isTest;

        $cardname = trim($this->_request->getParam('cardname'));
        $adapterName = $this->_request->getParam('adapter');
        $foilType = $this->_request->getParam('foil');

        if (!empty($cardname) && !empty($adapterName)) {
            //get adapter
            $adapter = Application_Model_Factory::getModel($adapterName, $foilType, $isTest);
        }

        try {
            if (isset($adapter) && $adapter->doCardRequest($cardname)) {
                $data = $adapter->getData();
                $this->view->success = TRUE;
                $this->view->total = count($data);
                $this->view->results = $data;
            }
            else {
                $this->view->success = FALSE;
                $this->view->reason = 'NO_VALID_DATA';
            }
        }
        catch (Exception $e) {
            $this->view->success = FALSE;
            $this->view->reason = 'PARSE_ERROR';
            if (1 == $isTest) {
                $this->view->message = $e->getMessage();
                $this->view->trace = $e->getTraceAsString();
            }
        }
    }
}

