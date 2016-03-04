<?php

class MyServer {

    public function __construct() {
        echo "MyServer starting...";
    }

    public function greeter($name) {
        return "Hello $name!";
    }

    public function kaputt() {
        throw new Exception("Kaputt!");
    }

}

$log->info("Accepting connection to ".$_SERVER['REQUEST_URI']." from ".$_SERVER['REMOTE_ADDR']);
$tmp_rpd = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
$log->debug("Request:\n".print_r($tmp_rpd, 1));

$server = new SoapServer(null, array('uri' => 'uri:lathspell'));
$server->setClass('MyServer');

try {
    ob_start();
    $server->handle();
    $response = ob_get_flush();
    // $log->info($response);
} catch (Exception $e) {
    // $log->info($response);
    $server->fault('Receiver', $e->getMessage());
}