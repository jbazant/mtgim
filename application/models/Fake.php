<?php
require_once('Baz/Http/ShopPost.php');

final class Application_Model_Fake extends Baz_Http_ShopPost
{
    protected $_url = 'http://localhost';
    protected $_encodeFrom = 'cp1250';

    protected function _getParams($cardName)
    {
        return array();
    }

    protected function _processData($rowData)
    {
        for ($i = 0; $i < 3; ++$i) {
            $result[] = array(
                'name' => 'Testovaci karticka',
                'expansion' => 'Return to Ravnica',
                'amount' => $i,
                'value' => 100 * $i,
                'quality' => '',
            );
        }

        $this->_setData($result);
        return true;
    }
}
