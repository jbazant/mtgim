<?php

class Application_Model_Form_Contact2 extends Zend_Form {
    public function init() {
        $this
            ->setAction('/index/test')
            ->setMethod(self::METHOD_POST)
        ;

        //todo nejak se zbavit obecneho decoraturu dt dd

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
                ),
            ),

            array(
                'name' => 'f_message',
                'type' => 'textarea',
                'options' => array(
                    'label' => 'Sdělení',
                    'required' => TRUE,
                    'filters' => array('StripTags', 'StringTrim'),
                ),
            ),

            //todo nejak doplnit div decorator
            array(
                'name' => 'anti_spam_check',
                'type' => 'text',
                'options' => array(
                    'label' => 'Kontrolní pole',
                    'required' => TRUE,
                    'description' => 'Toto pole by mělo být vyplněno automaticky. Pokud tomu tak není. Vepiště do něj "anti-spam kontrola".'
                ),
            ),

            array(
                'name' => 'find',
                'type' => 'submit',
                'options' => array(
                    'label' => 'Odeslat',
                    'data-transition' => 'slide',
                ),
            ),
        ));
    }

    //todo zpracovani formulare
}