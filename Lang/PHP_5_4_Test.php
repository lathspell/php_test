<?php

/** Beispiel Treat. */
trait GetUcClassNameTrait {
    private function getUcClassName() {
        return strtoupper(get_class($this));
    }
}

/** Examples of the new PHP 5.4 features.
 * 
 * What's not inside:
 *
 * - Scalar type hints for method parameters.
 * - Unicode/UTF-8 support in the language (e.g. strpos()).
 * - Native annotations support: they'll still be embedded in the docblocks and parsed in userland.
 * - Primitive types (integer, string...) as reserved words, removed for backward compatibility reasons.
 * - Foreach list() support.
 * 
 */
class PHP_5_4_Test extends PHPUnit_Framework_TestCase {
    use GetUcClassNameTrait;
    
    public function setUp() {
        if (PHP_VERSION_ID < "50400") {
            self::markTestSkipped("PHP 5.4 required!");
        }
    }

    public function testBinaryNotation() {
        $x = 0b0110;
        self::assertEquals(6, $x);
    }

    public function testTraits() {
        self::assertEquals(strtoupper(__CLASS__), $this->getUcClassName());
    }

    public function testArraySyntax() {
        $a = [1, 2, 3, 4];
        self::assertEquals(array(1,2,3,4), $a);
    }

    public function testArrayDereferencing() {
        function gimmeFive() {
            return array(1,2,3,4,5);
        }
        self::assertEquals(3, gimmeFive()[2]);
    }

    public function testCallableHint() {
        function applyFunction($x, callback $f) {
            return $f($x);
        }
        $sqrFunc = function($i) { return $i*$i; };
        // self::assertEquals(9, applyFunction(3, $sqrFunc));
        self::markTestSkipped("Scheinbar noch nicht in 5.4-alpha2");
    }

    public function testBuiltinWebServer() {
        // Start with "php -S localhost:1234 -t ./web/"
        self::assertTrue(true);
    }
    
    public function testDTrace() {
        // DTrace is Solaris (or Linux with self compiled modules).
        self::assertTrue(true);
    }
}
