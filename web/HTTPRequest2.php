<?php

require_once 'HTTP/Request2.php';

$request = new HTTP_Request2('http://www.lathspell.de/', HTTP_Request2::METHOD_GET);

$response = $request->exec();
if (200 == $response->getStatus()) {
    print_r($response->getHeader());
    echo $response->getBody();
} else {
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase();
}
