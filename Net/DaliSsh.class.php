<?php

/**
 * Opens an SSH connection for "Expect" like chit chat.
 *
 * Used to execute remote commands via SSH connection.
 * Uses sockets functions and fread() to process result.
 *
 * All methods throw Exceptions on error.
 *
 * Usage:
 *  $dali = new DaliSsh($host);
 *  $dali->login($user, $pass, '$ ');
 *  $dali->exec('calc', '; ');
 *  $stdout = $dali->exec("2*3", '; ');   // Should be "2*3\r\n\t6\r\n; "
 *  $stderr = $dali->getStderr();
 *
 * Written in 2012 by Christian Hammers <chammers@netcologne.de> based on
 * DaliTelnet which was written by Dalibor Andzakovic <dali@swerve.co.nz>
 * Based on the code by Matthias Blaser <mb@adfinis.ch>
 * Based on the code originally written by Marc Ennaji.
 *
 */
class DaliSsh {

    /** @var resource   SSH connection (not the input/output stream!) */
    private $conn;

    /** @var string     Hostname */
    private $host;

    /** @var int        Port number */
    private $port;

    /** @var int        Timeout in seconds */
    private $timeout;

    /** @var resource   The stdin+stdout stream. */
    private $stdio;

    /** @var resource   The stderr stream. */
    private $stderr;

    /** @var string     The string that's waited for after every command. */
    private $prompt;

    /** @var string     Contains the complete communication recording. */
    private $global_buffer = '';

    /** @var Logger     Apache Log4php is activated with activateLog4php(). */
    private $logger;

    /** @var int        Current logging direction. */
    private $dir = 0;

    /** @var float      Waiting time in µs between fread() calls. */
    private $usleepTime = 100;

    /**
     * Initialises parameters and connects to remote host.
     *
     * @param string $host      Host name or IP address.
     * @param int $port         TCP port number.
     * @param int $timeout      Connection timeout in seconds.
     */
    public function __construct($host = '127.0.0.1', $port = '22', $timeout = 10) {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;

        $this->connect();
    }

    /**
     * Cleans up socket connection and command buffer.
     */
    public function __destruct() {
        $this->disconnect();
    }

    /**
     * Closes IP socket.
     */
    public function disconnect() {
        if ($this->stderr !== null) {
            if (!fclose($this->stderr)) {
                throw new Exception("Error while closing ssh stderr handle!");
            }
        }
        if ($this->stdio !== null) {
            if (!fclose($this->stdio)) {
                throw new Exception("Error while closing ssh stdio handle!");
            }
        }
    }

    /**
     * Attempts connection to remote host.
     */
    public function connect() {
        $this->conn = ssh2_connect($this->host, $this->port);
        if (!is_resource($this->conn)) {
            throw new Exception("Connect to $this->host failed!");
        }
    }

    /** Logs in and opens a shell.
     *
     * @param string $user
     * @param string $password
     * @param string [$prompt]
     * @return string               The Login Message.
     */
    public function login($user, $password, $prompt = '$ ', $term = 'vt102') {
        if ($this->logger)
            $this->logger->debug("login($user, xxx, $prompt)");

        // authenticate
        if (!ssh2_auth_password($this->conn, $user, $password)) {
            throw new Exception("Login as $user@$this->host failed!");
        }

        // request interactive shell
        $this->stdio = ssh2_shell($this->conn, $term);
        if (!is_resource($this->stdio))
            throw new Exception("Requesting shell failed!");
        stream_set_timeout($this->stdio, $this->timeout + 1);
        stream_set_blocking($this->stdio, false);

        // shells have 3 streams, stdin and stdout (here stdio) and stderr
        $this->stderr = ssh2_fetch_stream($this->stdio, SSH2_STREAM_STDERR);
        stream_set_timeout($this->stderr, $this->timeout + 1);
        stream_set_blocking($this->stderr, false);

        $this->setPrompt($prompt);
        return $this->waitPrompt();
    }

    /**
     * Executes command and returns a string with result.
     *
     * This method is a wrapper write() and waitPrompt().
     *
     * @param string $command           Command to execute.
     * @param string [$onetimePrompt]   Prompt for just this command.
     * @param int [$onetimeTimeout]     Timeout for just this command.
     * @return string Command           Received data.
     */
    public function exec($command, $onetimePrompt = null, $onetimeTimeout = null) {
        $this->write($command);
        return $this->readTo($onetimePrompt, $onetimeTimeout);
    }

