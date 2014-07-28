<?php

/**
 * Trait pro lokalni testovani shop adapteru
 * Trait Application_Model_Local_Trait
 */
trait Application_Model_Local_Trait {
    /**
     * @var nazev soouboru, ktery se bude nacitat
     */
    protected $_fileName;

    /**
     * Setter pro nastaveni nazvu souboru
     * @param $name
     */
    public function setFileName($name) {
        $this->_fileName = $name;
    }

    /**
     * Prepise metodu doCardRequest tak, aby brala data z lokalniho (statickeho) zdroje
     * @param string $cardName nepouziva se, definovano pouze pro zachovani stejneho behaviouru
     * @return array mixed
     * @throws Exception pri neplatnem nastaveni jmena lokalniho souboru
     */
    public function doCardRequest($cardName) {
        if (empty($this->_fileName)) {
            throw new Exception('No filename set.');
        }

        // sestavim adresu lokalniho souboru a overim
        $fullFileName = __DIR__ . '/../../../tests/testdata/' . $this->_fileName;

        if (!is_readable($fullFileName)) {
            throw new Exception ('Cannot read file: ' . $fullFileName);
        }

        // nactu soubor
        $rawData = file_get_contents($fullFileName);

        // prekoduji pokud je potreba
        if ($this->_encodeFrom && $this->_encodeFrom !== 'utf-8') {
            $rawData = iconv($this->_encodeFrom, 'utf-8', $rawData);
        }

        // zpracuji a vratim data standardni cestou
        return $this->_processData($rawData);
    }
}
