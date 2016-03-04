<?php

class SNMPv3_Test extends PHPUnit_Framework_TestCase {

    /**
     * Agent (Server).
     *
     * @var string
     */
    private $host = 'localhost';
    /**
     * Set the securityName used for authenticated SNMPv3 messages.
     * Overrides the defSecurityName token in the snmp.conf file.
     *
     * @var string
     */
    private $secName;
    /**
     * Set the securityLevel used for SNMPv3 messages (noAuthNoPriv|authNoPriv|authPriv).
     * Appropriate pass phrase(s) must provided when using any level higher than noAuthNoPriv.
     * Overrides the defSecurityLevel token in the snmp.conf file.
     *
     * @var string
     */
    private $secLevel = 'authPriv';
    /**
     * Set the authentication protocol (MD5 or SHA) used for authenticated SNMPv3 messages.
     * Overrides the defAuthType token in the snmp.conf file.
     *
     * @var string
     */
    private $authProtocol = 'SHA';
    /**
     * Set the authentication pass phrase used for authenticated SNMPv3 messages.
     * Overrides the defAuthPassphrase token in the snmp.conf file.
     * 
     * @var string
     */
    private $authPassword;
    /**
     * Set the privacy protocol (DES or AES) used for encrypted SNMPv3 messages.
     * Overrides the defPrivType token in the snmp.conf file. This option is
     * only valid if the Net-SNMP software was build to use OpenSSL.
     *
     * @var string
     */
    private $privProtocol = 'AES';
    /**
     * Set the privacy pass phrase used for encrypted SNMPv3 messages.
     * Overrides the defPrivPassphrase token in the snmp.conf file.
     * 
     * @var string
     */
    private $privPassword;
    /**
     * @var int
     */
    private $timeout = null;
    /**
     * @var int
     */
    private $retries = null;

    /** Unit-Test Constructor. */
    public function setUp() {
        $this->secName = 'james';
        $this->authPassword = 'secret007';
        $this->privPassword = 'secret007';
    }

    public function testIfAlias() {
        self::assertTrue(function_exists('snmp3_set'));

        snmp_set_quick_print(true);

        $random = "test" . rand(0, 10000);
        $this->set3('IF-MIB::ifAlias.3', 's', $random);
        self::assertEquals($random, $this->get3('IF-MIB::ifAlias.3'));

        $aliases = $this->walk3('IF-MIB::ifAlias');
        self::assertEquals($random, $aliases[2]);
    }

    private function get3($object_id) {
        return snmp3_get($this->host, $this->secName, $this->secLevel, $this->authProtocol, $this->authPassword, $this->privProtocol, $this->privPassword, $object_id /* , $this->timeout, $this->retries */);
    }

    private function set3($object_id, $type, $value) {
        snmp3_set($this->host, $this->secName, $this->secLevel, $this->authProtocol, $this->authPassword, $this->privProtocol, $this->privPassword, $object_id, $type, $value /* , $this->timeout, $this->retries */);
    }

    private function walk3($object_id) {
        return snmp3_walk($this->host, $this->secName, $this->secLevel, $this->authProtocol, $this->authPassword, $this->privProtocol, $this->privPassword, $object_id /* , $this->timeout, $this->retries */);
    }

}
