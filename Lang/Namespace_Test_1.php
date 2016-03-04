<?php

namespace lathspell;

class Lath1 {

}

namespace lathspell\test;

class LathTest1 {

}

namespace foo;

class Namespace_Test extends \PHPUnit_Framework_TestCase {

    public function test1() {
        new \lathspell\Lath1();
        new \lathspell\test\LathTest1();
    }

}

namespace foo2;
use \PHPUnit_Framework_TestCase;

class Namespace2_Test extends PHPUnit_Framework_TestCase {

    public function test2() {
        $a = new \lathspell\Lath1();
        $b = new \lathspell\test\LathTest1();
        self::assertNotNull($a);
        self::assertNotNull($b);
    }

}
