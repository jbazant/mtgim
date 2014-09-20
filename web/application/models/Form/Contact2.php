<?php

//todo testy - potrebuji k nim bootstrap zendu?
require_once('Zend/Form.php');
require_once('Zend/Form/Exception.php');


/**
 * Class Application_Model_Form_Contact2
 *
 * Pri vytvareni objektu se predpoklada, ze dostane v poli parametru atributy
 *  array mailerSettings (from => nepovinne, to => povinne)
 *  Zend_Mail mailer
 *
 * Pokud mailer neni uveden pouzije se Zend_Mail('UTF-8')
 */
class Application_Model_Form_Contact2 extends Zend_Form {

    /**
     * @var null|array
     */
    protected $_mailerSettings = NULL;

    /**
     * Adapter zajistujici odeslani emailu
     * @var null|Zend_Mail
     */
    protected $_mailer = NULL;


    /**
     * Inicializace formulare
     * @throws Zend_Form_Exception
     */
    public function init() {
        $this
            ->setMethod(self::METHOD_POST)
            ->setAttrib('id', 'contactform')
            ->_addElements()
        ;

        if (!isset($this->_mailer)) {
            require_once('Zend/Mail.php');
            $this->_mailer = new Zend_Mail('UTF-8');
        }

        if (!isset($this->_mailerSettings)) {
            throw new Zend_Form_Exception('Mailer settings are not set!', 101);
        }

        if (empty($this->_mailerSettings['contactEmail'])) {
            throw new Zend_Form_Exception('Contact email is not set!', 102);
        }
        else {
            $this->_mailer->addTo($this->_mailerSettings['contactEmail']);
        }

        if (!empty($this->_mailerSettings['fromEmail'])) {
            $this->_mailer->setFrom($this->_mailerSettings['fromEmail']);
        }
    }


    /**
     * Validace formulare
     * @param array $data
     * @return bool
     */
    public function isValid($data) {
        if (parent::isValid($data)) {
            // sem patri specialni logika validace
            // prozatim ovsem zadna neni potreba
            return TRUE;
        }
        else {
            return FALSE;
        }
    }


    /**
     * Zpracovani formulare
     * @param array $data
     * @return string
     */
    public function process($data) {
        if ($this->isValid($data)) {
            return ($this->_validProcess()) ? 'sent' : 'error';
        }
        else {
            return 'not_valid';
        }
    }


    /**
     * Funkce se samotnou logikou formulare
     * Sestavi a odesle email
     * @return bool
     */
    protected function _validProcess() {
        $m = $this->_mailer;

        $senderEmail = $this->getValue('f_user_email');

        $m->setSubject('MtGiM - zprava od ' . $senderEmail);
        $m->setBodyText(sprintf(
            "Zpráva z webu MtGiM\n\nod: %s\n\ntelefon: %s\n\ntext:%s",
            $senderEmail,
            $this->getValue('f_user_phone'),
            $this->getValue('f_message')
        ));

        try {
            $m->send();
            return TRUE;
        }
        catch (Exception $e) {
            return FALSE;
        }
    }


    /**
     * Inicializace elementu formulare vcetne validatoru
     * @return Application_Model_Form_Contact2
     */
    protected function _addElements() {
        $default_decorators = array(
            array('ViewHelper'),
            array('Errors'),
            array('Description', array('tag' => 'p', 'class' => 'description')),
            array('Label'),
        );

        $this->addElements(array(
            // email
            array(
                'name' => 'f_user_email',
                'type' => 'text',
                'options' => array(
                    'label' => 'E-mail',
                    'required' => TRUE,
                    'filters' => array('StringTrim'),
                    'validators' => array('email'),
                    'decorators' => $default_decorators,
                ),
            ),

            // telefon
            array(
                'name' => 'f_user_phone',
                'type' => 'text',
                'options' => array(
                    'label' => 'Telefon (nepovinné)',
                    'required' => FALSE,
                    'filters' => array('StringTrim'), //todo filter mezery
                    'validators' => array(), //todo validate telefon
                    'decorators' => $default_decorators,
                ),
            ),

            array(
                'name' => 'f_message',
                'type' => 'textarea',
                'options' => array(
                    'label' => 'Sdělení',
                    'required' => TRUE,
                    'filters' => array('StripTags', 'StringTrim'),
                    'decorators' => $default_decorators,
                ),
            ),

            array(
                'name' => 'anti_spam_check',
                'type' => 'text',
                'options' => array(
                    'label' => 'Kontrolní pole',
                    'required' => TRUE,
                    'description' => 'Toto pole by mělo být vyplněno automaticky. Pokud tomu tak není. Vepiště do něj "anti-spam kontrola".',
                    'decorators' => array(
                        array('ViewHelper'),
                        array('Errors'),
                        array('Description', array('tag' => 'p', 'class' => 'description')),
                        array('Label'),
                        array('HtmlTag', array('tag' => 'div', 'class' => 'anti_spam_holder')),
                    ),
                    'validators' => array(), // todo kontrola, ze je vyplnen pozadovany text
                ),
            ),

            array(
                'name' => 'find',
                'type' => 'submit',
                'options' => array(
                    'label' => 'Odeslat',
                    'data-transition' => 'slide',
                    'decorators' => array(
                        array('ViewHelper'),
                    ),
                ),
            ),
        ));

        return $this;
    }


    /**
     * Protected mailer setter
     * @param $mailer
     */
    protected function setMailer($mailer) {
        $this->_mailer = $mailer;
    }


    /**
     * Mailer getter
     * @return Zend_Mail
     */
    public function getMailer() {
        return $this->_mailer;
    }


    /**
     * Protected mailerSettings setter
     * @param $settings
     */
    protected function setMailerSettings($settings) {
        $this->_mailerSettings = $settings;
    }


    /**
     * Mailer settings getter
     * @return array
     */
    public function getMailerSettings() {
        return $this->_mailerSettings;
    }
}