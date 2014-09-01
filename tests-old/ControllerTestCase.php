<?php
require_once('Zend/Test/PHPUnit/ControllerTestCase.php');

abstract class ControllerTestCase 
    extends Zend_Test_PHPUnit_ControllerTestCase
{
	/**
	 * 
	 * @var Zend_Application
	 */
	protected $_application;
	
	/**
	 * Set up test
	 */
	public function setUp()
	{
		$this->bootstrap = array($this, 'appBootstrap');
		parent::setUp();
	}
	
	/**
	 * bootstrap application
	 */
	public function appBootstrap()
	{
		$this->_application = 
			new Zend_Application(APPLICATION_ENV,
				APPLICATION_PATH . '/configs/application.ini');
	}
}