    /**
     * Sets the string of characters to respond to.
     *
     * This should be set to the last character of the command line prompt
     *
     * @param string $s     String to respond to.
     */
    public function setPrompt($s = '$ ') {
        $this->prompt = $s;
    }

    /**
     * Returns the content of the global command buffer.
     *
     * @return string       Content of the global command buffer.
     */
    public function getGlobalBuffer() {
        return $this->global_buffer;
    }

    /** Returns the data that the server send on stderr.
     *
     * @return string
     */
    public function getStderr() {
        if ($this->logger) {
            $this->logger->debug("getting stderr...");
        }
        $stderr = '';
        while (!feof($this->stderr)) {
            $tmp = fread($this->stderr, 8192);
            if ($tmp === false or $tmp === '') {
                break;
            }
            $stderr .= $tmp;
        }
        return $stderr;
    }

    /** Sets the Log4php level.
     *
     * @param LoggerLevel $level        Use LoggerLevel::getInfoLevel() etc.
     */
    public function activateLog4php(LoggerLevel $level) {
        require_once 'log4php/Logger.php';
        $this->logger = Logger::getLogger(__CLASS__);
        $this->logger->setLevel($level);
    }

    /**
     * Reads characters from the socket and adds them to command buffer.
     *
     * Stops when prompt is ecountered.
     *
     * @param string [$onetimePrompt]   Explicit prompt or null for the standard one.
     * @param int [$onetimeTimeout]     Explicit timeout in seconds or null for standard.
     * @return string                   The buffer.
     */
    protected function readTo($onetimePrompt = null, $onetimeTimeout = null) {
        if (!$this->stdio) {
            throw new Exception("Connection closed");
        }
        $myPrompt = isset($onetimePrompt) ? $onetimePrompt : $this->prompt;
        $myTimeout = isset($onetimeTimeout) ? $onetimeTimeout : $this->timeout;
        if ($this->logger) {
            $this->logger->debug("waitFor $myPrompt for $myTimeout seconds");
        }

        $buffer = '';
        $until_t = time() + $myTimeout;
        while (time() <= $until_t) {
            $tmp = fread($this->stdio, 8192);
            $this->log(1, $tmp);
            if ($tmp === false) {
                throw new Exception("Error reading from ssh stdin!");
            }
            $buffer .= $tmp;

            // We've encountered the prompt. Break out of the loop.
            if ((substr($buffer, strlen($buffer) - strlen($myPrompt))) == $myPrompt) {
                return $buffer;
            }

            usleep($this->usleepTime);
        }
        if ($this->logger) {
            $this->logger->debug("STDERR: " . $this->getStderr());
        }
        throw new Exception("Couldn't find the requested : '$myPrompt' within {$myTimeout} seconds!");
    }

    /**
     * Write command to a socket.
     *
     * @param string $buffer        Stuff to write to socket.
     * @param boolean $addNewLine   Default true, adds newline to the command.
     */
    protected function write($buffer, $addNewLine = true) {
        if (!$this->stdio) {
            throw new Exception("Connection closed");
        }

        if ($addNewLine == true) {
            $buffer .= "\n";
        }

        $this->log(0, $buffer);

        if (!fwrite($this->stdio, $buffer) < 0) {
            throw new Exception("Error writing to socket");
        }
    }

    /**
     * Reads socket until prompt is encountered.
     *
     * @return string       The received data including the prompt.
     */
    protected function waitPrompt() {
        return $this->readTo();
    }

    /** Adds string to communication recording.
     *
     * Non-printable characters are converted to e.g. "#13".
     *
     * @param int $dir          0=Outgoing, 1=Incoming
     * @param string $string    The char or string to be logged.
     */
    protected function log($dir, $string) {
        // fread will return false on nonblocking reads with no input.
        if ($dir == 1 and ($string === false or $string === '')) {
            return;
        }

        // determine the direction markers
        $prefix = (($dir == 0) ? '> ' : '< ');
        if ($this->dir != $dir) {
            $this->dir = $dir;
        }

        // mask non-printable characters
        $converted = '';
        foreach (str_split($string) as $char) {
            $ascii = ord($char);
            $converted .= ($ascii < 32 or $ascii >= 127) ? sprintf("#%02X", $ascii) : $char;
        }

        // append to logger and/or variable
        foreach (explode("#0A", $converted) as $singleLine) {
            if ($this->logger) {
                $this->logger->debug("$prefix$singleLine");
            }
            $this->global_buffer .= "$prefix$converted";
        }
    }

}
