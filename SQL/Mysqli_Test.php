<?php

class Mysqli_Test extends PHPUnit_Framework_TestCase {

    private $mysqli;

    public function setUp() {
        $this->mysqli = new mysqli('localhost', 'root', 'secret');
        self::assertTrue($this->mysqli instanceof mysqli);

        $this->mysqli->query("DROP DATABASE IF EXISTS php_test");
        $this->mysqli->query("CREATE DATABASE php_test");
        $this->mysqli->query("USE php_test");
    }

    public function testIt() {
        // CREATE TABLE
        $this->mysqli->query("CREATE TABLE t (i int not null primary key, s varchar(255))");

        // INSERT mit Named Parameter - geht nicht
        // INSERT mit ?
        $query = $this->mysqli->prepare("INSERT INTO t SET i=?, s=?");
        $query->bind_param('is', $param_i, $param_s);
        $param_i = 1;
        $param_s = "test1";
        $query->execute();
        $param_i = 2;
        $param_s = "test2";
        $query->execute();

        // UPDATE
        $query = $this->mysqli->prepare("UPDATE t SET i=3, s='test3' WHERE i=?");
        $param_i = 2;
        $query->bind_param('i', $param_i);
        $query->execute();

        // SELECT mit Binding
        $stmt = $this->mysqli->prepare("SELECT i,s FROM t ORDER BY i");
        $stmt->execute();
        $stmt->bind_result($col_i, $col_s);
        $result = array();
        while ($stmt->fetch()) {
            $result[$col_i] = $col_s;
        }
        self::assertEquals(array('1' => 'test1', '3' => 'test3'), $result);

        // SELECT mit fetchObject
        $stmt = $this->mysqli->query("SELECT i,s FROM t ORDER BY i");
        $row = $stmt->fetch_object();
        self::assertEquals('test1', $row->s);

        // SELECT mit fetchRow
        $query = $this->mysqli->query("SELECT i,s FROM t ORDER BY i");
        $row = $query->fetch_assoc();
        self::assertEquals('test1', $row['s']);

        // DELETE
        $query = $this->mysqli->query("DELETE FROM t WHERE i=3");

        // SELECT mit fetch_all - geht nicht
    }

    public function testMultiQuery() {
        $link = $this->mysqli;
        $ist = "";

        mysqli_multi_query($link, "SELECT 1; SELECT 2; SELECT 3;");
        if (mysqli_errno($link))
            throw new Exception(mysqli_error());
        while (mysqli_more_results($link)) {
            mysqli_next_result($link);

            $result = mysqli_store_result($link);
            $ist .= join(",", mysqli_fetch_assoc($result)) . "\n";

            mysqli_free_result($result);
        }

        self::assertEquals("1\n2\n3\n", $ist);
    }
}
