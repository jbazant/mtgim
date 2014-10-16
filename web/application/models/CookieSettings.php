<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 16.10.2014
 * Time: 19:51
 */

class Application_Model_CookieSettings {
    /** @var  Zend_Controller_Request_Http */
    protected $_request;

    /** @var  string application version string */
    protected $_appVersion;


    /**
     * @param Zend_Controller_Request_Http $request
     * @param string $version
     */
    public function __construct($request, $version) {
        $this->_request = $request;
        $this->_appVersion = $version;
    }


    /**
     * Tels wheter dialog about cookie usage should be shown
     * @return bool
     */
    public function shouldShowCookieDialog() {
        return 0 == $this->_request->getCookie($this->getCookiesAcceptedCookieName(), 0);
    }


    /**
     * submits that user acccepted usage of cookies
     */
    public function submitCookieDialog() {
        $this->_submitCookie($this->getCookiesAcceptedCookieName(), 1);
    }


    /**
     * Name of cookie used for detection, that user submited dialog about cookie usage
     * @return string
     */
    public function getCookiesAcceptedCookieName() {
        return 'cookies_accepted';
    }


    /**
     * Name of cookie which detects last version of news, that user hide
     * @return string
     */
    public function getNewsCookieName() {
        return 'last_news_version';
    }


    /**
     * Tels whether shows news to user
     * @return bool
     */
    public function shouldShowNews() {
        return $this->_appVersion != $this->_request->getCookie($this->getNewsCookieName());
    }


    /**
     * Sets that user want to hide current news
     */
    public function hideNews() {
        $this->_submitCookie($this->getNewsCookieName(), $this->_appVersion);
    }


    /**
     * cookie setting wrapper
     * @param string $name
     * @param mixed $val
     */
    private function _submitCookie($name, $val) {
        setcookie($name, $val, $this->_getValidTo(), '/');
    }


    /**
     * Returns default time of cookie validity
     * @return int
     */
    private function _getValidTo() {
        // cookies are valid 1 year (365 days)
        return time()+60*60*24*365;
    }
}
