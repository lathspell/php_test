<?php

class SimpleXML_3_Test extends PHPUnit_Framework_TestCase {

    public function test1() {

        // Given the following XML document in a SimpleXML object:
        $xml = <<<EOT
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>XML Example</title>
    </head>
    <body>
        <p>
            Moved to &lt;<a href="http://example.org/">http://www.example.org/</a>.&gt;
            <br/>
        </p>
    </body>
</html>
EOT;

        /*
          Select the proper statement below which will display the HREF attribute of the anchor tag.

          Answer...
          $sxe->body->p[0]->a[1]['href']
          $sxe->body->p->a->href
          $sxe->body->p->a['href']
          $sxe['body']['p'][0]['a']['href']
          $sxe->body->p[1]->a['href']
         */
        $sxe = simplexml_load_string($xml);
        self::assertTrue($sxe instanceof SimpleXMLElement);
        self::assertEquals("http://example.org/", (string) $sxe->body->p->a['href']);
    }

    public function test2() {

// Given the following PHP script:



        $xmldata = <<< XML
<?xml version="1.0" encoding="ISO-8859-1" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>XML Example</title>
  </head>
  <body>
    <p>
      <b>Hello, World!</b>
    </p>
  </body>
</html>
XML;

        $sxe = simplexml_load_string($xmldata);

        $p = $sxe->body->p;

        /*
          $string = ????????

          What should go in place of ????? above to print the string Hello, World! (with no leading/trailing whitespace or markup)?

          Answer...
          trim(($p[1]));
          trim(strip_tags(($p->asText())));
          trim(strip_tags(($p->asXML())));
          trim(($p->asXML()));
          strip_tags(($p->asXML()));
         */
        self::assertEquals("Hello, World!", trim(strip_tags($p->asXML())));
        self::assertEquals("Hello, World!", trim($p->b));
        self::assertEquals("Hello, World!", (string)$p->b);
    }

}
