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
    }


    /**
     * Zakladni inicializace CSS
     * @param Zend_Config $config
     */
    private function _initCss($config) {
        $v = $config->version;
        $i = $this->view;

        if (TRUE || $config->isTest) {
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
    private function _initJs($config) {
        $v = $config->version;
        $i = $this->view;

        $i->headScript()
            ->appendScript('var jsParams = ' . json_encode($i->jsParams) . ';')
        ;

        if (TRUE || $config->isTest) {
            $i->headScript()
                ->appendFile($i->baseUrl('/js/jquery-1.11.0.min.js'))
                ->appendFile($i->baseUrl('/js/tracking.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/searchPage.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/searchResult.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/app.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/init.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/jquery.mobile-1.4.2.min.js'))
            ;
        }
        else {
            $i->headScript()
                ->appendFile($i->baseUrl('/js/jquery-1.11.0.min.js'))
                ->appendFile($i->baseUrl('/js/app.min.js?v=' . $v))
                ->appendFile($i->baseUrl('/js/jquery.mobile-1.4.2.min.js'))
            ;
        }
    }
}
