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
	 * @param Baz_Http_ShopPost $server
	 */
	public static function getModel($server)
	{
		switch ($server) {
			case 'cernyrytir':
				return new Application_Model_CernyRytir();

            case 'cernyrytir_basic':
                $a = new Application_Model_CernyRytir();
                $a->setFoilType('R');
                return $a;

            case 'cernyrytir_foil':
                $a = new Application_Model_CernyRytir();
                $a->setFoilType('F');
                return $a;

			case 'mystic':
				return new Application_Model_MysticShop();

			case 'najada':
				return new Application_Model_Najada();

            case 'fake':
                return new Application_Model_Fake();

            case 'fakecr':
                require_once(__DIR__ . '/pom.php');
                return new LocalCernyRytir();

			default:
				return null;
		} 
	}

}

