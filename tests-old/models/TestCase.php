<?php

abstract class Model_TestCase 
    extends ControllerTestCase
{
	/**
	 * @var Baz_Http_ShopPost
	 */
	protected $_model;
	
	/**
	 * 
	 * @var Zend_Http_Client_Adapter_Test
	 */
	protected $_adapter;
	
	/**
	 * @var string
	 */
	protected $_targetServer;
	
	protected function setOkResponse($response)
	{
		$this->_adapter->setResponse(
			"HTTP/1.1 200 OK"        . "\r\n" .
    		"Content-type: text/html" . "\r\n" . "\r\n" .
			$response
		);
	}
	
	public function setUp()
	{
		parent::setUp();
		
		$this->_model = Application_Model_Factory::getModel($this->_targetServer);

		$this->_adapter = new Zend_Http_Client_Adapter_Test();
		$this->_model->getHttpClient()->setAdapter($this->_adapter);
	}
}