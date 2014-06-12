<?php
require_once('Baz/Http/ShopPost.php');

final class Application_Model_MysticShop extends Baz_Http_ShopPost
{
	protected $_url = 'http://mysticshop.cz/mtgshop.php'; 
	
	protected function _getParams($cardName)
	{
		return array(
			'artist' =>	null,
			'cmdsearch' =>	'Vyhledej',
			'language' =>	0,
			'limit' =>	50,
			'manacost' => null,
			'maxprice' => null,
			'minprice' => null,
			'name' =>	$cardName,
			'power' => null,
			'power-relation' =>	'>',
			'set' => 0,
			'text' => null,
			'toughness' => null,
			'toughness-relation' =>	'>',
			'typetext' => null,
		);
	}
	
	protected function _translateRarity($r)
	{
		$map = array(
			'M' => 'Mythic',
			'R' => 'Rare',
			'U' => 'Uncommon',
			'C' => 'Common',
			'S' => 'Special',
		);
		if (array_key_exists($r, $map)) {
			return $map[$r];
		} else {
			throw new Zend_Exception('Invalid argument (' . $r . ')specified for rarity.');
		}
	}

	protected function _extractQuality($q)
	{
		if (preg_match_all('/>([^<]+)</', $q, $matches)) {
		
			$ret = array();
			foreach ($matches[1] as $item) {
				$val = trim($item, " \t,.");
				
				if ('mint' == $val) {
					$ret[] = 'nearmint';
				} elseif ('' != $val) {
					$ret[] = $val;
				}
			}
			return implode (', ', $ret);
			
		} else {
			throw new Zend_Exception('Invalid argument (' . $q . ') specified for quality');
		}
	}
	
	protected function _processData($rowData)
	{
		$dataPosition = strpos($rowData, 'Název karty</a>');
		if (!$dataPosition) {
			return false;
		}
		
		$begin = substr($rowData, $dataPosition);
		//get to real data
		$tbody = substr($begin, strpos($begin, '<tbody>'));
		$data = substr($tbody, 0, strpos($tbody, '</tbody>'));
		
		//cleanup
		unset($begin);
		unset($tbody);
		
		//get all results
		$result = array();
		
		while ($start = strpos($data, '<tr')) {
			//parse row
			$data = substr($data, $start + 4);
			$row = substr($data, 4, strpos($data, '</tr>') - 4 );
			$items = explode('</td>', $row);
			
			if (count($items) < 10) {
				//TODO log it!
				return false;
			} else {
				//parse data
				
				//parse name
				$name = explode('>',substr($items[1], 0, -4));
				
				//parse expansion
				preg_match ('/alt="([^"]+)"/i', $items[5], $expansion);
				
				//parse value (cut ,-)
				if (preg_match('/^([0-9]+)/', $this->_cut($items[8]), $matches)) {
					$value = $matches[1];
				} else {
					throw new Zend_Exception('Invalid string supplied for value');
				}
				
				$rarity = $this->_translateRarity(trim($this->_cut($items[4])));
				
				$result[] = array(
					'name' => $name[2],
					//'type' => $this->_cut($items[3]),
					//'rarity' => $rarity,
					'expansion' => $expansion[1],
					'amount' => $this->_cut($items[7]),
					'value' => $value,
					'quality' => $this->_extractQuality($items[6]),
				);
			}
			
			$this->_setData($result);
		}
		
		return true;
	}
}

