<?php

class PDO_MySQL_Test extends PHPUnit_Framework_TestCase {
    private $pdo;

    public function setUp() {
        $this->pdo = new PDO('mysql:localhost', 'root', 'secret');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::assertTrue($this->pdo instanceof PDO);

        $this->pdo->query("DROP DATABASE IF EXISTS php_test");
        $this->pdo->query("CREATE DATABASE php_test");
        $this->pdo->query("USE php_test");
    }

    public function testIt() {
        // CREATE TABLE
        $this->pdo->query("CREATE TABLE t (i int not null primary key, s varchar(255))");

        // INSERT mit Named Parameter
        $query = $this->pdo->prepare("INSERT INTO t SET i=:i, s=:s");
        $query->bindParam('s', $param_s);
        $query->bindValue('i', 1);
        $param_s = "test1";
        $query->execute();

        // INSERT mit ?
        $query = $this->pdo->prepare("INSERT INTO t SET i=?, s=?");
        $query->execute(array(2, 'test2'));

        // UPDATE
        $query = $this->pdo->prepare("UPDATE t SET i=3, s='test3' WHERE i=?");
        $query->execute(array(2));

        // SELECT mit Binding
        $query = $this->pdo->query("SELECT i,s FROM t ORDER BY i");
        $query->bindColumn('s', $col_s, PDO::PARAM_STR);
        $query->bindColumn('i', $col_i, PDO::PARAM_INT);
        $result = array();
        while ($query->fetch()) {
            $result[$col_i] = $col_s;
        }
        self::assertEquals(array('1'=>'test1', '3'=>'test3'), $result);

        // SELECT mit fetchObject
        $query = $this->pdo->query("SELECT i,s FROM t ORDER BY i");
        $row = $query->fetchObject();
        self::assertEquals('test1', $row->s);

        // SELECT mit fetchRow
        $query = $this->pdo->query("SELECT i,s FROM t ORDER BY i");
        $row = $query->fetch(PDO::FETCH_ASSOC);
        self::assertEquals('test1', $row['s']);

        // DELETE
        $query = $this->pdo->query("DELETE FROM t WHERE i=3");

        // SELECT mit fetch
        $query = $this->pdo->query("SELECT i,s FROM t");
        self::assertEquals(array('0'=>array('i'=>'1', 's' => 'test1')), $query->fetchAll(PDO::FETCH_ASSOC));
    }

}
