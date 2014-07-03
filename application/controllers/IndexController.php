<?php

require_once('Baz/Controller/Action.php');

/**
 * Class IndexController
 * Zakladni stranky aplikace
 */
class IndexController extends Baz_Controller_Action {

    /**
     * Uvodni stranka aplikace
     */
    public function indexAction() {
        $this->view->pageId = 'page-index';

        $this->view->showOldAlert =
            isset($_SERVER['HTTP_REFERER'])
            && FALSE !== strpos($_SERVER['HTTP_REFERER'], 'magic.plasticport.cz')
        ;
    }

    /**
     * Stranka pro vyhledavani
     */
    public function searchAction() {
        $cardname = $this->_request->getParam('cardname');

        if (!empty($cardname)) {
            //get adapter
            //$adapter = Application_Model_Factory::getModel('cernyrytir');
            $this->view->cardname = $cardname;

            $this->view->adapters = array(
                $this->_getSearchResultArr('cernyrytir', 'Černý Rytíř'),
                $this->_getSearchResultArr('mystic', 'Mystic Shop'),
                $this->_getSearchResultArr('najada', 'Najáda'),
            );

            // pouze pokud mam povoleny test, tak zobrazim i fake adapter
            if (Zend_Registry::get('config')->mtgim->isTest == 1) {
                $this->view->adapters[] = $this->_getSearchResultArr('fake', 'Fake Adapter', array(
                    array('foil' => 'basic', 'type' => 'Fake'),
                    array('foil' => 'foil', 'type' => 'Foil'),
                ));
            }
        }
        else {
            $this->getHelper('redirector')->goto('index', 'index');
        }

        $this->view->pageId = 'page-search';
    }


    /**
     * Stranka kontaktniho formulare
     */
    public function contactAction() {

        $request = $this->getRequest();
        $isPost = $request->isPost();

        if ($isPost) {
            $formModel = new Application_Model_Form_Contact($request, Zend_Registry::get('config')->mtgim);
            $this->view->showForm = $formModel->process();
            $this->view->formErrors = $formModel->getErrors();
        }
        else {
            $this->view->showForm = TRUE;
        }


        $this->view->pageId = 'page-contact';
    }

    /**
     * Testovaci akce
     */
    public function testAction() {
        if (Zend_Registry::get('config')->mtgim->isTest == 1) {
            $this->view->pageId = 'page-test';
            $this->view->contactForm = new Application_Model_Form_Contact2();
        }
        else {
            $this->getHelper('redirector')->goto('index', 'index');
        }
    }

    /**
     * Pomocna funkce pro sestaveni pole s konfiguraci vysledku vyhledavani
     *
     * @param string $shopId
     * @param string $shopName
     * @param null|array $adapters
     * @return array
     */
    private function _getSearchResultArr($shopId, $shopName, $adapters = NULL) {
        if (empty($adapters)) {
            $adapters = array(
                array('foil' => 'basic', 'type' => 'Obyčejné'),
                array('foil' => 'foil',  'type' => 'Foily'),
            );
        }

        return array(
            'adapter' => $shopId,
            'name'    => $shopName,
            'types'   => $adapters
        );
    }
}
