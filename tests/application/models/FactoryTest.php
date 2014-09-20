<?php

// todo tohle tu nema co delat
defined('TEST_ROOT_PATH')
    || define('TEST_ROOT_PATH', __DIR__ . '/../../..');
// nastaveni include path, potrebne pro spravne instancovani objektu
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(TEST_ROOT_PATH . '/library'),
    get_include_path(),
)));

class Model_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        require_once(TEST_ROOT_PATH . '/application/models/Factory.php');
    }

    //todo predelat na data provider
    /**
     * Test vraceni adapteru pro CR
     * @dataProvider _basicProvider
     */
	public function testAdapterBasic($adapterClass, $adapterName) {
        $this->assertInstanceOf($adapterClass, Application_Model_Factory::getModel($adapterName));
	}

    public function _basicProvider() {
        return array(
            array('Application_Model_CernyRytir', 'cernyrytir'),
            array('Application_Model_MysticShop', 'mystic'),
            array('Application_Model_Najada',     'najada'),
            array('Application_Model_Rishada',    'rishada'),
        );
    }
}