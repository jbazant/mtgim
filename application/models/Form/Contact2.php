<?php

class Application_Model_Form_Contact2 extends Zend_Form {
    public function init() {
        $this
            ->setAction('/index/test')
            ->setMethod(self::METHOD_POST)
        ;
        //todo doplnit formulari classu a potom bacha jak je napsany js selector


        $default_decorators = array(
            array('ViewHelper'),
            array('Errors'),
            array('Description', array('tag' => 'p', 'class' => 'description')),
            //array('HtmlTag', array('tag' => '')), // tenhle tag pouzit pouze pro antispam
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
                        array('HtmlTag', array('tag' => 'div', 'class' => 'anti_spam_holder')),
                        array('Label'),
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

    }

    //todo zpracovani formulare
}