<?php

class fraction {

    var $numerator;
    var $denominator;

    function fraction($n, $d) {
        $this->set_numerator($n);
        $this->set_denominator($d);
    }

    function set_numerator($num) {
        $this->numerator = (int) $num;
    }

    function set_denominator($num) {
        $this->denominator = (int) $num;
    }

    function to_string() {
        return "{$this->numerator} / {$this->denominator}";
    }
}

function gcd($a, $b) {
    return ($b > 0) ? gcd($b, $a % $b) : $a;
}

function reduce_fraction($fraction) {
    $gcd = gcd($fraction->numerator, $fraction->denominator);
    $fraction->numerator /= $gcd;
    $fraction->denominator /= $gcd;
}

$eight_tenths = new fraction(8, 10);
/* Reduce the fraction */
reduce_fraction($eight_tenths);

var_dump($eight_tenths->to_string());
