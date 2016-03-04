<?php

class UTF8_Bug {
    /**
     * Täßt
     */
    public function test() {   
    }
}

set_include_path(get_include_path() . ':/home/james/workspace/ZendFramework/library/');
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

$autodiscover = new Zend_Soap_AutoDiscover();
$autodiscover->setUri('uri:test');
$autodiscover->setClass('UTF8_Bug');
echo $autodiscover->toXml();
