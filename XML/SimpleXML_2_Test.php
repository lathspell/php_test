<?php

/** SimpleXML sollte nur zum Zugriff auf simple XML Dateien benutzt werden.
 *
 * Das Erstellen/Modifizieren und insbesondere Entfernen von Attributen und
 * Elementen ist umständlich oder nicht möglich.
 */
class SimpleXML_2_Test extends PHPUnit_Framework_TestCase {

    public function testLoad() {
        $xml = simplexml_load_file(__DIR__."/example.xml");
        $xml2 = new SimpleXMLElement(__DIR__."/example.xml", null, $is_file=true);
        self::assertEquals($xml, $xml2);
        self::assertTrue($xml instanceof SimpleXMLElement);
    }

    public function testPrint() {
        $xml = new SimpleXMLElement(__DIR__."/example.xml", null, true);
        $xml_string = file_get_contents(__DIR__."/example.xml");
        $xml_string = preg_replace("/\n\n/s",  "\n", $xml_string);
        self::assertEquals($xml_string, $xml->asXML());
    }

    public function testCreating() {
        $soll = "<?xml version=\"1.0\"?>\n".
                "<root>\n".
                "<sub>42</sub>\n".
                "<sub id=\"2\">21</sub>\n".
                "</root>\n";
        $soll = preg_replace("/[\n]/", "", $soll);

        $root = new SimpleXMLElement("<root></root>"); // geht nicht anders!
        $sub = $root->addChild("sub", 42);
        $sub2 = $root->addChild("sub", 21);
        $sub2->addAttribute("id", 2);
        $ist = preg_replace("/[\n]/", "", $root->asXML());

        self::assertEquals($soll, $ist);
    }


    public function testAccessing() {
        $xml = new SimpleXMLElement(__DIR__."/example.xml", null, true);
        self::assertEquals(60, (string) $xml->artikel[2]->price);
    }

    public function testXPath() {
        $xml = new SimpleXMLElement(__DIR__."/example.xml", null, true);

        $result = $xml->xpath("//artikel[price>=70]/price");
        self::assertEquals(1, count($result));
        self::assertEquals(70, (string)$result[0]);

        $result = $xml->xpath("//artikel[price>=70]");
        $attrs = $result[0]->attributes();
        self::assertEquals('artikel7', (string)$attrs['id']);
    }
}
