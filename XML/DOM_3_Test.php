<?php

class DOM_3_Test extends PHPUnit_Framework_TestCase {

    public function test1a() {
        $dom = new DOMDocument();

        $head = $dom->createElement("head");
        $title = $dom->createElement('title');

        // What should ??????? be replaced with to add a <title> node with the value of Hello, World!
        // $node = ????????
        $node = $dom->createTextNode("Hello, World!");

        $title->appendChild($node);
        $head->appendChild($title);

        $dom->appendChild($head);

        $xml = $dom->saveXML();

        self::assertEquals('<?xml version="1.0"?>' . "\n" . '<head><title>Hello, World!</title></head>' . "\n", $xml);
    }

    public function test1b() {
        $dom = new DOMDocument();
        $dom->appendChild($head = $dom->createElement("head"));
        $head->appendChild($title = $dom->createElement("title", "Hello, World!"));
        self::assertEquals('<?xml version="1.0"?>' . "\n" . '<head><title>Hello, World!</title></head>' . "\n", $dom->saveXML());
    }

    public function test2() {
        $dom = new DomDocument();
        $dom->load(__DIR__.'/example.xml');
        $xpath = new DomXPath($dom);
        $nodes = $xpath->query("*[local-name()='artikel']" /*, $dom->documentElement */);
        self::assertEquals("artikel4", $nodes->item(0)->getAttributeNode('id')->value);
    }

}