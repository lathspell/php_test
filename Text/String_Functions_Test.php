<?php

class String_Functions_Test extends PHPUnit_Framework_TestCase {

    public function testSubString() {
        $comment = '01234567890123456789ABCDE';

        self::assertEquals("ABCDE...", substr($comment, 20) . '...');
        self::assertEquals('01234567890123456789...', substr_replace($comment, '...', 20));
        self::assertEquals("ABCDE...", substr($comment, 20, strlen($comment)) . '...');
        self::assertEquals("ABCDE...", substr($comment, 20, strlen($comment)-20) . '...');
        self::assertEquals("20", substr_replace($comment, 20, '...'));

        self::assertEquals("45", substr("12345", -2, 2));
        self::assertEquals("23", substr("12345", -4, -2)); // -2 schneidet 2 ab
    }

    public function testSuche() {
        //
        // strpos
        //

        self::assertEquals(3, strpos("12345", "4"));
        self::assertEquals(3, strpos("12345", "45"));
        self::assertEquals(false, strpos("12345", "4666"));
        self::assertEquals(0, strpos("12345", "123"));

        // strstr - Alles hinter dem "@"
        $s = "foo@example.com";
        self::assertEquals("example.com", substr($s, strpos($s, '@')+1));

        // pos ist trotz $offset relativ zum Anfang von $haystack
        self::assertEquals(3, strpos("12345", "45", 2));
        self::assertEquals(false, strpos("12345", "45", 4));

        //
        // strstr (Alias: strchr)
        //

        self::assertEquals("4567", strstr("1234567", "45" /* $before=false */));
        self::assertEquals("123", strstr("1234567", "45", $before=true));
        self::assertEquals(false, strstr("1234567", "foo"));

        //
        // strrchr
        //
        self::assertEquals("4567", strrchr("1234567", "45"));
    }

    public function testWordwrap() {
        $s = "das ist  ein tst mit vielen wörtern";

        // * Es wird kein "\n" ans Ende gehangen wenn dort keines war
        // * Bei einem Umbruch wird genau ein Spaces durch $delim ersetzt
        $ist = wordwrap($s, 10);
        $soll = "das ist \n"."ein tst\n"."mit vielen\n"."wörtern";
        self::assertEquals($soll, $ist);

        // Auch bei $cut=true wird bei Leerzeichen umgebrochen falls es möglich ist
        $ist = wordwrap($s, 10, "\n", true);
        $soll = "das ist \n"."ein tst\n"."mit vielen\n"."wörtern";
        self::assertEquals($soll, $ist);

        $s = "das ist ein gaaaaaaaaaaaaaaaaaanz langes wort";

        // Hier ist wegen $cut=false eine Zeile zu lang
        $ist = wordwrap($s, 10, "\n", false);
        $soll = "das ist\n"."ein\n"."gaaaaaaaaaaaaaaaaaanz\n"."langes\n"."wort";
        self::assertEquals($soll, $ist);
        // Hier ist wegen $cut=true ein Wort mittendrin umgebrochen
        $ist = wordwrap($s, 10, "\n", true);
        $soll = "das ist\n"."ein\n"."gaaaaaaaaa\n"."aaaaaaaaan\n"."z langes\n"."wort";
        self::assertEquals($soll, $ist);
    }
}
