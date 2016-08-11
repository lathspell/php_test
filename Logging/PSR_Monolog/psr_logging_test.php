#!/usr/bin/php
<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Foo.php';

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Formatter\JsonFormatter;

// Setup Monolog
$consoleHandler = new ErrorLogHandler();
$consoleHandler->setFormatter(new JsonFormatter());

$log = new Logger("main");
$log->pushHandler($consoleHandler);

// Log from main
$log->info("initialized");

// Create class that uses the PSR/Log interface
$foo = new Foo();
$foo->bar();
$foo->useGlobalLog();
