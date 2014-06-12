<?php

class Model_FactoryTest extends ControllerTestCase
{
    public function setUp()
    {
        /* Setup Routine */
    	parent::setUp(); 
    }

    public function tearDown()
    {
        /* Tear Down Routine */
    }

    /**
     * Test vraceni adapteru pro CR
     */
	public function testCR()
	{
		$this->assertType('Application_Model_CernyRytir', Application_Model_Factory::getModel('cernyrytir'));
	}
	
	/**
	 * Test vraceni adapteru mystic
	 */
	public function testMystic()
	{
		$this->assertType('Application_Model_MysticShop', Application_Model_Factory::getModel('mystic'));
	}
	
	/**
	 * Test vraceni adapteru najada
	 */
	public function testNajada()
	{
		$this->assertType('Application_Model_Najada', Application_Model_Factory::getModel('najada'));
	}
}