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

        $this->_application->bootstrap();

        /*
         * Fix for ZF-8193
         * http://framework.zend.com/issues/browse/ZF-8193
         * Zend_Controller_Action->getInvokeArg('bootstrap') doesn't work
         * under the unit testing environment.
         */
        $front = Zend_Controller_Front::getInstance();
        if($front->getParam('bootstrap') === null) {
            $front->setParam('bootstrap', $this->_application->getBootstrap());
        }
	}
}