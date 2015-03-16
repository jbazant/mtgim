<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 16.3.2015
 * Time: 7:54
 */

require_once('Baz/Controller/Action.php');


class RefController extends Baz_Controller_Action {
    public function qrAction() {
        //todo - log it somewhere
        $this->getHelper('redirector')->goto('index', 'index');
    }
}