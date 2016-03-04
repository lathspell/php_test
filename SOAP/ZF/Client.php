<?php

$client = new SoapClient(null, array('location'=>"http://localhost/php_test/web/SOAP/ZF/Server.php", 'uri'=>'uri:lathspell', 'trace'=>true));
try {


    $response = $client->__soapCall("greeter", array("name" => "James"));
    echo "REPONSE: $response\n";

    $response = $client->__soapCall("greeter", array("James"));
    echo "REPONSE: $response\n";

    $response = $client->greeter("James");
    echo "REPONSE: $response\n";

    $response = $client->kaputt();
    echo "REPONSE: $response\n";

} catch (Exception $e) {
    echo $e->getMessage()."\n";
    echo $e->getTraceAsString()."\n\n";
    echo $client->__getLastRequestHeaders();
    echo $client->__getLastRequest();
    echo "\n";
    echo $client->__getLastResponseHeaders();
    echo $client->__getLastResponse();
    echo "\n";
}