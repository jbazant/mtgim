<?php

/**
 * Class Baz_Controller_Action
 */
abstract class Baz_Controller_Action extends Zend_Controller_Action {

    /**
     * Obecna inicializace radice
     */
    public function init() {
        $c = Zend_Registry::get('config')->mtgim;

        $appName = $c->applicationName;

        $this->view->appVersion = $c->version;
        $this->view->appName = $appName;
        $this->view->fbAppId = $c->fb->app_id;

        $this->view->jsParams = array(
            'baseUrl' => $this->view->baseUrl(),
            'appName' => $appName,
            'gaCode'  => $c->gaCode,
        );

        $this->_initCss($c);
        $this->_initJs($c);
        $this->_initMeta($c);
        $this->_initOg($c);
        $this->_initAppletouch($c);
    }


    /**
     * Zakladni inicializace CSS
     * @param Zend_Config $config
     */
    protected function _initCss($config) {
        $v = $config->version;
        $i = $this->view;

        if ($config->isTest) {
            // basic stylesheets
            $i->headLink()
                ->appendStylesheet($i->baseUrl('/css/jquery.mobile-1.4.2.min.css'))
                ->appendStylesheet($i->baseUrl('/css/jb.min.css'))
                ->appendStylesheet($i->baseUrl('/css/jquery.mobile.icons.min.css'))
                ->appendStylesheet($i->baseUrl('/css/web.css?v=' . $v))
            ;
        }
        else {
            //todo include real minified version
            $i->headLink()
                ->appendStylesheet($i->baseUrl('/css/app.min.css?v=' . $v))
            ;
        }
    }


    /**
     * Zakladni inicializace JS
     * @param Zend_Config $config
     */
    protected function _initJs($config) {
        $v = $config->version;
        $i = $this->view;

        $i->headScript()
            ->appendScript('var jsParams = ' . json_encode($i->jsParams) . ';')
        ;

        if ($config->isTest) {
            $i->headScript()
                ->appendFile($i->baseUrl('/js/lib/jquery-1.11.0.min.js'))
                ->appendFile($i->baseUrl('/js/src/tracking.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/src/utils/Popup.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/src/utils/CardDetailPopup.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/src/indexPage.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/src/searchPage.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/src/searchResult.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/src/app.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/src/init.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/lib/jquery.mobile-1.4.2.min.js'))
            ;
        }
        else {
            $i->headScript()
                ->appendFile($i->baseUrl('/js/app.min.js?v=' . $v))
            ;
        }
    }

    protected function _initMeta($config) {
        $this->view->meta = array(
            'viewport' => array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1'),
            'author' => array('name' => 'author', 'content' => 'Jiří Bažant'),
            'keywords' => array('name' => 'keywords', 'content' => 'MtGiM, Magic v Mobilu, Vyhledávání cen karet v obchodech, MtG, Magic: the Gathering'),
            'description' => array('name' => 'description', 'content' => 'Vyhledávání karet sběratelské hry Magic: the Gathering v&nbsp;českých internetových obchodech z&nbsp;pohodlí vašeho mobilu.'),
            'robots' => array('name' => 'robots', 'content' => 'index,follow'),
        );
    }

    protected function _initOg($config) {
        $this->view->og = array(
            array('property' => 'og:type', 'content' => 'website'),
            array('property' => 'og:title', 'content' => 'Vyhledávání cen MtG karet z mobilu'),
            array('property' => 'og:description', 'content' => 'Vyhledávání karet sběratelské hry Magic: the Gathering v&nbsp;českých internetových obchodech z&nbsp;pohodlí vašeho mobilu.'),
            array('property' => 'og:locale', 'content' => 'cs_CZ'),
            array('property' => 'og:site_name', 'content' => $config->applicationName),
            array('property' => 'og:url', 'content' => 'http://' . $_SERVER['SERVER_NAME']),
            array('property' => 'og:image', 'content' => 'http://' . $_SERVER['SERVER_NAME'] . $this->view->baseUrl('/images/cards-black-fb.png')),
        );
    }


    protected function _initAppletouch($config) {
        $this->view->apple = array(
            array('size' => ''),
            array('size' => '57'),
            array('size' => '72'),
            array('size' => '76'),
            array('size' => '114'),
            array('size' => '120'),
            array('size' => '144'),
            array('size' => '152'),
        );
    }
}
