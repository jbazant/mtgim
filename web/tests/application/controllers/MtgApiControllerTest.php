<?php
/**
 * Created by PhpStorm.
 * User: bazantj
 * Date: 8.9.14
 * Time: 23:31
 */

class MtgApiControllerTest extends ControllerTestCase {
    public function testSkip() {

        $this->request->setHeader('Accept', 'application/json');
        $this->dispatch('/mtg-api/invalid');
        $this->assertHeader('Content-Type: text/json');
        $this->assertEquals(
            $this->getResponse()->getBody(),
            '{error: "Invalid"}'
        );
    }
}
 