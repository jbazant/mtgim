<?php
require_once('Baz/Http/ShopPost.php');


/**
 * Trida pro ziskani dat z cernyrytir.cz
 * Class Application_Model_CernyRytir
 */
class Application_Model_CernyRytir extends Baz_Http_ShopPost {
    /** @var string $_url adresa akce */
	protected $_url = 'http://cernyrytir.cz/index.php3?akce=3';

    /** @var string $encodeFrom kodovani stranek */
	protected $_encodeFrom = 'cp1250';

    /**
     * POST parametry formulare pro vyhledavani karet
     * @param string $cardName
     * @return array
     */
    protected function _getParams($cardName) {
		return array(
            'edice_magic' => 'libovolna',
            'rarita' => 'A',
            'foil' => $this->_foilType,
            'jmenokarty' => $cardName,
            'triditpodle' => 'jmena',
            'submit' => 'Vyhledej',
		);
	}

    /**
     * Zpracovani dat dle cerneho rytire.
     * Vzhledem ke kvalite kodu nelze pouzit DOMDOcument nebo neco podobneho
     *
     * @param string $rowData
     * @return bool
     */
    protected function _processData($rowData) {
        // nalezeni druheho vyskytu tridy "kusovkytext"
        $dataPosition = strpos($rowData, 'bordercolor="#000000" class="kusovkytext">');
        $dataPosition = strpos($rowData, 'bordercolor="#000000" class="kusovkytext">', $dataPosition + 30);

        // pokud jsem nenasel, vracim neuspech
		if (!$dataPosition) {
            // jeste zjistim, jetli se mi nepodarilo vykonat pozadavek, nebo jsem jen nic nenasel
            return (FALSE !== strpos($rowData, 'Zvoleným kritériím neodpovídá žádná karta'));
		}

        // odriznu nezajimavy konec
		$begin = substr($rowData, $dataPosition);
		$table = substr($begin, 0, strpos($begin, '</table>'));

		//now i want to cut next line
		$data = substr($table, strpos($table, '<tr') + 4);
		
		//cleanup
		unset($begin);
		unset($table);
		
		//get all results
		$result = array();

        $data = explode('</tr>', $data);

        // prochazim samotna data a parsuji jednotlive pozadovane polozky
        for ($i = 0; $i < count($data); $i = $i + 3) {
            $items =  explode('</td>', $data[$i]);
            // pokud radek nema pozadovany trvar, zkusim dalsi
            if (empty($items[1])) {
                continue;
            }

            $nameStr = trim(str_replace('</font></div>', '', $this->_getRowText($items[1])));
            $nameArr = explode(' - ', $nameStr);
            $name = $nameArr[0];
            $quality = array_key_exists(1, $nameArr) ? $nameArr[1] : '';

            $items = explode('</td>', $data[$i + 1]);
            $expansion = $this->_getRowText($items[0]);

            $items = explode('</td>', $data[$i + 2]);
            $amount = trim(str_replace('&nbsp;ks', '', $this->_getRowText($items[1])));
            $value = trim(str_replace('&nbsp;Kč', '', $this->_getRowText($items[2])));

            $result[] = array(
                'name'      => $name,
                'expansion' => $expansion,
                'amount'    => (int)$amount,
                'value'     => (int)$value,
                'quality'   => $quality,
            );
        }

        $this->_setData($result);
		
		return true;
	}


    /**
     * Pomocna funkce pro vyparsovani jedneho hodnoty
     * @param string $row
     * @return string
     */
    protected function _getRowText($row) {
        return trim(substr($row, strrpos($row, '">', -1) + 2));
    }
}
