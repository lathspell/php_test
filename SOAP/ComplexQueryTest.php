<?php

class ComplexQueryTest extends PHPUnit_Framework_TestCase {

    public function testComplexQuery() {

        $deviceID = "MyHostname*";
        $portID = "1.2";

        $soll = <<<EOT
            <?xml version="1.0" encoding="UTF-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:Inventory" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
              <SOAP-ENV:Body>
                <ns1:getPorts>
                  <Map xsi:type="SOAP-ENC:Struct">
                    <item xsi:type="SOAP-ENC:Struct">
                      <key xsi:type="xsd:string">deviceID</key>
                      <value xsi:type="xsd:string">MyHostname*</value>
                    </item>
                    <item xsi:type="SOAP-ENC:Struct">
                      <key xsi:type="xsd:string">portID</key>
                      <value xsi:type="xsd:string">1.2</value>
                    </item>
                  </Map>
                </ns1:getPorts>
              </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>
EOT;

        // Build request and send to dummy host.
        $soap = new SoapClient(null, array('location' => "dummy.local.", 'uri' => 'urn:Inventory', 'trace' => true));
        $item1 = new SoapVar(array('key' => 'deviceID', 'value' => $deviceID), SOAP_ENC_OBJECT, null, null, 'item');
        $item2 = new SoapVar(array('key' => 'portID', 'value' => $portID), SOAP_ENC_OBJECT, null, null, 'item');
        $map = new SoapVar(array($item1, $item2), SOAP_ENC_OBJECT, null, null, "Map");
        $request = null;
        try {
            $soap->getPorts($map);
        } catch (Exception $e) {
            // ignore
        }
        $ist = $soap->__getLastRequest();

        // compare
        self::assertEquals(self::formatXML(trim($soll)), self::formatXML($ist));
    }

    /** Returns XML nicely formatted.
     *
     * @param string $xml
     * @return string
     */
    private static function formatXML($xml) {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($xml);
        return $doc->saveXML();
    }

}

