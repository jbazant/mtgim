<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 22.9.2014
 * Time: 8:20
 */

require_once(__DIR__ . '/../../api-client/mtgim-api-lib/Adapter/Curl.php');

/**
 * Testy pro tridu Mtgim_Api_Adapter_Curl
 * Class Mtgim_Api_Adapter_CurlTest
 */
class Mtgim_Api_Adapter_CurlTest extends PHPUnit_Framework_TestCase {

    /**
     * Test for valid timeouts
     */
    public function testTimeoutSettingValid() {
        $o = new Mtgim_Api_Adapter_Curl();

        $this->assertEquals(
            $o->getTimeout(),
            30,
            'Default timeout should be 30 seconds'
        );

        // set new timeout
        $o->setTimeout(10);

        $this->assertEquals(
            $o->getTimeout(),
            10,
            'Timeout shold be set to new value'
        );
    }


    /**
     * @dataProvider invalidTimeoutProvider
     * @expectedException Mtg_Api_Exception
     */
    public function testTimeoutInvalid($val) {
        $o = new Mtgim_Api_Adapter_Curl();
        $o->setTimeout($val);
    }


    /**
     * Provider for testTimeoutInvalid
     * @return array
     */
    public function invalidTimeoutProvider() {
        return array(0, -1, NULL, 'NON_INT_VALUE', 1.1);
    }


    /**
     * Test for valid api urls
     */
    public function testApiUrlSettingValid() {
        $o = new Mtgim_Api_Adapter_Curl();

        $this->assertEquals(
            $o->getApiUrl(),
            'http://mtgim.cz/mtg-api/',
            'Default api url should be set'
        );

        // create object with custom URL
        $o = new Mtgim_Api_Adapter_Curl('http://www.mtgim.cz/mtg-api');

        $this->assertEquals(
            $o->getApiUrl(),
            'http://www.mtgim.cz/mtg-api',
            'Api URL should be set to given value'
        );
    }


    /**
     * @dataProvider invalidApiUrlProvider
     * @expectedException Mtg_Api_Exception
     */
    public function testApiUrlInvalid($val) {
        $o = new Mtgim_Api_Adapter_Curl();
        $o->setTimeout($val);
    }


    /**
     * Provider for testApiUrlInvalid
     * @return array
     */
    public function invalidApiUrlProvider() {
        return array(1, NULL, 'NON_URL_VALUE');
    }


    //todo testy pro callMethod
}
 