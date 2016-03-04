<?php

class MyServer {

    public function __construct() {
        echo "MyServer starting...";
    }

    /**
     * Liefert Begrüßung.
     *
     * Docblocks werden für die automatische WSDL Generierung benutzt!
     *
     * @param string $name
     * @return string
     */
    public function greeter($name) {
        return "Hello $name!";
    }

    public function kaputt() {
        throw new Exception("Kaputt!");
    }

}

set_include_path(get_include_path().':/home/james/workspace/ZendFramework/library/');
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

if (isset($_GET['wsdl'])) {
    $autodiscover = new Zend_Soap_AutoDiscover();
    $autodiscover->setUri('uri:lathspell');
    // $autodiscover->setOperationBodyStyle(array('use' => 'literal'));
    // $autodiscover->setBindingStyle(array('style' => 'document'));
    $autodiscover->setClass('MyServer');
    echo $autodiscover->toXml();
    $autodiscover->handle();
} else {
    $wsdl_uri = 'http://localhost' . $_SERVER['PHP_SELF'] . '?wsdl';
    $soap = new Zend_Soap_Server($wsdl_uri);
    $soap->setClass('MyServer');
    try {
        ob_start();
        $soap->handle();
        $response = ob_get_flush();
    } catch (Exception $e) {
        $response = ob_get_flush();
        $server->fault('Receiver', "Got Exception: " . $e->getMessage() . "\nSo far:\n$response");
    }
}

