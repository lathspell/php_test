<?php
/*
 * SimpleXML ist nur f�r wirklich simple Dinge brauchbar. Sobald Namespace, also "<foo:bar" Notation
 * ins Spiel kommt, versagt sie. DOM ist auch nicht schwieriger und l��t sich genauso einfach
 * mit XPath verbinden.
 */

class SimpleXML_Test extends PHPUnit_Framework_TestCase {
    private $xml,  $sxe;
    private $xml2, $sxe2;
    
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
 <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/thumbnail,.png"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="settings.xml"/>
</manifest:manifest>
EOT;
        $this->sxe = simplexml_load_string($this->xml);
        
        $this->xml2 = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>        
<books>
   <book id="bk101">
      <author>Mustermann, Max</author>
      <title>XML Developer's Guide</title>
      <genre>Computer</genre>
      <price>43.95</price>
   </book>
   <book id="bk102">
      <author>Doe, John</author>
      <title>Some Rain</title>
      <genre>Fantasy</genre>
      <price>1.95</price>
   </book>
</books>
EOT;
        $this->sxe2 = simplexml_load_string($this->xml2);
    }

    
    public function testBasic() {
        $this->assertTrue($this->sxe instanceof SimpleXMLElement);
        $this->assertTrue(abs(strlen($this->xml) - strlen($this->sxe->asXML())) < 10);
    }
    
    public function testXpath() {
        // find all book records with price higher than 40$
        $results = $this->sxe2->xpath("book/price[.>'40']/parent::*");

        // Der Cast zu String ist wichtig, sonst gibt es ein Objekt zur�ck!
        $this->assertEquals((string)$results[0]['id'], 'bk101');
        
        // Bei Default-Namespaces:
        //   $this->sx->xpath('//*[local-name()="leihender"]/*[local-name()="name"]');
        // oder
        //   $this->sx->registerXPathNamespace("tmp", "http://www.example.org/verleihliste");
        //   $this->sx->xpath('//tmp:leihender/tmp:name');
    }
    
    public function testNamespace() {
        $sxe_manifest = $this->sxe->children('http://openoffice.org/2001/manifest');
        $this->assertTrue($sxe_manifest instanceof SimpleXMLElement);
        
        $sxe_entries = $sxe_manifest->xpath('//manifest:file-entry[@manifest:media-type="text/xml"]');
        $this->assertTrue(is_array($sxe_entries));
        $this->assertEquals(count($sxe_entries), 4);

        $entry2 = $sxe_entries[2];
        $entry2_attributes = $entry2->attributes('http://openoffice.org/2001/manifest');
        
        $this->assertEquals((string)$entry2_attributes['full-path'], 'meta.xml');
    }
    
}
