<?php

require_once 'log4php/Logger.php';
require_once 'DaliSsh.class.php';

class DaliSsh_Test extends PHPUnit_Framework_TestCase {

    public function testShell() {
        $host = getenv("UNITTEST_HOST");
        $user = getenv("UNITTEST_USER");
        $pass = getenv("UNITTEST_PASS");
        if (empty($host) or empty($user) or empty($pass)) {
            self::markTestSkipped("Env vars UNITTEST_* not set!");
            return;
        }

        $dali = null;
        try {
            // Connect and login
            $dali = new DaliSsh($host, 22, 5);
            $dali->activateLog4php(LoggerLevel::getLevelDebug());
            $dali->login($user, $pass, '$ ');

            // Do a little chit chat
            $dali->exec('calc', '; ');
            $result = $dali->exec("2*3", '; ');
            self::assertEquals("2*3\r\n\t6\r\n; ", $result);

            // Check if anything happend on stderr.
            $stderrOutput = $dali->getStderr();
            self::assertEmpty($stderrOutput);
        } catch (Exception $e) {
            self::fail("Exception $e with stderr: " . $dali->getGlobalBuffer());
        }
    }

}

