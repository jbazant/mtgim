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
				$adapter = new Application_Model_CernyRytir();
                break;

			case 'mystic':
				$adapter = new Application_Model_MysticShop();
                break;

			case 'najada':
				$adapter = new Application_Model_Najada();
                break;

            case 'rishada':
                $adapter = new Application_Model_Rishada();
                break;

            case 'fake':
                $adapter = new Application_Model_Fake();
                break;

            case 'fake_cr':
                require_once(__DIR__ . '/pom.php');
                $adapter = new LocalCernyRytir();
                break;

            case 'fake_rishada':
                require_once(__DIR__ . '/pom.php');
                $adapter = new LocalRishada();
                break;

			default:
				return null;
		}

        if (!empty($foil)) {
            if ('basic' == $foil) {
                $adapter->setFoilType('R');
            }
            elseif('foil' == $foil) {
                $adapter->setFoilType('F');
            }
        }

        return $adapter;
	}

}

