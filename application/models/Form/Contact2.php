<?php
//todo kompletne komentare

class Application_Model_Form_Contact2 extends Zend_Form {
    public function init() {
        $this
            //todo tady potrebuji najit baseurl
            ->setAction('/index/test')
            ->setMethod(self::METHOD_POST)
            ->setAttrib('id', 'contactform')
            ->_addElements()
        ;
    }

    public function isValid($data) {
        if (parent::isValid($data)) {
            //todo sem patri specialni logika validace
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    //todo zpracovani formulare
    public function process($data) {
        $res = $this->isValid($data);
    }

    /**
     * Funkce se samotnou logikou formulare
     * Sestavi a odesle email
     */
    protected function _validProcess() {

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
}