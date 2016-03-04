<?php

class XML_Parser_Test extends PHPUnit_Framework_TestCase {
    private $parsed;

    public function testElementHandler() {
        $xml = file_get_contents(__DIR__."/example.xml");

        $this->parsed = "";
        $parser = xml_parser_create();
        xml_set_element_handler($parser, array("self","show_start_element"), array("self", "show_end_element"));
        xml_parse($parser, $xml);

        $soll = "(ROOT(ARTIKEL(PRICE))(ARTIKEL(PRICE))(ARTIKEL(PRICE))(ARTIKEL(PRICE)))";
        self::assertEquals($soll, $this->parsed);
    }

    private function show_start_element($xml, $tag, $attributes) {
        $this->parsed .= "(${tag}";
    }

    private function show_end_element($xml, $tag) {
        $this->parsed .= ")";
    }
}