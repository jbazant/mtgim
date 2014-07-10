<?php
/**
 * Soubor s testovacimi tridami,
 * ktere slouzi pro lokalni opakovatelne testovani funkcnosti adapteru.
 * Adapter dela dotaz na statickou stranku a pote standardni cestou parsuje odpoved.
 * Tyto adaptery jsou ovsem nezavisle na vstupnich datech a vzdy vraci stejny vysledek
 */

require_once (__DIR__ . '/CernyRytir.php');
require_once (__DIR__ . '/Rishada.php');

/**
 * Class LocalCernyRytir
 */
class LocalCernyRytir extends Application_Model_CernyRytir {
    public function doCardRequest($cardName) {
        $rawData = file_get_contents(__DIR__ . '/../tests/data/cr-bridge.html');

        return $this->_processData($rawData);
    }
}


/**
 * Class LocalRishada
 */
class LocalRishada extends Application_Model_Rishada {
    public function doCardRequest($cardName) {
        $rawData = file_get_contents(__DIR__ . '/../tests/data/rishada.htm');

        return $this->_processData($rawData);
    }
}
