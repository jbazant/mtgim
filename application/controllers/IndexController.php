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
                array('adapter' => 'cernyrytir_basic', 'shortName' => 'Obyčejné', 'longName' => 'Pouze obyčejné'),
                array('adapter' => 'cernyrytir_foil', 'shortName' => 'Foily', 'longName' => 'Pouze foily'),
                //array('adapter' => 'mystic', 'shortName' => 'Mystic Shop', 'longName' => 'Mystic-Shop'),
                //array('adapter' => 'najada', 'shortName' => 'Najáda', 'longName' => 'Najáda'),
            );

            // pouze pokud mam povoleny test, tak zobrazim i fake adapter
            if (Zend_Registry::get('config')->mtgim->isTest == 1) {
                $this->view->adapters[] =
                    array('adapter' => 'fake', 'shortName' => 'fake', 'longName' => 'Fake development adapter');
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
        $this->view->pageId = 'page-test';

        $this->view->contactForm = new Application_Model_Form_Contact2();
    }
}
