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
        if (FALSE === $dataPosition) {
            return FALSE;
        }

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

            list($name, $quality) = $this->_nameQualityProcess($nameStr);

            $items = explode('</td>', $data[$i + 1]);
            $expansion = $this->_expansionProcess($items[0]);

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

		return TRUE;
	}


    /**
     * Pomocna funkce pro vyparsovani jedneho hodnoty
     * @param string $row
     * @return string
     */
    protected function _getRowText($row) {
        return trim(substr($row, strrpos($row, '">', -1) + 2));
    }


    /**
     * Ve stringu nazvu kary byva i info o kvalite.
     * Navic tam byva Token a Emblem na ruznych mistech.
     *
     * @param string $nameStr
     * @return array Dvojice jmeno_karty, kvalita
     */
    protected function _nameQualityProcess($nameStr) {
        // rozdelim nazev podle pomlcky
        $nameArr = array_map('trim', explode(' - ', $nameStr));

        // Pokud je na prvnim miste slovo token, tak nazev je az v druhe polozce
        if ('Token' === $nameArr[0]) {
            $name = $nameArr[1];
            unset($nameArr[1]);
        }
        // Pokud je na prvnim miste slovo emblem, tak nazev je az v druhe polozce
        elseif ('Emblem' === $nameArr[0]) {
            $name = 'Emblem ' . $nameArr[1];
            unset($nameArr[0]);
            unset($nameArr[1]);
        }
        // standardne je nazev v prvni polozce
        else {
            $name = $nameArr[0];
            unset($nameArr[0]);
        }

        // vse co mi zustalo v poli je info o kvalite karty (pripadne oznaceni jako token, foil, ...)
        $quality = implode(', ', $nameArr);

        return array($name, $quality);
    }


    /**
     * Vyparsuje nazev edice a pripadne se pokusi prelozit jeji nazev do standartizovaneho tvaru.
     *
     * @param string $expStr Neorezany nazev edice
     * @return string Standartizovany nazev edice
     */
    protected function _expansionProcess($expStr) {
        $expansion = $this->_getRowText($expStr);

        // Magic 2015 prekladam na Magic 2015 Core Set
        if (preg_match('/^Magic 20[0-9]{2}$/', $expansion)) {
            $expansion = $expansion . ' Core Set';
        }
        // 9th Edition prekladam na Ninth Edition
        elseif (preg_match('/^[0-9]+th Edition$/', $expansion)) {
            $search = array('4th', '5th', '6th', '7th', '8th', '9th', '10th');
            $replace = array('Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth');

            $expansion = str_replace($search, $replace, $expansion);
        }
        // DD: prekladam na Duel Decks
        elseif (preg_match('/^DD:/', $expansion)) {
            $expansion = str_replace('DD', 'Duel Decks', $expansion);
        }
        // FTV prekladam na From the Vault
        elseif (preg_match('/^FTV:/', $expansion)) {
            $expansion = str_replace('FTV', 'From the Vault', $expansion);
        }
        // Commander preklady
        elseif (preg_match('/Commander/', $expansion)) {
            //todo tady zvazit, jestli chci prekladat Commander na Magic: The Gathering-Commander
            //todo obdobne pro conspiracy

            $expansion = str_replace('Commander 2013', 'Commander 2013 Edition', $expansion);
        }
        // Alpha prekladam na Limited Edition Alpha
        elseif (preg_match('/^(Aplha)|(Beta)$/', $expansion)) {
            $expansion = 'Limited Edition ' . $expansion;
        }
        // Unlimited prekladam na Unlimited Edition
        elseif (preg_match('/^(Unlimited)|(Revised)$/', $expansion)) {
            $expansion = $expansion . ' Edition';
        }

        return $expansion;
    }
}
