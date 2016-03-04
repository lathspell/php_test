<?php

class Math_Test extends PHPUnit_Framework_TestCase {

    public function testGCD() {
        // größter gemeinsamer Teiler
        self::assertEquals(3, $this->gcd(9, 15));
    }

    function gcd($p, $q) {
        return ($q == 0) ? $p : $this->gcd($q, $p % $q);
    }
}
