<?php


class Model_MysticTest 
	extends Model_TestCase
{

	protected $_targetServer = 'mystic';
	
	public function testDataSize()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/mysticshop1.html'));
		
		$this->assertTrue($this->_model->doCardRequest('iona'));
		$data = $this->_model->getData();
		
		$this->assertType('array', $data);
		$this->assertEquals(27, count($data));
	}
	
	public function testDataRowSimple()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/mysticshop1.html'));
		
		$this->assertTrue($this->_model->doCardRequest('iona'));
		$data = $this->_model->getData();
		
		$this->assertEquals(
			array(
					'name' => 'Benalish Missionary',
					//'color' => 'White',
					'type' => 'Creature — Human Cleric (1/1)',
					'rarity' => 'Common',
					'expansion' => 'Weatherlight',
					'amount' => 7,
					'value' => 3,
					'quality' => 'nearmint',
			),
			$data[0]
		);
	}
	
	public function testDataRowPlayed()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/mysticshop2.html'));
		
		$this->assertTrue($this->_model->doCardRequest('Badlands'));
		$data = $this->_model->getData();
		$this->assertEquals(
			array(
					'name' => 'Badlands',
					//'color' => 'Land',
					'type' => 'Land — Mountain Swamp',
					'rarity' => 'Rare',
					'expansion' => 'Revised Edition',
					'amount' => 1,
					'value' => 918,
					'quality' => 'excellent',
			),
			$data[4]
		);
	}
	
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
} 