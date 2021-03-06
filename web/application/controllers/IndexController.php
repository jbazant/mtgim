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
        $r = $this->_request;
        $c = Zend_Registry::get('config')->mtgim;

        $settingsModel = new Application_Model_CookieSettings($r, $c->version, strtotime($c->newsTo));

        // odsouhlaseni pouziti cookies
        if (1 == $r->getParam('cookieAccept', 0)) {
            $settingsModel->submitCookieDialog();
            $this->view->showCookiesInfo = FALSE;
        }
        else {
            $this->view->showCookiesInfo = $settingsModel->shouldShowCookieDialog();
        }

        // skryti aktualnich novinek
        if (1 == $r->getParam('hideNews')) {
            $settingsModel->hideNews();
            $this->view->showNews = FALSE;
        }
        else {
            $this->view->showNews = $settingsModel->shouldShowNews();
        }

        // nastaveni id stranky
        $this->view->pageId = 'page-index';
    }


    /**
     * Stranka pro vyhledavani
     */
    public function searchAction() {
        $cardname = $this->_request->getParam('card');


        if (empty($cardname)) {
            $this->getHelper('redirector')->goto('index', 'index');
        }
        else {
            $this->view->meta['robots']['content'] = 'noindex,nofollow';
            $this->view->meta['keywords']['content'] = 'Magic v mobilu, Výsledky hledání ceny karty ' . htmlspecialchars($cardname);
            $this->view->meta['description']['content'] = 'Výsledky hledání ceny karty \'' . htmlspecialchars($cardname) . '\' v MtGiM.cz';

            //get adapter
            $adapters = array();
            $availableAdapters = Application_Model_Factory::getAvailableModels(Zend_Registry::get('config')->mtgim->isTest);

            foreach ($availableAdapters as $key => $name) {
                $adapters[] = $this->_getSearchResultArr($key, $name);
            }

            $this->view->adapters = $adapters;

            $this->view->pageId = 'page-search';
        }
    }


    /**
     * Akce urcena vyhradne pro vyhledavani pres vyhledavaci pole prohlizece.
     * Jine akce by sem nemely smerovat
     */
    public function findAction() {
        // todo log access
        $cardname = $this->_request->getParam('cardname');
        if (empty($cardname)) {
            $this->getHelper('redirector')->goto('index', 'index');
        }
        else {
            $this->getHelper('redirector')->gotoUrl('/index/search/card/' . rawurlencode($cardname));
        }
    }


    /**
     * Stranka kontaktniho formulare
     */
    public function contactAction() {
        $this->view->meta['keywords']['content'] = 'MtGiM kontakt, napište nám';
        $this->view->meta['description']['content'] = 'Máte připomínky nebo dotazy? Chcete s námi spolupracovat? Kontaktujte nás!';

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
            $contactForm = new Application_Model_Form_Contact2(
                array('mailerSettings' => Zend_Registry::get('config')->mtgim->toArray())
            );
            $contactForm->setAction('/index/test');

            $showForm = TRUE;
            // todo tohle udelat doopravdy - isPost neexistuje
            if ($this->_request->isPost()) {
                // zpracuji formular
                $showForm = !$contactForm->process($_POST);
            }

            $this->view->pageId = 'page-test';
            $this->view->showForm = $showForm;

            if ($showForm) {
                $this->view->contactForm = $contactForm;
            }
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
