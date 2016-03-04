<?php

class File_Test extends PHPUnit_Framework_TestCase {

    public function testStat() {
        $a = stat(__FILE__);
        self::assertEquals(posix_getgid(), $a['gid']);

        $f = fopen(__FILE__, "r");
        self::assertTrue(is_resource($f));
        $a = fstat($f);
        self::assertEquals(posix_getuid(), $a['uid']);
        fclose($f);
        self::assertFalse(is_resource($f));
    }

    public function testFileInfo() {
        file_put_contents("compress.zlib://test.data", md5("blah blub bluh"));
        self::assertTrue(is_readable("test.data"));

        // procedural
        $finfo = finfo_open();
        $mime_type = finfo_file($finfo, "test.data");
        self::assertEquals("gzip compressed data, from Unix", $mime_type);

        // object oriented
        $finfo = new finfo();
        $mime_type = $finfo->file("test.data");
        self::assertEquals("gzip compressed data, from Unix", $mime_type);

        unlink("test.data");
    }

    public function testStreamSelect() {
        file_put_contents("test.data", "blah blub bluh blib");

        $have_read = "";
        $f = fopen("test.data", "r");
        $to_read = array($f);
        while (count($to_read) > 0) {
            $readarray = $to_read;
            $writearray = array();
            $exceptarray = array();
            $a = stream_select($readarray, $writearray, $exceptarray, $tv_sec = 1);
            if ($a === false)
                throw new Exception("Error!");
            if ($a === 0) {
                print("nix passert");
                continue;
            }

            foreach ($readarray as $fread) {
                while (($s = fread($fread, 5)) !== false) {
                    $have_read .= $s;
                    if (feof($f)) {
                        fclose($f);
                        unset($to_read[array_search($fread, $to_read, true)]);
                        break;
                    }
                }
            }
        }
        self::assertEquals(file_get_contents("test.data"), $have_read);
    }

    public function testStreamGetMeta() {
        $f = fopen("test.data", "r");
        $size = filesize("test.data");

        $meta = stream_get_meta_data($f);
        self::assertEquals("plainfile", $meta['wrapper_type']);
        self::assertEquals("STDIO", $meta['stream_type']);
        self::assertEquals("r", $meta['mode']);
        // php.net zu unread_bytes: "You shouldn't use this value"
        self::assertEquals(0, $meta['unread_bytes']); // wird erst nach dem ersten Lesen gesetzt!
        self::assertTrue($meta['seekable']);
        self::assertEquals("test.data", $meta['uri']);
        self::assertFalse($meta['timed_out']);
        self::assertTrue($meta['blocked']);
        self::assertFalse($meta['eof']);
        
        $data = @fread($f, $size);
        $meta = @stream_get_meta_data($f);
        self::assertEquals(0, $meta['unread_bytes']);
        self::assertFalse($meta['eof']);

        $data = @fread($f, 10); // eof kommt erst beim Lesen jenseits des letzten Bytes
        $meta = @stream_get_meta_data($f);
        self::assertTrue("" === $data); // nicht false!
        self::assertTrue($meta['eof']);
    }
}
