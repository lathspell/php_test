<?php

class DNS_Test extends PHPUnit_Framework_TestCase {
    
    public function testSyntax() {
    }
    
    public function testCheckPresence() {
        $x = dns_check_record('www.netcologne.de', 'CNAME');
        $this->assertFalse($x);
        
        $x = checkdnsrr('netcologne.de', 'MX');
        $this->assertTrue($x);
    }
    
    public function testA() {
        $x = dns_get_record('www.netcologne.de', DNS_A);
        // print_r($x);
    }
    
    public function testMX() {
        dns_get_mx('netcologne.de', $mxhosts, $weight);
        // print_r($mxhosts);
        // print_r($weight);
    }
}
