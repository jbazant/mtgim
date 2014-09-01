<?php

/**
 * Testy pro tridu Application_Model_Factory
 * Class Model_FactoryTest
 */
class Model_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * setup - include tridy
     */
    public function setUp() {
        require_once(APPLICATION_PATH . '/models/Factory.php');
    }

    /**
     * Test vraceni adapteru pro CR
     * @dataProvider basicProvider
     */
	public function testAdapterBasic($adapterClass, $adapterName) {
        $this->assertInstanceOf($adapterClass, Application_Model_Factory::getModel($adapterName));
	}

    /**
     * Provider pro testAdapterBasic
     * @return array
     */
    public function basicProvider() {
        return array(
            array('Application_Model_CernyRytir', 'cernyrytir'),
            array('Application_Model_MysticShop', 'mystic'),
            array('Application_Model_Najada',     'najada'),
            array('Application_Model_Rishada',    'rishada'),
        );
    }

    //todo test na foil a regular

    //todo test na lokalni adaptery
}