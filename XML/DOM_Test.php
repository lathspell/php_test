<?php
/*
 * XML Zugriff mit DOM und XPath.
 *
 * Die bevorzugte Methode für PHP5. Auf jeden Fall besser als SimpleXML,
 * insbesondere sobald Namespace ins Spiel kommen!
 */

class DOM_Test extends PHPUnit_Framework_TestCase {
    private $xml,  $dom;
    private $xml2, $dom2;

    public function setUp() {
        $this->xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE manifest:manifest PUBLIC "-//OpenOffice.org//DTD Manifest 1.0//EN" "Manifest.dtd">
<manifest:manifest xmlns:manifest="http://openoffice.org/2001/manifest">
 <manifest:file-entry manifest:media-type="application/vnd.sun.xml.writer" manifest:full-path="/"/>
 <manifest:file-entry manifest:media-type="application/vnd.sun.xml.ui.configuration" manifest:full-path="Configurations2/"/>
 <manifest:file-entry manifest:media-type="application/binary" manifest:full-path="layout-cache"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="styles.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="meta.xml"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/thumbnail.png"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="settings.xml"/>
</manifest:manifest>
EOT;
        $this->dom = new DOMDocument();
        $this->dom->loadXML($this->xml);

        $this->xml2 = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>        
<books>
   <book id="bk101">
      <author>Mustermann, Max</author>
      <title>Some fancy title</title>
      <genre>Computer</genre>
      <price>44.35</price>
   </book>
   <book id="bk102">
      <author>Doe, John</author>
      <title>Some Rain</title>
      <genre>Fantasy</genre>
      <price>1.95</price>
   </book>
</books>
EOT;

        $this->dom2 = new DOMDocument();
        // We need to validate our document before refering to the id
        $this->dom2->validateOnParse = true;
        $this->dom2->loadXML($this->xml2);

        // Without namespace
        $this->xml3 = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>        
<books xmlns="http://example.com/test">
   <book>
      <author>Mustermann, Max</author>
      <title>Some fancy title</title>
      <genre>Computer</genre>
      <price>44.35</price>
   </book>
</books>
EOT;

        $this->dom3 = new DOMDocument();
        // We need to validate our document before refering to the id
        $this->dom3->validateOnParse = true;
        $this->dom3->loadXML($this->xml3);
    }


    public function testBasic() {
        $this->assertTrue($this->dom instanceof DOMDocument);
        $this->assertTrue(abs(strlen($this->xml) - strlen($this->dom->saveXML())) < 10);
    }

    public function testIterating() {
        $books = $this->dom2->getElementsByTagName("book");
        $this->assertTrue($books instanceof DOMNodeList);

        foreach ($books as $book) {
            $this->assertTrue($book instanceof DOMNode);

            $title = $book->getElementsByTagName('title')->item(0);
            $this->assertTrue($title instanceof DOMNode);
        }

        $title1 = $books->item(1)->getElementsByTagName('title')->item(0)->nodeValue;
        $this->assertEquals($title1, 'Some Rain');
    }

    public function testXpath() {
        // find all book records with price higher than 40$
        $xp = new DOMXPath($this->dom2);
        $books = $xp->query("book/price[.>'40']/parent::*");
        $this->assertTrue($books instanceof DOMNodeList);

        $book0 = $books->item(0);
        $this->assertTrue($book0 instanceof DOMElement);
         
        $book0_attribute_id = $book0->getAttribute('id');
        $this->assertEquals($book0_attribute_id, 'bk101');

        // It IS possible without the item(0)! Without the string() cast a DOMNodeList would be returned.
        $this->assertEquals("Some fancy title", $xp->evaluate('string(//books/book[@id="bk101"]/title)'));
        $this->assertEquals(false, $xp->evaluate('string(//books/book[@id="bk666"]/title)'));

        // Nur das Attribut abfragen
        $this->assertEquals('bk101', $xp->evaluate('string(//books/book[@id="bk101"]/@id)'));
    }
    
    public function testXpathWithDefaultNamespace() {
        // Caveat: default namespaces have to be mapped to some temporary one or query() will never match
        $xp = new DOMXPath($this->dom3);
        $genre = $xp->query('//book/genre');
        self::assertEquals(0, $genre->length);

        $xp->registerNamespace("tmp", "http://example.com/test");
        $genres = $xp->query('//tmp:book/tmp:genre');
        self::assertTrue($genres instanceof DOMNodeList);
        self::assertEquals(1, $genres->length);
        $genre0 = $genres->item(0);        
        self::assertTrue($genre0 instanceof DomElement);
        self::assertEquals("Computer", $genre0->textContent);
        
        // or if not otherwise possible:
        $genres = $xp->query('//*[local-name()="book"]/*[local-name()="genre"]');
        self::assertEquals("Computer", $genres->item(0)->textContent);
    }

    public function testNamespace() {
        $manifest_uri = $this->dom->lookupNamespaceUri('manifest');
         
        $xp = new DOMXPath($this->dom);
        $hits = $xp->query('//manifest:file-entry[@manifest:media-type="text/xml"]');
        $this->assertTrue($hits instanceof DOMNodeList);

        $hit2 = $hits->item(2);
        $this->assertTrue($hit2 instanceof DOMElement);

        $hit2_attribute = $hit2->getAttributeNS($manifest_uri, 'full-path');
        $this->assertEquals($hit2_attribute, 'meta.xml');
    }
}
