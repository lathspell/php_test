<?php

class Mysql_Test extends PHPUnit_Framework_TestCase {
    private $mysql;

    public function setUp() {
        $this->mysql = mysql_connect('localhost', 'root', 'secret');
        self::assertTrue(is_resource($this->mysql));
        
        mysql_query("DROP DATABASE IF EXISTS php_test", $this->mysql);
        mysql_query("CREATE DATABASE php_test", $this->mysql);
        mysql_query("USE php_test", $this->mysql);
    }

    public function testIt() {
        // CREATE TABLE
        mysql_query("CREATE TABLE t (i int not null primary key, s varchar(255))", $this->mysql);

        // INSERT mit Named Parameter - geht nicht
        // INSERT mit ? - geht nicht
        mysql_query("INSERT INTO t SET i=1, s='".mysql_real_escape_string("test1")."'");
        mysql_query("INSERT INTO t SET i=2, s='".mysql_real_escape_string("test2")."'");

        // UPDATE
        mysql_query("UPDATE t SET i=3, s='test3' WHERE i=2");

        // SELECT mit Binding -- geht nicht

        // SELECT mit fetchObject
        $result = mysql_query("SELECT i,s FROM t ORDER BY i", $this->mysql);
        $row = mysql_fetch_object($result);
        self::assertEquals('test1', $row->s);

        // SELECT mit fetchRow
        $result = mysql_query("SELECT i,s FROM t ORDER BY i", $this->mysql);
        $row = mysql_fetch_assoc($result);
        self::assertEquals('test1', $row['s']);

        // DELETE
        mysql_query("DELETE FROM t WHERE i=3", $this->mysql);

        // SELECT mit fetchAll - geht nicht
        // SELECT zur Kontrolle
        $result = mysql_query("SELECT i,s FROM t WHERE i=3", $this->mysql);
        self::assertEquals(0, mysql_num_rows($result));
    }

}
