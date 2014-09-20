<?php

// potrebne soubory - staticke linkovani je rychlejsi nez autoloader
// navic je potreba pro unit testy
require_once(__DIR__ . '/../MysticShop.php');
require_once(__DIR__ . '/Trait.php');

/**
 * Trida pro testovani eshopu MysticShop
 * Class Application_Model_Local_MysticShop
 */
class Application_Model_Local_MysticShop extends Application_Model_MysticShop {
    use Application_Model_Local_Trait;
}
