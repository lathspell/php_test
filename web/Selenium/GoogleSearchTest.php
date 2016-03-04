<?php

class GoogleSearchTest extends PHPUnit_Extensions_SeleniumTestCase {

    function setUp() {
        $this->setBrowser("*firefox");
        $this->setBrowserUrl("http://www.google.de/");
    }

    function testMyTestCase() {
        $this->open("/");
        $this->assertTitle("Google");
        $this->isElementPresent('//input[@name="btnK"]');
        $this->isElementPresent('//input[@name="q"]');
        $this->type('q', 'www.lathspell.de');
        // - clickAndWait() does not work well if too much JavaScript is involved.
        // - click() does not work with every JavaScript, fireEvent() and mouseDown() are alternatives
        $this->click('btnK');
        $this->waitForCondition("selenium.isTextPresent('another boring homepage')");
        // isTextPresent() is not a PHPUnit assertion by itself!
        $this->assertTrue($this->isTextPresent('another boring homepage'));
    }

}
