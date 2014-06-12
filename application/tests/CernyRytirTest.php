<?php

require_once (__DIR__ . '/../models/CernyRytir.php');

class LocalCernyRytir extends Application_Model_CernyRytir {
    public function doCardRequest($cardName) {
        $rawData = file_get_contents(__DIR__ . '/data/' . $cardName . '.html');

        return $this->_processData($rawData);
    }
}

class CernyRytirTest extends  PHPUnit_Framework_TestCase {
    public function testParsing() {
        $c = new LocalCernyRytir();

        $this->assertEquals(
            array(),
            $c->doCardRequest('cr-bridge')
        );

    }

}