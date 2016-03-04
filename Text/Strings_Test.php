<?php

class Strings_Test extends PHPUnit_Framework_TestCase {
    public function testStrings() {
        $s = "1111";
        $s{2} = "0";
        self::assertEquals('1101', $s);
    }
}
