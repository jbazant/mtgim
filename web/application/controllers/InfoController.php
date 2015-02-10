<?php

require_once('Baz/Controller/Action.php');

/**
 * Class InfoController
 * Textove stranky aplikace
 */
class InfoController extends Baz_Controller_Action {
    public function aboutAction() {
        $this->view->pageId = 'page-info-about';
        $this->view->meta['keywords']['content'] = 'O aplikaci MtGiM';
        $this->view->meta['description']['content'] = 'Informace o aplikaci Magic v Mobilu.';
    }

    public function tipsAction() {
        $this->view->pageId = 'page-info-tips';
        $this->view->meta['keywords']['content'] = 'Tipy a triky, MtGiM';
        $this->view->meta['description']['content'] = 'Tipy a triky pro efektivní používání aplikace Magic v Mobilu.';
    }

    public function changelogAction() {
        $this->view->pageId = 'page-info-changelog';
        $this->view->meta['keywords']['content'] = 'MtGiM changelog, Novinky, Změny v aplikaci';
        $this->view->meta['description']['content'] = 'Novinky a změny v aplikaci.';
    }

    public function termsAction() {
        $this->view->pageId = 'page-info-terms';
        $this->view->meta['keywords']['content'] = 'MtGiM podmínky používání';
        $this->view->meta['description']['content'] = 'Podmínky používání aplikace. Prohlášní o využívání souborů cookies a ochraně osobních dat';
    }

    public function apiAction() {
        $this->view->pageId = 'page-info-api';
        $this->view->meta['keywords']['content'] = 'MtGiM API';
        $this->view->meta['description']['content'] = 'Dokumentace k poskytovanému API pro vyhledávání cen karet.';
    }
}
