<?php

class SNMP_Test extends PHPUnit_Framework_TestCase {

    /** Wie teste ich ob eine MIB existiert? */
    public function testMIBExisting() {
        if (!class_exists('SNMP')) {
            self::markTestSkipped("Need PHP-5.4 with SNMP OOP API!");
        }

        $snmp = new SNMP(SNMP::VERSION_1, 'localhost', 'unused', 0, 0);
        $oid1 = 'IF-MIB::ifAlias.1';
        $oid2 = 'DOES-NOT-EXIST-MIB::FOO';
        $oid3 = 'NOTIFICATION-LOG-MIB::nlmConfigGlobalEntryLimit.0';
        $oid4 = 'DOES-NOT-EXIST-MIB::BAR';
        
        @$snmp->get(array($oid1, $oid2, $oid3));
        self::assertEquals("Invalid object identifier: DOES-NOT-EXIST-MIB::FOO", $snmp->getError());

        // Only the first error is reported!
        @$snmp->get(array($oid2, $oid4));
        self::assertEquals("Invalid object identifier: DOES-NOT-EXIST-MIB::FOO", $snmp->getError());

        // And if everything's OK?
        @$snmp->get(array($oid1, $oid3));
        self::assertNotEquals(SNMP::ERRNO_OID_PARSING_ERROR, $snmp->errno);
    }

    /** One OID at a time... */
    public function testSet() {
        snmp2_set('localhost', 'private', 'IF-MIB::ifAlias.1', '=', 'foo');
        snmp2_set('localhost', 'private', 'IF-MIB::ifAlias.2', '=', 'bar');
        self::assertEquals('STRING: foo', snmp2_get('localhost', 'private', 'IF-MIB::ifAlias.1'));
        self::assertEquals('STRING: bar', snmp2_get('localhost', 'private', 'IF-MIB::ifAlias.2'));
    }

    /** Remember the protocol prefix! */
    public function testIPv6() {
        self::assertEquals('STRING: foo', snmpget('udp6:[::1]:161', 'private', "IF-MIB::ifAlias.1"));
        $snmp = new SNMP(SNMP::VERSION_2C, 'udp6:[::1]', 'private');
        self::assertEquals('STRING: foo', $snmp->get('IF-MIB::ifAlias.1'));
    }

    /** Multi-OID only works with SNMPv2+ and the OOP extention.
     * 
     * https://bugs.php.net/bug.php?id=37865
     */
    public function testMultiOID() {
        if (!class_exists('SNMP')) {
            self::markTestSkipped("Need PHP-5.4 with SNMP OOP API!");
        }

        $snmp = new SNMP(SNMP::VERSION_2C, 'localhost', 'private');
        $snmp->quick_print = true;
        $orig = $snmp->get('NOTIFICATION-LOG-MIB::nlmConfigGlobalEntryLimit.0');
        $snmp->set(array('IF-MIB::ifAlias.1', 'NOTIFICATION-LOG-MIB::nlmConfigGlobalEntryLimit.0'), array('s', 'u'), array('foo3', 42));
        $a = $snmp->get(array('IF-MIB::ifAlias.1', 'NOTIFICATION-LOG-MIB::nlmConfigGlobalEntryLimit.0'));
        $snmp->set('NOTIFICATION-LOG-MIB::nlmConfigGlobalEntryLimit.0', '=', $orig);
        self::assertEquals(array('foo3', '42'), array_values($a));
    }

    /** Test for the BITS datatype.
     * 
     * The output is identical to that of the snmpwalk/snmpget cli tools.
     */
    public function testBITS() {
        if (!class_exists('SNMP')) {
            self::markTestSkipped("Need PHP-5.4 with SNMP OOP API!");
        }

        $snmp = new SNMP(SNMP::VERSION_2C, 'localhost', 'private');
        $x = $snmp->get("DISMAN-EVENT-MIB::mteEventActions.\"_snmpd\".'_linkDown'");
        self::assertEquals("BITS: 80 notification(0) ", $x);
        self::assertEquals('notification', self::parseBitName($x));

        $snmp->enum_print = true;
        $x = $snmp->get("DISMAN-EVENT-MIB::mteEventActions.\"_snmpd\".'_linkDown'");
        self::assertEquals("BITS: 80 0 ", $x);

        $snmp->quick_print = true;
        $x = $snmp->get("DISMAN-EVENT-MIB::mteEventActions.\"_snmpd\".'_linkDown'");
        self::assertEquals('"80 "', $x);
    }

    /** Returns the name of a bit value.
     * 
     * The NetSNMP library returns values of OIDs of the BIT type like
     * "BITS: 80 notification(0)". This method can be used to extract
     * just the name "notification" from this output.
     * 
     * https://bugs.php.net/bug.php?id=54502 -> wontfix
     *
     * @param string $raw_value     The return of a walk() or get() request.
     * @return string               The name of the BIT value.
     */
    public static function parseBitName($raw_value) {
        preg_match('/^BITS: [0-9a-f]+ (.*)\(\d+\)\s+$/i', $raw_value, $match);
        if (!isset($match[1])) {
            throw new Exception("Cannot parse BIT value from '$raw_value'!");
        }
        return $match[1];
    }

}
