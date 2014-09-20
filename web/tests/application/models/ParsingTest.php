<?php
/**
 * Aktualizovane testy
 *
 */

/**
 * Testy jednotlivych adapteru, ze umi spravne rozparsovat prijaty vstup.
 * Testy NEslouzi k overeni, ze adapter umi spravne sestavit pozadavek.
 * Class ParsingTest
 */
class ParsingTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerParsing
     * @param string $localModel
     * @param string $file
     * @param int $resultCount
     */
    public function testParsing($localModel, $file, $resultCount) {
        /** @var Baz_Http_ShopPost $modelName */
        $fileName = APPLICATION_PATH . '/models/Local/' . $localModel . '.php';
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
            $resultCount,
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
            //array('Najada', 'najada1.html', 30),
            array('MysticShop', 'mysticshop1.html', 27),
            array('MysticShop', 'mysticshop2.html', 5),
        );
    }
}