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
            'foil' => $this->_foilType == 'F' ? 1 : null,
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
				
				if (
                    !in_array(strtolower($val), array('mint', 'nm', 'nearmint', ''))
                ) {
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
            // pokud jsem nic nenasel, tak je to ok, pokud jsem dostal nejakou blbost, hlasim chybu
            return (FALSE !== strpos($rowData, 'Podle Vámi zadaných kriterií nebyly nalezeny žádné karty.'));
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
					$value = (int)$matches[1];
				} else {
                    // obcas mysticshop vraci v cene i text, kaslem na to a dame tam 0
                    $value = 0;
					//throw new Zend_Exception('Invalid string supplied for value');
				}
				
				//$rarity = $this->_translateRarity(trim($this->_cut($items[4])));

                $quality = $this->_extractQuality($items[6]);

                if ('R' !== $this->_foilType || FALSE === stristr($quality, 'foil')) {
                    $result[] = array(
                        'name' => $name[2],
                        //'rarity' => $rarity,
                        'expansion' => $expansion[1],
                        'amount' => (int)$this->_cut($items[7]),
                        'value' => $value,
                        'quality' => $this->_extractQuality($items[6]),
                    );
                }
			}
			
			$this->_setData($result);
		}
		
		return true;
	}


    /**
     * Urci, zda-li je dana karta pozadovaneho foil typu
     * @param string $type
     * @return bool
     */
    protected function _isRequestedFoilType($type) {
        if ('A' == $this->_foilType) {
            return TRUE;
        }
        elseif ('R' == $this->_foilType && 'foil' != $type) {
            return TRUE;
        }
        elseif ('F' == $this->_foilType && 'foil' == $type) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}

