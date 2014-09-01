<?php

/**
 * Testy pro tridu Application_Model_Form_Contact2
 * Class Model_ContactTest
 */
class Model_ContactTest extends PHPUnit_Framework_TestCase {

    /**
     * setup - include tridy
     */
    public function setUp() {
        require_once(APPLICATION_PATH . '/models/Form/Contact2.php');
    }


    /**
     * Test, ze kontakt form se chova rozumne, pokud nedostane parametry
     * @expectedException Zend_Form_Exception
     */
    public function testNoAttribInstance() {
        new Application_Model_Form_Contact2();
    }


    /**
     * Test, ze kontakt form se chova rozumne pri spatnych parametrech
     * @expectedException Zend_Form_Exception
     */
    public function testBadAttributes() {
        $t = new Application_Model_Form_Contact2(array(
            'mailerSettings' => array('a' => 'a'),
        ));
    }


    /**
     * Test zaklani (nejmensi mozne) inicializace formulare
     */
    public function testBasicMailer() {
        $t = new Application_Model_Form_Contact2(array(
            'mailerSettings' => array('contactEmail' => 'test@mtgim.cz'),
        ));

        /** @var Zend_Mail $mailer */
        $mailer = $t->getMailer();
        $this->assertInstanceOf('Zend_Mail', $mailer);
        $this->assertEquals(array('test@mtgim.cz'), $mailer->getRecipients());
    }


    /**
     * Test uplne inicializace formulare
     */
    public function testFullMailer() {
        $testMailer = new Zend_Mail('cp1250');
        $t = new Application_Model_Form_Contact2(array(
            'mailer' => $testMailer,
            'mailerSettings' => array(
                'contactEmail' => 'test1@mtgim.cz',
                'fromEmail'    => 'test2@mtgim.cz',
            ),
        ));

        /** @var Zend_Mail $mailer */
        $mailer = $t->getMailer();
        $this->assertInstanceOf('Zend_Mail', $mailer);
        $this->assertEquals($testMailer, $mailer);
        $this->assertEquals(array('test1@mtgim.cz'), $mailer->getRecipients());
        $this->assertEquals('test2@mtgim.cz', $mailer->getFrom());
    }
}
