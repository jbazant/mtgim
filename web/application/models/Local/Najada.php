<?php

// potrebne soubory - staticke linkovani je rychlejsi nez autoloader
// navic je potreba pro unit testy
require_once(__DIR__ . '/../Najada.php');
require_once(__DIR__ . '/Trait.php');

/**
 * Trida pro testovani eshopu Najada
 * Class Application_Model_Local_Najada
 */
class Application_Model_Local_Najada extends Application_Model_Najada {
    use Application_Model_Local_Trait;
}
