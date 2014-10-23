<?php

/**
 * todo testy
 * Trida modelu kontaktniho formulare
 * Class Application_Model_Form_Contact
 */
class Application_Model_Form_Contact {
    /**
     * @var Zend_Controller_Request_Abstract
     */
    private $_request;

    /**
     * Zend Mail nebo objekt umplementujici stejne API (podmnozinu)
     * @var Zend_Mail
     */
    private $_mailer;

    /**
     * Pole chyb, ktere nastaly pri zpracovani formulare
     * @var array
     */
    private $_errors = array();


    /**
     *
     * @param Zend_Controller_Request_Abstract$request
     * @param Zend_Mail|null $mailer
     * @param string|null $to
     */
    public function __construct($request, $config, $mailer = NULL) {
        $this->_request = $request;

        if (isset($mailer)) {
            $this->_mailer = $mailer;
        }
        else {
            $this->_mailer = new Zend_Mail('UTF-8');
        }

        $this->_mailer->addTo($config->contactEmail);
        if (!empty($config->fromEmail)) {
            $this->_mailer->setFrom($config->fromEmail);
        }
    }


    /**
     * Validace formulare
     */
    private function _validate() {
        // validace emailu
        $email = $this->_request->getParam('f_user_email');
        if ('' == trim($email)) {
            $this->_addError('Pole s emailem nesmí být prázdné', 'f_user_email');
        }

        if (100 < strlen($email)) {
            $this->_addError('Zadaný email je příliž dlouhý', 'f_user_email');
        }

        if ('' == trim($this->_request->getParam('f_message'))) {
            $this->_addError('Sdělení nesmí být prázdné', 'f_user_email');
        }

        // validace telefonu
        if (!preg_match('/^([0-9]+[ ]*)*$/', $this->_request->getParam('f_user_phone'))) {
            $this->_addError('Zadaný telefon je v neplatném tvaru', 'f_user_phone');
        }

        // anti spam check
        if ('Anti-spam check' != $this->_request->getParam('anti_spam_check')) {
            $this->_addError('Prosím vyplňte kontrolní pole', 'anti_spam_check');
        }
    }


    /**
     * Akce formulare - odeslani emailu se zpravou od uzivatele
     */
    private function _submit() {
        $m = $this->_mailer;

        $senderEmail = $this->_request->getParam('f_user_email');

        $m->setReplyTo($senderEmail);
        $m->setSubject('MtGiM - zprava od ' . $senderEmail);
        $m->setBodyText(sprintf(
            "Zpráva z webu MtGiM\n\nAutor: %s (tel: %s)\n\nText:\n%s",
            $senderEmail,
            $this->_request->getParam('f_user_phone'),
            $this->_request->getParam('f_message')
        ));

        try {
            $m->send();
        }
        catch (Exception $e) {
            //todo nekam zalogovat
            $this->_addError('Omlouváme se, zprávu se nepovedlo odeslat.');
        }
    }


    /**
     * Pridani chyby formulare
     * @param string $msg
     * @param string $field
     */
    private function _addError($msg, $field = '_') {
        $this->_errors[$field][] = $msg;
    }

    /**
     * Validace a zpracovani formulare
     * @return bool
     */
    public function process() {
        $this->_validate();
        if (0 == count($this->_errors)) {
            $this->_submit();
        }

        return count($this->_errors) > 0;
    }

    /**
     * Getter pro cteni pole chyb
     * @return array
     */
    public function getErrors() {
        return $this->_errors;
    }
}
