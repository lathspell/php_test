<?php

class PCRE_Test extends PHPUnit_Framework_TestCase {

    public function testGrouping() {
        // ?: bedeutet nur grouping aber nicht capturing
        // ?= bedeutet nur look-ahead aber nicht capturing
        //    (hier: nur spaces mit Datum dahinter)
        preg_match('/^(\d{1,2}([a-z]+))(?:\s*)\S+ (?=200[0-9])/', '21st March 2006', $matches);
        self::assertEquals(array("21st March ", "21st", "st"), $matches);
        // TODO: warum bloß das Space hinter March?
    }

}
