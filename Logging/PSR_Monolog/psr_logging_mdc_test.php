#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Foo.php';

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$consoleHandler = new StreamHandler('php://stderr');
$consoleHandler->setFormatter(new LineFormatter("[%datetime%] [MDC=%extra.my_mdc%] %level_name% %channel%: %message%\n"));

$mainLogger = new Logger("main");
$mainLogger->pushProcessor(function (array $record) {
    $record['extra']['my_mdc'] = $GLOBALS['myMDC'];
    return $record;
});
$mainLogger->pushHandler($consoleHandler);

// Log from main
$myMDC = null;
$mainLogger->info("one");
$myMDC = '222222';
$mainLogger->info("two");
$mainLogger->info("two two");
$mainLogger->info("two two two");
$myMDC = '333333';
$mainLogger->info("three");
