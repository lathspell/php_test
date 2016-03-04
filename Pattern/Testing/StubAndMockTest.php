<?php

class Foo {

    public function getData() {
        return $this->produceData();
    }

    protected function produceData() {
        return "foodata";
    }

}

class StubAndMockTest extends PHPUnit_Framework_TestCase {

    public function testOriginal() {
        $foo = new Foo();

        self::assertEquals("foodata", $foo->getData());
    }

    /** Bei einem Mock wird die ganze Klasse ersetzt. */
    public function testWithMockClass() {
        $foo = $this->getMock('Foo');
        $foo->expects($this->any())
            ->method('produceData')
            ->will($this->returnValue('bardata'));

        self::assertEquals("bardata", $foo->getData());
    }

    /** Bei einem Stub wird nur eine einzelne Methode ausgetauscht. */
    public function testWithStubMethod() {
        $foo = $this->getMock('Foo', array('produceData'));
        $foo->expects($this->once())
            ->method('produceData')
            ->will($this->returnValue('bardata'));

        self::assertEquals('bardata', $foo->getData());
    }
}
