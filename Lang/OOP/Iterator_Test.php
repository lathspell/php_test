<?php

class Test implements Iterator, ArrayAccess, Countable {

    public $data = array(10, 20, 30, 40, 50);
    private $pos = 0;

    // Iterator

    public function rewind() {
        $this->pos = 0;
    }

    public function current() {
        return $this->data[$this->pos];
    }

    public function key() {
        return $this->pos;
    }

    public function next() {
        $this->pos++;
    }

    public function valid() {
        return isset($this->data[$this->pos]);
    }

    // ArrayAccess

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function offsetSet($offset, $value) {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    // Countable

    public function count() {
        return count($this->data);
    }

    // my

    public function __toString() {
        return "Enthaelt " . join(",", $this->data);
    }

}

class Iterator_Test extends PHPUnit_Framework_TestCase {

    public function testObjectIterator() {
        // In PHP 4 you could iterate over every member property of an object
        // using foreach(), in PHP 5 to accomplish the same task on a
        // non-public array you could use the ___________ interface.


        $o = new Test();
        self::assertEquals(0, $o->key());
        self::assertEquals("Enthaelt 10,20,30,40,50", (string) $o);

        // Iterating
        $found = array();
        foreach ($o as $i) {
            $found[] = $i;
        }
        self::assertEquals(array(10, 20, 30, 40, 50), $found);

        // ArrayAccess
        $o[] = 60;
        self::assertEquals("Enthaelt 10,20,30,40,50,60", (string)$o);
        $o[1] = 22;
        self::assertEquals("Enthaelt 10,22,30,40,50,60", (string)$o);
        unset($o[4]);
        self::assertEquals("Enthaelt 10,22,30,40,60", (string)$o);
        self::assertTrue(isset($o[5]));
    }

}
