<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 18.9.2014
 * Time: 19:14
 */

require_once(__DIR__ . '/../mtg-api-lib/Mtgim_Api_Client.php');


class Mtgim_Api_ClientTest extends PHPUnit_Framework_TestCase {
    /** @var  Mtgim_Api_Client */
    protected $_testObject;

    public function setUp() {
        $this->_testObject = new Mtgim_Api_Client('testKey', 'testSecret');
    }

    public function testTimeoutSettingValid() {
        $o = $this->_testObject;

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
     * @shouldThrow Mtg_Api_Exception
     * todo udelat pres data source vsech moznych spatnych hodnot?
     */
    public function testTimeoutInvalid() {
        $this->_testObject->setTimeout(0);
    }

    /**
     * todo overit, ze smi to opravdu vola s predpokladanymi daty
     */
    public function testFindPriceCall() {

    }

    /**
     * todo overit, ze smi to opravdu vola s predpokladanymi daty
     */
    public function testShopsAvailableCall() {

    }

    //todo test na chovani k curl vysledku
}
 