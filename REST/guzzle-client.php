<?php
namespace lathspell\test;

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use function GuzzleHttp\Psr7\str;

$client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'http://example.com',
    // You can set any number of default request options.
    'timeout' => 2.0,
    ]);

try {
    echo "Requesting...\n";
    $response = $client->request('GET', 'test', ['query' => ['foo' => 'bar']]);
} catch (ClientException $e) {
    echo "Exception: " . $e->getCode() . " " . $e->getMessage() . ":\n";
    echo "----\n";
    echo str($e->getRequest());
    echo "----\n";
    if ($e->hasResponse()) {
        echo str($e->getResponse());
    }
    exit(1);
}

print_r($response);

$code = $response->getStatusCode(); // 200
$reason = $response->getReasonPhrase(); // OK
print("RESULT: $code $reason\n");

foreach ($response->getHeaders() as $name => $values) {
    echo "HEADER: " . $name . ': ' . implode(', ', $values) . "\r\n";
}

$body = $response->getBody();
print("BODY:\n$body\n");

