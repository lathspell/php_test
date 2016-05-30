<?php

/** Random Numbers as recommended on https://paragonie.com/blog/2016/05/how-generate-secure-random-numbers-in-various-programming-languages#java-csprng */
class Random_Test extends PHPUnit_Framework_TestCase {

    /** PHP7 builtin or via paragonie/random_compat dependency. */
    public function testRandomWithPhp7() {
        if (version_compare(phpversion(), '7.0.0', '<')) {
            self::markTestSkipped("PHP7 required");
        }

        $string = random_bytes(32);
        self::assertNotNull($string);
        $int = random_int(0, PHP_INT_MAX);
        $int2 = random_int(0, PHP_INT_MAX);
        self::assertNotNull($int);
        self::assertNotNull($int2);
        self::assertNotEquals($int, $int2);
    }

}
