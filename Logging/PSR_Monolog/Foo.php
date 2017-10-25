<?php

use Psr\Log\LoggerInterface;
use Monolog\Logger;

class Foo {

    /** @var LoggerInterface Logger */
    private $log;

    public function __construct()
    {
        // Caveat: This is a totally new Logger which does not inherit anything!
        $this->log = new Logger(__CLASS__);
        $this->log->info("ctor");
    }

    public function bar() {
        $this->log->info("bar");
    }

}