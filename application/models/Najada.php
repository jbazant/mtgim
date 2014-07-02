<?php
require_once('Baz/Http/ShopPost.php');

class Application_Model_Najada extends Baz_Http_ShopPost
{
	protected $_url = 'http://www.najada.cz/cz/kusovky-mtg/';

	protected function _getParams($cardName)
	{
        switch($this->_foilType) {
            case 'A':
                $foil = -1;
                break;

            case 'F':
                $foil = 1;
                break;

            case 'R':
                $foil = 0;
                break;

            default:
                throw new Exception ('Unknown foil type ' . $this->_foilType);
        }

		return array(
			'Action' =>	'EShop.Search',
			'Anchor' =>	'EShopSearchArticles',
			'Foil' => $foil,
			'From' => null,
			'MagicCardSet' => -1,
			'Search' =>	$cardName,
			'To' => null,
		);
	}

	protected function _processData($rowData)
	{
		$dataPosition = strpos($rowData, 'id="EShopSearchArticles"');
		
		if (!$dataPosition) {
			return false;
		}
		
		$begin = substr($rowData, $dataPosition);
		//get to real data
		$tbody = substr($begin, strpos($begin, '<table>'));
		$data = substr($tbody, 0, strpos($tbody, '</table>'));
		
		//cleanup
		unset($begin);
		unset($tbody);
		
		//now i want to cut next line
		$data = substr($data, strpos($data, '<tr') + 4);
		
		//get all results
		$result = array();
		$lastLine = false;
		
		while ($start = strpos($data, '<tr')) {
			//parse row
			$data = substr($data, $start + 4);
			$row = substr($data, 4, strpos($data, '</tr>') - 4 );
			$items = explode('</td>', $row);
			
			if (count($items) < 8) {
				//ignore last line (only once)
				if (!$lastLine) {
					$lastLine = true;
					continue;
				} else {
					return false;
				}
				
			} else {
				//parse data
				$name = explode('>',substr(trim($items[0]), 0, -4));
				
				$info = $this->_cut($items[7]);
				preg_match(
					'/.*"Nearmint"[^>]*>[^>]*>([0-9]+)<[^(]*[(][^>]*>([0-9]+)</',
					$info,
					$info_mint
				);
                $info = $this->_cut($items[8]);
				preg_match(
					'/.*"Ostatní"[^>]*>[^>]*>([0-9]+)<[^(]*[(][^>]*>([0-9]+)</',
					$info,
					$info_other
				);
				
				$result[] = array(
					'name' => $name[3],
					//'type' => $this->_cut($items[4]),
					//'rarity' => $this->_cut($items[5]),
					'expansion' => $this->_cut($items[6]),
					'value' => (int)$info_mint[1],
					'amount' => (int)$info_mint[2],
					'quality' => '',
				);

				if (/*isset($info_other[2]) && */0 != $info_other[2]) {
					$result[] = array(
						'name' => $name[3],
						//'type' => $this->_cut($items[4]),
						//'rarity' => $this->_cut($items[5]),
						'expansion' => $this->_cut($items[6]),
						'amount' => (int)$info_other[2],
						'value' => (int)$info_other[1],
						'quality' => 'jiné'
					);
				}
			}
			
			$this->_setData($result);
		}
		
		return true;
	}
}

