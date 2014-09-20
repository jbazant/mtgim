<?php
require_once('Baz/Http/ShopPost.php');

/**
 * Trida pro stahovani vysledku z obchodu Rishada
 * Class Application_Model_Rishada
 */
class Application_Model_Rishada extends Baz_Http_ShopPost {

    /**
     * URL na vysledky hledani karet v obchodu Rishada
     * @var string
     */
    protected $_url = 'http://rishada.cz/kusovky/vysledky-hledani';

    /**
     * U Rishady se pouziva get a dlouha uri
     * @var string
     */
    protected $_method = Zend_Http_Client::GET;


    /**
     * @see parent
     * @param string $cardName
     * @return array
     * @throws Exception
     */
    protected function _getParams($cardName)
    {
        switch($this->_foilType) {
            case 'A':
                $foil = 2;
                break;

            case 'F':
                $foil = 1;
                break;

            case 'R':
                $foil = 0;
                break;

            default:
                throw new Exception('Unknown foil type ' . $this->_foilType);
        }

        return array(
            'searchtype'    => 'advanced',
            'xxwhichpage'   => 1,
            'xxcardname'    => $cardName,
            'xxtype'        => '',
            'xxtext'        => '',
            'xxedition'     => '1000000',
            'xxminquality'  => 4,
            'xxmaxquality'  => 1,
            'xxfoil'        => $foil,
            'xxsignature'   => 2,
            'xxstamp'       => 2,
            'xxwhite'       => 2,
            'xxblue'        => 2,
            'xxblack'       => 2,
            'xxred'         => 2,
            'xxgreen'       => 2,
            'xxpowermin'    => '',
            'xxpowermax'    => '',
            'xxtoughnessmin' => '',
            'xxtoughnessmax' => '',
            'xxcmcmin'      => '',
            'xxcmcmax'      => '',
            'xxpricemin'    => '',
            'xxpricemax'    => '',
            'xxpagesize'    => 50,
            'search'        => 'Vyhledat',
        );
    }


    /**
     * @see parent
     * @param string $rowData
     * @return bool
     */
    protected function _processData($rowData)
    {
        //najdu <div class="results">
        $dataPosition = strpos($rowData, '<div class="results">');

        if (!$dataPosition) {
            return false;
        }

        // a oriznu to podle nej
        $begin = substr($rowData, $dataPosition);

        // data v tabulce jsou to co hledam
        $tbody = substr($begin, strpos($begin, '<table'));
        $data = substr($tbody, 0, strpos($tbody, '</table>'));

        //cleanup
        unset($begin);
        unset($tbody);

        //now i want to cut next line
        $data = substr($data, strpos($data, '<tr') + 4);

        //get all results
        $result = array();

        // from second line till end i got my data
        while ($start = strpos($data, '<tr')) {
            $data = substr($data, $start + 18);
            $row = substr($data, 4, strpos($data, '</tr>') - 4 );
            $items = explode('</td>', $row);

            // jmeno obsahuje jeste dalsi tagy
            $name = explode('>', substr(trim($items[0]), 0, -4));
            $ltPos = strpos($name[2], '<');
            $cardname = $ltPos > 0 ? substr($name[2], 0, $ltPos) : $name[2];

            // Near mint nechci zobrazovat
            $quality = $this->_cut($items[3]);
            if ('Near Mint' == $quality) {
                $quality = '';
            }

            // u ceny chci jen samotne cislo, ostatni zahazuji
            $value = preg_replace('/[^0-9]+/', '', $this->_cut($items[5]));

            // sestavim polozku
            $result[] = array(
                'name' => html_entity_decode($cardname),
                'expansion' => $this->_cut($items[1]),
                'value' => (int)$value,
                'amount' => (int)$this->_cut($items[6]),
                'quality' => $quality,
            );
        }

        // ulozim data
        $this->_setData($result);

        // vratim uspech
        return TRUE;
    }
}
