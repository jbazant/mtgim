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


    public function testAvailableAdapters() {
        // production only adapters
        $this->assertEquals(
            Application_Model_Factory::getAvailableModels(0),
            array(
                'cernyrytir' => 'Černý Rytíř',
                'mystic' => 'Mystic Shop',
                'najada' => 'Najáda',
                'rishada' => 'Rishada',
            )
        );

        // testing adapters
        $this->assertEquals(
            Application_Model_Factory::getAvailableModels(1),
            array(
                'cernyrytir' => 'Černý Rytíř',
                'mystic' => 'Mystic Shop',
                'najada' => 'Najáda',
                'rishada' => 'Rishada',
                'fake' => 'Fake Adapter',
                'fake_rishada' => 'Fake Rishada',
                'fake_cr' => 'Fake Černý Rytíř',
            )
        );

        // adapters without conditional parameter
        $this->assertEquals(
            Application_Model_Factory::getAvailableModels(),
            array(
                'cernyrytir' => 'Černý Rytíř',
                'mystic' => 'Mystic Shop',
                'najada' => 'Najáda',
                'rishada' => 'Rishada',
            )
        );
    }

    //todo test na foil a regular

    //todo test na lokalni adaptery
}