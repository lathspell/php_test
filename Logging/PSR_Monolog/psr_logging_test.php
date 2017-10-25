#!/usr/bin/php
<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Foo.php';

use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;

$_SERVER['REQUEST_URI'] = '/foo'; // simulating web request (WebProcessor actually checks for this one)
$_SERVER['REMOTE_ADDR'] = '1.2.3.4'; // simulating web request

// The "main" logger uses String output
$consoleHandler = new StreamHandler('php://stderr');
$consoleHandler->setFormatter(new LineFormatter("[%datetime%] [REMOTE_ADDR=%extra.ip%] %channel%.%level_name%: %message%\n"));

$mainLogger = new Logger("main");
$mainLogger->pushProcessor(new WebProcessor()); // adds "extra.ip" from $_SERVER['REMOTE_ADDR'] and others
$mainLogger->pushHandler($consoleHandler);

// The "infoFile" logger uses JSON output
$infoFileHandler = new StreamHandler('info.log', Logger::INFO);
$infoFileHandler->setFormatter(new JsonFormatter());

$infoFileLogger = new Logger("infoFile");
$infoFileLogger->pushHandler($infoFileHandler);

// Log from main
$mainLogger->info("initialized");
$infoFileLogger->info("initialized");

// Create class that uses its own (unconfigured) logger
$foo = new Foo();
$foo->bar();
