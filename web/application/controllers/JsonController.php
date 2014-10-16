<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 16.10.2014
 * Time: 20:18
 */

require_once('Baz/Controller/JsonAction.php');


/**
 * Class JsonController
 */
final class JsonController extends Baz_Controller_JsonAction {

    /** @var Application_Model_CookieSettings */
    private $_settingsModel;


    /**
     * Akce pro povoleni pouzivani cookies
     */
    public function submitcookiesAction() {
        $this->_getSettingsModel()->submitCookieDialog();
        $this->_response_data = array('result' => 'OK');
    }


    /**
     * Akce pro skryti novinek
     */
    public function hidenewsAction() {
        $this->_getSettingsModel()->hideNews();
        $this->_response_data = array('result' => 'OK');
    }


    /**
     * Getter pro ziskani CookieSettings
     *
     * @return Application_Model_CookieSettings
     * @throws Zend_Exception
     */
    private function _getSettingsModel() {
        $r = $this->_request;
        $c = Zend_Registry::get('config')->mtgim;

        if (empty($this->_settingsModel)) {
            $this->_settingsModel = new Application_Model_CookieSettings($r, $c->version);
        }

        return $this->_settingsModel;
    }
}
