<?php

require_once('Baz/Controller/Action.php');

/**
 * Class InfoController
 * Textove stranky aplikace
 */
class InfoController extends Baz_Controller_Action {
    public function aboutAction() {
        $this->view->pageId = 'page-info-about';
    }

    public function tipsAction() {
        $this->view->pageId = 'page-info-tips';
    }

    public function changelogAction() {
        $this->view->pageId = 'page-info-changelog';
    }

    public function termsAction() {
        $this->view->pageId = 'page-info-terms';
    }
}
