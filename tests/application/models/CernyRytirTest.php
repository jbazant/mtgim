<?php


class Model_CerbynytirTest 
	extends Model_TestCase
{

	protected $_targetServer = 'cernyrytir';
	
	public function testDataSize()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/cernyrytir1.html'));
		
		$this->assertTrue($this->_model->doCardRequest('iona'));
		$data = $this->_model->getData();
		
		$this->assertType('array', $data);
		$this->assertEquals(25, count($data));
	}
	
	public function testDataRowSimple()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/cernyrytir1.html'));
		
		$this->assertTrue($this->_model->doCardRequest('iona'));
		$data = $this->_model->getData();
		
		//TODO tohle je soucasny, ne pozadovany vysledek
		$this->assertEquals(
			array(
					'name' => 'Benalish Missionary',
					//'color' => 'White',
					'type' => 'Creature - Cleric',
					'rarity' => 'Common',
					'expansion' => 'Weatherlight',
					'amount' => 11,
					'value' => 5,
					'quality' => 'nearmint',
			),
			$data[0]
		);
	}
	
	public function testDataRowPlayed()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/cernyrytir2.html'));
		
		$this->assertTrue($this->_model->doCardRequest('Badlands'));
		$data = $this->_model->getData();
		$this->assertEquals(
			array(
					'name' => 'Badlands',
					//'color' => 'Land',
					'type' => 'Land - Mountain Swamp',
					'rarity' => 'Rare',
					'expansion' => '3rd-7th Edition',
					'amount' => 2,
					'value' => 870,
					'quality' => 'played',
			),
			$data[1]
		);
	}
	
	public function testDataRowFoil()
	{
		$this->setOkResponse(file_get_contents(APPLICATION_PATH . '/../tests/testsdata/cernyrytir3.html'));
		
		$this->assertTrue($this->_model->doCardRequest('Bant Sojourners'));
		$data = $this->_model->getData();
		$this->assertEquals(
			array(
					'name' => 'Bant Sojourners',
					//'color' => 'Multicolored',
					'type' => 'Creature — Human Soldier',
					'rarity' => 'Common',
					'expansion' => 'Alara Reborn',
					'amount' => 3,
					'value' => 7,
					'quality' => 'foil',
			),
			$data[1]
		);
	}
} 