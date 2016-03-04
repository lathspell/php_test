<?php
namespace de\lathspell\test\lang;

use \PHPUnit_Framework_TestCase;

class PHP_5_3_Test extends PHPUnit_Framework_TestCase {

    public function testNamespace() {
        self::assertEquals("de\\lathspell\\test\\lang", __NAMESPACE__);
    }

    public function testTernaryShortcut() {
        $input = null;
        $x = $input ?: "irgendwas";
        self::assertEquals("irgendwas", $x);
    }

    public function testTheEvil() {
        $x = 1;
        goto ende;
        $x = 2;
        ende:
        self::assertEquals(1, $x);

        goto big;
        $x = 3;
        big: {
            $x = 4;
            $x = 5;
        }
        self::assertEquals(5, $x);
    }

    private function runFunc($i, \Closure $func) {
        return $func($i); // ^^^ manual: "don't rely on the class name Closure"!
    }

    public function testClosures() {
        $inc = function($i) { return $i+1; };
        $dec = function($i) { return $i-1; };

        self::assertEquals(3, $inc(2));
        self::assertEquals(3, $this->runFunc(2, $inc));
    }
}