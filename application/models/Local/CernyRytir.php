<?php

// potrebne soubory - staticke linkovani je rychlejsi nez autoloader
// navic je potreba pro unit testy
require_once(__DIR__ . '/../CernyRytir.php');
require_once(__DIR__ . '/Trait.php');

/**
 * Trida pro testovani eshopu Cerny Rytir
 * Class Application_Model_Local_CernyRytir
 */
class Application_Model_Local_CernyRytir extends Application_Model_CernyRytir {
    use Application_Model_Local_Trait;
}