<?php

require_once '/usr/share/php/log4php/Logger.php';

class Net_SSH2_Test extends PHPUnit_Framework_TestCase {

    /** @var Logger */
    private $log;

    /** @var resource */
    private $stdio;

    /** @var resource */
    private $stderr;

    public function setUp() {
        $this->log = Logger::getRootLogger();
        $this->log->setLevel(LoggerLevel::getLevelWarn());
    }

    public function testShell() {
        $host = getenv("UNITTEST_HOST");
        $user = getenv("UNITTEST_USER");
        $pass = getenv("UNITTEST_PASS");
        if (empty($host) or empty($user) or empty($pass)) {
            self::markTestSkipped("Env vars UNITTEST_* not set!");
            return;
        }

        // connect, authenticate and request a shell
        $this->log->info("Connecting to $host");
        $conn = ssh2_connect($host, 22, array());
        self::assertTrue(is_resource($conn));

        $this->log->info("Authenticating as $user");
        $ret = ssh2_auth_password($conn, $user, $pass);
        self::assertTrue($ret);

        $this->log->info("Opening shell");
        $this->stdio = ssh2_shell($conn);
        self::assertTrue(is_resource($this->stdio));

        $this->log->info("Fetch stderr");
        $this->stderr = ssh2_fetch_stream($this->stdio, SSH2_STREAM_STDERR);
        self::assertTrue(is_resource($this->stderr));

        $this->log->info("Configuring streams");
        stream_set_blocking($this->stdio, false);
        stream_set_timeout($this->stdio, 60);

        stream_set_blocking($this->stderr, false);
        stream_set_timeout($this->stderr, 60);

        // Start a little chit chat
        $this->waitFor("$ ", 5);

        fputs($this->stdio, "calc\n");
        $this->waitFor("; ", 5);

        fputs($this->stdio, "2*3\n");
        $s = $this->waitFor("; ", 5);
        self::assertEquals("2*3\r\n\t6\r\n; ", $s);

        // Check if anything happend on stderr.
        $stderrOutput = fread($this->stderr, 8192);
        self::assertEmpty($stderrOutput);
    }

    /** Wait for prompt.
     *
     * @global Logger $log
     * @global resource $stdio
     * @param string $prompt    The prompt to wait for.
     * @param int $waitSecs     Seconds to wait for the prompt to appear.
     * @return string           The output so far. Does usually include the echoed command.
     * @throws Exception        In case of a timeout before the prompt was seen.
     */
    function waitFor($prompt = '$ ', $waitSecs = 10) {
        $time0 = time();
        $s = "";
        while (true) {
            $this->log->info("While...");

            $s = fread($this->stdio, 8192);
            $this->log->info("< |" . rtrim($s) . "|");

            if (preg_match('/' . preg_quote($prompt) . '/', $s)) {
                $this->log->info("Found prompt!");
                break;
            }

            if ((time() - $time0) > $waitSecs) {
                throw new Exception("Timeout!");
            }

            $this->log->info("Sleeping");
            usleep(0.5 * 1000000);
        }

        return $s;
    }

}