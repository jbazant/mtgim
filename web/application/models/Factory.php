<?php
/**
 * @file tovarna na Baz_Http_ShopPost objekty
 * @author bazantj
 */

/**
 * @brief tovarna na Baz_Http_ShopPost objekty 
 * @author bazantj
 *
 */
class Application_Model_Factory
{
	/**
	 * 
	 * @param string $server
     * @param string|null $foil
     * @return Baz_Http_ShopPost
	 */
	public static function getModel($server, $foil = NULL, $isTest = 0)
	{
        /** @var Baz_Http_ShopPost $adapter */
        if (!array_key_exists($server, self::getAvailableModels($isTest))) {
            return NULL;
        }

		switch ($server) {
			case 'cernyrytir':
                require_once(__DIR__ . '/CernyRytir.php');
				$adapter = new Application_Model_CernyRytir();
                break;

			case 'mystic':
                require_once(__DIR__ . '/MysticShop.php');
				$adapter = new Application_Model_MysticShop();
                break;

			case 'najada':
                require_once(__DIR__ . '/Najada.php');
				$adapter = new Application_Model_Najada();
                break;

            case 'rishada':
                require_once(__DIR__ . '/Rishada.php');
                $adapter = new Application_Model_Rishada();
                break;

            case 'fake':
                require_once(__DIR__ . '/Fake.php');
                $adapter = new Application_Model_Fake();
                break;

            case 'fake_cr':
                require_once(__DIR__ . '/Local/CernyRytir.php');
                $adapter = new Application_Model_Local_CernyRytir();
                $adapter->setFileName('cr-bridge.html');
//                $adapter->setFileName('cr-emblem.htm');
//                $adapter->setFileName('cr-token.htm');
                break;

            case 'fake_rishada':
                require_once(__DIR__ . '/Local/Rishada.php');
                $adapter = new Application_Model_Local_Rishada();
                $adapter->setFileName('rishada.htm');
                break;

			default:
				throw new Exception ('Unknown adapter server: ' . $server);
		}

        if (!empty($foil)) {
            if ('basic' == $foil) {
                $adapter->setFoilType('R');
            }
            elseif ('foil' == $foil) {
                $adapter->setFoilType('F');
            }
        }

        return $adapter;
	}

    /**
     * Specifikuje dostupne adaptery
     * @param int $isTest
     * @return array
     */
    public static function getAvailableModels($isTest = 0) {
        $ret = array(
            'cernyrytir' => 'Černý Rytíř',
            'mystic' => 'Mystic Shop',
            'najada' => 'Najáda',
            'rishada' => 'Rishada',
        );

        if (1 == $isTest) {
            $ret['fake'] = 'Fake Adapter';
            $ret['fake_rishada'] = 'Fake Rishada';
            $ret['fake_cr'] = 'Fake Černý Rytíř';
        }

        return $ret;
    }

}

