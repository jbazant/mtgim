<?php

// potrebne soubory - staticke linkovani je rychlejsi nez autoloader
// navic je potreba pro unit testy
require_once(__DIR__ . '/../Rishada.php');
require_once(__DIR__ . '/Trait.php');

/**
 * Trida pro testovani eshopu Rishada
 * Class Application_Model_Local_Rishada
 */
class Application_Model_Local_Rishada extends Application_Model_Rishada {
    use Application_Model_Local_Trait;
}
