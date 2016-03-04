<?php

class Array_Test extends PHPUnit_Framework_TestCase {

    public function testIndexes() {
        $a = array(
            '3' => '4',
            'b' => '2',
            40
        );

        $keys = array_keys($a);
        self::assertEquals(3, count($keys));
        self::assertEquals('integer', gettype($keys[0])); // "3" wird zu 3
        self::assertEquals('string', gettype($keys[1]));
        self::assertEquals('integer', gettype($keys[2]));

        // Ein neuer Eintrag ohne expliziten Index bekommt den höchsten
        // numerischen Index + 1 als Index.
        self::assertEquals(40, $a[4]);

        // Negative Indices sind hier eine Ausnahme, da beginnt der automatische bei 0.
        $a = array(-3 => "a");
        $a[] = "b";
        self::assertEquals("b", $a[0]);

        // Strings vs. Int als Index
        $a = array(0 => "a", 10 => "b", "AAA" => "BBB");
        self::assertEquals("b", $a["10"]);
    }

    public function testUnset() {
        // Using hardcoded index
        $a = [ '10', '20', '30' ];
        unset($a[1]);
        self::assertEquals([0=>10, 2=>30], $a);

        // Using array_search
        $a = [ '10', '20', '30' ];
        $idx = array_search('20', $a);
        if ($idx !== false) {
            unset($a[$idx]);
        }
        self::assertEquals([0=>10, 2=>30], $a);
        // Caveat: if 'false' is not checked, it will result in $a[false] i.e. $a[0] !!!
        unset($a[array_search('20', $a)]); // WRONG!
        self::assertEquals([2=>30], $a); // Accidently removed value '10'!

        // Using array_diff
        $a = [ '10', '20', '30' ];
        $a = array_diff($a, ['20']);
        self::assertEquals([0=>10, 2=>30], $a);
    }

    public function testList() {
        error_reporting(0);

        $arr = array(3 => "First", 2 => "Second", 1 => "Third");

        list($a, $b, $c) = $arr;
        self::assertEquals(null, $a); // $arr[0]
        self::assertEquals("Third", $b); // $arr[1]
        self::assertEquals("Second", $c); // $arr[2]

        list(, $result) = $arr;
        self::assertEquals("Third", $result); // null=$arr[0]; $result=$arr[1]
    }

    public function testFunctions() {
        $array = array(10, 20, 30, 40);

        array_pop($array);
        self::assertEquals(array(10, 20, 30), $array);

        $array = array_splice($array, 0, 2);
        self::assertEquals(array(10, 20), $array);

        $array = array(10, 20, 30, 40, 50);
        $removed = array_splice($array, $offset = 2, $count = 2, array("a"));
        self::assertEquals(array(30, 40), $removed);
        self::assertEquals(array(10, 20, "a", 50), $array);

        $array = array(10, 20, 30);
        unset($array[1]);
        self::assertEquals(array(10, 2 => 30), $array);

        array_push($array, 50);
        self::assertEquals(array(10, 2 => 30, 3 => 50), $array);
    }

    public function testFunction2() {
        $a = array(1, 2);
        array_pad($a, 2, '5'); // keine Änderung!
        self::assertEquals(array(1, 2), $a);

        $a = array_pad($a, 4, '5');
        self::assertEquals(array(1, 2, 5, 5), $a);
    }

    public function testComplicated() {
        $array = array(1 => 0, 2, 3, 4);
        array_splice($array, 3, count($array), array_merge(array('x'), array_slice($array, 3)));
        //                                                x,                  4
        //     0, 2, 3, x, 4 (keys gehen verloren)
        self::assertEquals(array(0, 2, 3, 'x', 4), $array);


        // step by step
        $array = array(1 => 0, 2, 3, 4);

        $a = array_slice($array, 3);
        self::assertEquals(array(4), $a);

        $a = array_merge(array('x'), $a);
        self::assertEquals(array('x', 4), $a);

        $merge = array("x", "y", "z", 44 => "4"); // keys gehen verloren!
        $keys_removed = array_splice($array, 3, count($array), $merge);
        self::assertEquals(array(4), $keys_removed);
        self::assertEquals(array(0, 2, 3, 'x', 'y', 'z', 4), $array);
    }

    public function testCurly() {
        $a{4} = "a";
        // $a{} = "x"; geht nicht!
        $a[5] = "b";
        $a[] = "c";
        self::assertEquals(array(4 => "a", 5 => "b", 6 => "c"), $a);
    }

    public function testSorts() {
        // asort - ascending by-value of Associative arrays
        $a = array(2 => "u", 4 => "b", 1 => "f");
        asort($a);
        self::assertEquals(array(4 => "b", 1 => "f", 2 => "u"), $a);

        // ksort - ascending by-Key of assiciative arrays
        $a = array(2 => "u", 4 => "b", 1 => "f");
        ksort($a);
        self::assertEquals(array(1 => "f", 2 => "u", 4 => "b"), $a);
    }

    public function testExplode() {
        $a = explode("", "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        self::assertFalse($a);
    }

    public function testSplit() {
        $string = "aaa-=-bbb-=-ccc";
        $soll = array("aaa", "bbb", "ccc");
        // self::assertEquals($soll, str_split($string, strpos($string, "-=-"))); überspring das 1.
        // self::assertEquals($soll, preg_split("-=-", $string)); // hat die "/" vergessen!
        self::assertEquals($soll, explode("-=-", $string));
    }

    public function testArrayFuncs() {
        $a = array(3, null, false, 5);

        $s = "";
        end($a);
        while (!is_null($key = key($a))) {
            $val = current($a);
            $s .= "$key => " . var_export($val, 1) . ", ";
            prev($a);
        }

        self::assertEquals("3 => 5, 2 => false, 1 => NULL, 0 => 3, ", $s);
    }

    public function testArrayMap() {
        $a = array('test1', 'test2');
        $b = array_map('Array_Test::myStaticFunc', $a);
        self::assertEquals(array('TEST1', 'TEST2'), $b);
    }

    public static function myStaticFunc($x) {
        return strtoupper($x);
    }
}
