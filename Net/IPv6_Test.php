<?php
require_once 'Net/IPv6.php';

class IPv6_Test extends PHPUnit_Framework_TestCase {
    
    
    public function testCheckPresence() {
        $ipv6 = new Net_IPv6();
 
        self::assertTrue(  $ipv6->isInNetmask('F800::0000:0001', 'F800::', 48) );
        self::assertTrue(  $ipv6->isInNetmask('F800::1000:0000', 'F800::', 48) );
        self::assertFalse( $ipv6->isInNetmask('F801::', 'F800::', 48) );
        self::assertTrue(  $ipv6->isInNetmask('F801::', 'F800::', 8) );
    }
}
