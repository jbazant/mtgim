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
	public static function getModel($server, $foil = NULL)
	{
        /** @var Baz_Http_ShopPost $adapter */

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
				return null;
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

}

