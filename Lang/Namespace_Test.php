<?php

namespace lathspell;

class Lath1 {

}

namespace lathspell\test;

class LathTest1 {

}

namespace foo2;
use \PHPUnit_Framework_TestCase;
use \lathspell\Lath1; // wo ist das .* ???
use \lathspell\test\LathTest1; // wo ist das .* ???

class Namespace2_Test extends PHPUnit_Framework_TestCase {

    public function test2() {
        $a = new Lath1();
        $b = new LathTest1();
        self::assertNotNull($a);
        self::assertNotNull($b);
    }

}
