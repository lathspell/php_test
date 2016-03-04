<?php

class DOM_2_Test extends PHPUnit_Framework_TestCase {

    public function testLoad() {
        $xml = new DOMDocument();
        $xml->load(__DIR__ . "/example.xml");

        self::assertTrue($xml instanceof DOMDocument);
    }

    public function testPrinting() {
        $xml = new DOMDocument();
        $xml->load(__DIR__ . "/example.xml");
        $xml_string = $xml->saveXML();

        $orig = file_get_contents(__DIR__ . "/example.xml");
        $orig = preg_replace("/\n\n/", "\n", $orig);

        self::assertEquals($orig, $xml_string);
    }

    public function testXPath() {
        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false; // sonst Spaces im nodeValue!!!
        $xml->load(__DIR__ . "/example.xml");

        $xpath = new DOMXPath($xml);
        $artikel7 = $xpath->query("//artikel[price>60]")->item(0);

        self::assertEquals('artikel7', $artikel7->attributes->getNamedItem('id')->value);
        self::assertEquals(70, $artikel7->nodeValue);
    }

    public function testZugriff() {
        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false; // sonst Spaces im nodeValue!!!
        $xml->load(__DIR__ . "/example.xml");
        self::assertTrue($xml->validate());

        // getElementByTagName
        $artikel = $xml->getElementsByTagName("artikel");
        $artikel7 = $artikel->item(3);
        self::assertEquals(70, $artikel7->nodeValue);

        // getElementById
        $artikel7 = $xml->getElementById('artikel7');
        self::assertEquals(70, $artikel7->nodeValue); // uhh... vorsicht!
        self::assertEquals('artikel7', $artikel7->attributes->getNamedItem('id')->value);
        self::assertEquals('artikel', $artikel7->tagName);
    }

    public function testCreate() {
        $soll = '<root><sub></sub><sub id="sub2"><subsub>42</subsub></sub></root>'."\n";

        $xml = new DOMDocument("1.0", "UTF-8");
        $xml->appendChild($root = new DOMElement('root'));
        $root->appendChild($sub1 = new DOMElement("sub"));
        $root->appendChild($sub2 = new DOMElement("sub"));
        $root->appendChild($sub3 = new DOMElement("sub"));
        $sub2->setAttribute("id", "sub2");
        $sub2->appendChild($subsub = new DOMElement("subsub", "42"));

        // korrektur
        $root->removeChild($sub3);

        // saveHTML setzt keinen XML Header davor
        self::assertEquals($soll, $xml->saveHTML());
    }

    public function testMixWithSimpleXML() {
        $xml = new DOMDocument();
        $xml->load(__DIR__ . "/example.xml");

        $sx = simplexml_import_dom($xml);
        self::assertEquals(60, (string)$sx->artikel[2]->price);
    }
}
