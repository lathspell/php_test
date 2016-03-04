<?php

class Lang_Test extends PHPUnit_Framework_TestCase {

    public function testExtract() {

        $array = array('a' => 'John',
            'b' => 'Coggeshall',
            'c' => array('d' => 'John',
                'e' => 'Smith'));

        function something($array) {
            extract($array);  // nur function-local
            return $c['e'];
        }

        self::assertFalse(isset($c));
        self::assertEquals("Smith", something($array));
    }

    public function testRefs() {

        // What is the output of the following?
        // Answer... 	50 	5 	95 	10 	100

        function byRef(&$number) {
            $number *= 10;
            return ($number - 5);
        }

        $number = 10;
        $number = byRef($number);

        self::assertEquals(95, $number);
    }

    public function testFunnyIdentifier() {
        ${'0-neg tive'} = 42;
        ${'0-neg tive'}++;
        self::assertEquals(43, ${'0-neg tive'});
    }

    public function testForeach() {
        $array = array(1, 2, 3);
        foreach ($array as $key => &$val) {
            $val++;
        }
        self::assertEquals(array(2, 3, 4), $array);
    }

    public function testNumericConstants() {
        $a = 010;
        $b = 0xA;
        $c = 2;

        self::assertEquals(20, $a + $b + $c);
    }

}
