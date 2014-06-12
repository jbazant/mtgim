<?php

require_once (__DIR__ . '/CernyRytir.php');

class LocalCernyRytir extends Application_Model_CernyRytir {
    public function doCardRequest($cardName) {
        $rawData = file_get_contents(__DIR__ . '/../tests/data/cr-bridge.html');

        return $this->_processData($rawData);
    }
}
