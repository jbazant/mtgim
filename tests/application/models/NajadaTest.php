<?php


class Model_NajadaTest 
	extends Model_TestCase
{

	protected $_targetServer = 'najada';
	
	public function testDataSize()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/najada1.html'));
		
		$this->assertTrue($this->_model->doCardRequest('iona'));
		$data = $this->_model->getData();
		
		$this->assertType('array', $data);
		$this->assertEquals(27, count($data));
	}
	
	public function testDataRowSimple()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/najada1.html'));
		
		$this->assertTrue($this->_model->doCardRequest('iona'), 'card request');
		$data = $this->_model->getData();
		
		$this->assertEquals(
			array(
					'name' => 'Benalish Missionary',
					//'color' => 'White',
					'type' => 'Creature',
					'rarity' => 'Common',
					'expansion' => 'Weatherlight',
					'amount' => 1,
					'value' => 3,
					'quality' => 'nearmint',
			),
			$data[0]
		);
	}
	
	public function testDataRowPlayed()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/najada1.html'));
		
		$this->assertTrue($this->_model->doCardRequest('iona'));
		$data = $this->_model->getData();
		$this->assertEquals(
			array(
					'name' => 'Dimensional Breach',
					//'color' => 'Land',
					'type' => 'Sorcery',
					'rarity' => 'Rare',
					'expansion' => 'Scourge',
					'amount' => 6,
					'value' => 6,
					'quality' => 'jiné',
			),
			$data[2]
		);
	}
	
	/* //Foil search not supported, cause it must be forced
	 
	public function testDataRowFoil()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/mysticshop1.html'));
		
		$this->assertTrue($this->_model->doCardRequest('iona'));
		$data = $this->_model->getData();
		$this->assertEquals(
			array(
					'name' => 'Cho-Manno, Revolutionary',
					//'color' => 'Multicolored',
					'type' => 'Legendary Creature — Human Rebel (2/2)',
					'rarity' => 'Rare',
					'expansion' => 'Tenth Edition',
					'amount' => 1,
					'value' => 60,
					'quality' => 'nearmint, foil',
			),
			$data[3]
		);
	}
	/**/
} 