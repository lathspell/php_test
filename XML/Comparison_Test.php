<?php

class Comparison_Test extends PHPUnit_Framework_TestCase {

    public function test1() {
        $sx = simplexml_load_file(__DIR__."/example.xml");
        self::assertEquals("artikel6", (string)$sx->artikel[2]["id"]);
        self::assertEquals("60", (string)$sx->artikel[2]->price);

        $dom = dom_import_simplexml($sx);
        self::assertEquals("artikel6", $dom->getElementsByTagName("artikel")->item(2)->attributes->getNamedItem("id")->value);
        self::assertEquals("60", $dom->getElementsByTagName("artikel")->item(2)->getElementsByTagName("price")->item(0)->nodeValue);

        $domdoc = new DOMDocument();
        $domdoc->load(__DIR__."/example.xml");
        $xpath = new DOMXPath($domdoc);
        self::assertEquals("artikel6", $xpath->query("//artikel[@id='artikel6']/@id")->item(0)->nodeValue);
        self::assertEquals("60", $xpath->query("//artikel[@id='artikel6']/price")->item(0)->nodeValue);
    }

}