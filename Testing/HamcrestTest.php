<?php

namespace Testing;

use Hamcrest\Util;
use PHPUnit\Framework\TestCase;
use function assertThat;
use function is;
use function not;

class HamcrestTest extends TestCase
{

    /** @beforeClass */
    public static function beforeClass()
    {
        Util::registerGlobalFunctions();
    }

    public function testArrays()
    {
        $a = [1, 3, 4];
        $aa = [1, 3, 4];
        $b = [1, 5, 4];

        assertThat($a, is($aa));
        assertThat($a, not(is($b)));
    }

    public function testFailing1()
    {
        $a = [1, 3, 4];
        $b = [1, 5, 4];
        assertThat($a, is($b));
    }

    public function testFailing2()
    {
        $a = [1, 3, 4];
        $b = [1, 5, 4];
        assertThat($a, is(anArray($b)));
    }

    public function testFailing3()
    {
        $a = [1, 3, 4];
        $b = [1, 5, 4];
        assertThat($a, equalTo($b));
    }

    public function testFailing4()
    {
        $a = [1, 3, 4];
        $b = [1, 5, 4];
        self::assertEquals($b, $a);
    }
}
