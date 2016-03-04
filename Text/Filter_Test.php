<?php

class FilterTest extends PHPUnit_Framework_TestCase {

    public function testFilter() {
        self::assertFalse( filter_var($x=null, FILTER_VALIDATE_BOOLEAN) );
        self::assertFalse( filter_var($x=false, FILTER_VALIDATE_BOOLEAN) );
        self::assertFalse( filter_var($x=0, FILTER_VALIDATE_BOOLEAN) );
        self::assertFalse( filter_var($x="no", FILTER_VALIDATE_BOOLEAN) );
        self::assertTrue( filter_var($x=true, FILTER_VALIDATE_BOOLEAN) );
        self::assertTrue( filter_var($x=1, FILTER_VALIDATE_BOOLEAN) );
        self::assertTrue( filter_var($x="yes", FILTER_VALIDATE_BOOLEAN) );

        self::assertEquals("ch@lathspell.de", filter_var($x="ch@lathspell.de", FILTER_VALIDATE_EMAIL) );
        self::assertEquals("ch+spam@lathspell.de", filter_var($x="ch+spam@lathspell.de", FILTER_VALIDATE_EMAIL) );
        self::assertFalse( filter_var($x="root", FILTER_VALIDATE_EMAIL) );
        self::assertFalse( filter_var($x="x@y", FILTER_VALIDATE_EMAIL) );

        self::assertTrue( false !== filter_var($x="212.117.67.2", FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE));
        self::assertFalse( filter_var($x="10.117.67.2", FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE));
        self::assertTrue( false !== filter_var($x="fe80::0", FILTER_VALIDATE_IP, FILTER_FLAG_IPV6));

        self::assertFalse( filter_var($x="Täst", FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-z]$/i"))));
        self::assertFalse( filter_var($x="Test", FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-z]$/i"))));

        self::assertEquals(42, filter_var($x=42, FILTER_VALIDATE_INT, FILTER_REQUIRE_SCALAR));
        self::assertEquals(array(42,4), filter_var($x=array(42,4), FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY));
        self::assertFalse(filter_var($x=42, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY));
    }

    public function testSanitize() {
        // Vorsicht, SANITIZE schneidet nur ungültige Zeichen weg, das Ergebnis
        // ist deshalb zwar VALID aber nicht umbedingt das erwartete.
        self::assertEquals(12345,  filter_var($x="1,234.5", FILTER_SANITIZE_NUMBER_INT));
        self::assertEquals("ch+spam@lathspell.defoo", filter_var($x="ch+spam\n@lathspell.de,foo", FILTER_SANITIZE_EMAIL));
    }

    public function testInput() {
        // buggy? self::assertNotNull(filter_input(INPUT_SERVER, 'HOME', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/./"))));
    }

    public function testArray() {
        $input = array('x'=>42, 'y'=>'yes');
        self::assertEquals($input, filter_var_array($input, array(
            'x' => FILTER_VALIDATE_INT,
            'y' => FILTER_VALIDATE_BOOLEAN)));
    }
}
