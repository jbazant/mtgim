<?php
/**
 * Aktualizovane testy
 *
 */

// todo tohle by melo byt poresene v phpunit.xml
defined('TEST_ROOT_PATH')
    || define('TEST_ROOT_PATH', __DIR__ . '/../../..');
// nastaveni include path, potrebne pro spravne instancovani objektu
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(TEST_ROOT_PATH . '/library'),
    get_include_path(),
)));


class ParsingTest extends PHPUnit_Framework_TestCase {
    /**
     * @dataProvider providerParsing
     * @param string $localModel
     * @param string $file
     * @param int $count
     */
    public function testParsing($localModel, $file, $count) {
        /** @var Baz_Http_ShopPost $modelName */
        $fileName = TEST_ROOT_PATH . '/application/models/Local/' . $localModel . '.php';
        $this->assertFileExists($fileName);
        require_once ($fileName);

        $modelName = 'Application_Model_Local_' . $localModel;
        $this->assertTrue(class_exists($modelName));
        $c = new $modelName();

        $c->setFileName($file);

        $this->assertTrue(
            $c->doCardRequest('anything')
        );

        $this->assertEquals(
            $count,
            count($c->getData())
        );
    }


    /**
     * Data source pro zakladni overeni funkcnosti parsovani
     * @return array
     */
    public function providerParsing() {
        return array(
            array('CernyRytir', 'cr-bridge.html', 4),
            array('CernyRytir', 'cr-emblem.htm', 19),
            //array('CernyRytir', 'cr-token.htm', 20),
            array('Rishada', 'rishada.htm', 50),
            array('Najada', 'najada1.html', 30),
            array('MysticShop', 'mysticshop1.html', 27),
            array('MysticShop', 'mysticshop2.html', 5),
        );
    }
}