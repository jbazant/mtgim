<?php
require_once('Baz/Http/ShopPost.php');

final class Application_Model_Rishada extends Baz_Http_ShopPost
{
    protected $_url = 'http://mysticshop.cz/mtgshop.php';


    protected function _getParams($cardName)
    {
        return array(
        );
    }

    protected function _processData($rowData)
    {
        return true;
    }
}
