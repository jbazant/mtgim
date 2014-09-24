<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 18.9.2014
 * Time: 19:14
 */

require_once(__DIR__ . '/../../api-client/mtgim-api-lib/Client.php');

class Mtgim_Api_ClientTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $o = new Mtgim_Api_Client('testKey', 'testHash');

        $this->assertEquals(
            'testKey',
            $o->getAccessKey()
        );

        $this->assertEquals(
            'testHash',
            $o->getSecret()
        );

        $this->assertInstanceOf(
            'Mtgim_Api_Adapter_Curl',
            $o->getAdapter()
        );

        $p = new StdClass();
        $o = new Mtgim_Api_Client('testKey', 'testHash', $p);

        $this->assertEquals(
            'testKey',
            $o->getAccessKey()
        );

        $this->assertEquals(
            'testHash',
            $o->getSecret()
        );

        $this->assertNotInstanceOf(
            'Mtgim_Api_Adapter_Curl',
            $o->getAdapter()
        );

        $this->assertEquals(
            $p,
            $o->getAdapter()
        );
    }


    public function testFindPriceCall() {
        $ret = array('data' => array('test' => 'ok'));

        $stub = $this->getMock('Mtgim_Api_Adapter_Curl');
        $stub->expects($this->once())
            ->method('callMethod')
            ->with(
                $this->equalTo('find-price'),
                $this->equalTo(array(
                    'AccessKey' => 'testKey',
                    'Cardname' => 'Disenchant',
                    'Shop' => 'rishada',
                    'FoilType' => 'R',
                    'Signature' => 'fdfc0f4016a5d24ede15d610a7598c46e0d26a8a',
                ))
            )
            ->willReturn($ret)
        ;

        $o = new Mtgim_Api_Client('testKey', 'testSecret', $stub);

        $this->assertEquals(
            $ret['data'],
            $o->findPrice('Disenchant', 'rishada', 'R')
        );
    }


    public function testShopsAvailableCall() {
        $ret = array('data' => array('test' => 'ok'));

        $stub = $this->getMock('Mtgim_Api_Adapter_Curl');
        $stub->expects($this->once())
            ->method('callMethod')
            ->with(
                $this->equalTo('shops-available'),
                $this->equalTo(array(
                    'AccessKey' => 'testKey',
                ))
            )
            ->willReturn($ret)
        ;

        $o = new Mtgim_Api_Client('testKey', 'testSecret', $stub);

        $this->assertEquals(
            $ret['data'],
            $o->shopsAvailable('Disenchant', 'rishada', 'R')
        );
    }


    public function testTypesAvailable() {
        $o = new Mtgim_Api_Client('testKey', 'testSecret');

        $this->assertEquals(
            array('A' => 'Vše', 'R' => 'obyčejné', 'F' => 'foilové'),
            $o->typesAvailable()
        );
    }

    //todo test na chovani k curl vysledku
}
 