<?php

class Date_Test extends PHPUnit_Framework_TestCase {
    public function test1() {
        $iso0 = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 5, date("Y"))); // ugly
        $iso1 = date('Y-m-d', strtotime('-5 days')); // nice
        self::assertEquals($iso0, $iso1);
    }

    public function test2()
    {
        $actual = date('d. M Y', strtotime('-5 days', strtotime('2016-11-21 14:23:22')));
        self::assertEquals("16. Nov 2016", $actual);
    }
}
