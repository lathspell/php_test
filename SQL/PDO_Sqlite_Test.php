<?php

class MyRow {
    // Unnötig, würden automatisch erstellt.
    public $i;
    public $v;
    public $e;
    
    public function __set($k, $v) {
        if (array_key_exists($k, get_class_vars(__CLASS__)) === false) {
            throw new Exception("Spalte $k ist nicht erwartet!");
        }
        $this->$k = $v;
    }
    
    public function __toString() {
        return $this->i.": ".$this->v." ist ".$this->e;
    }
}

class PDO_Sqlite_Test extends PHPUnit_Framework_TestCase {
    private $pdo;
    
    public function setUp() {
        $this->assertTrue(extension_loaded('pdo_sqlite'));
        
        $this->pdo = new PDO('sqlite::memory:');
        $this->assertTrue($this->pdo instanceof PDO);
        
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);

        $this->pdo->exec("
            CREATE TABLE test (
              i integer primary key autoincrement,
              v varchar(255) not null,
              e tinyint default null
            )
            ");
        $sth = $this->pdo->prepare("INSERT INTO test VALUES (?, ?, ?)");
        $sth->execute(array(10, 'a', 0));
        $this->assertEquals($sth->rowCount(), 1);
        
        $this->pdo->exec("INSERT INTO test (v, e) VALUES ('b', 1)");
        $this->assertEquals($this->pdo->lastInsertId(), 11);
    }

    
    public function testFetchIntoClass() {
        $sth = $this->pdo->query("SELECT * FROM test");
        $this->assertTrue($sth instanceof PDOStatement);
        
        // Nur einige Backends wie MySQL liefern einen rowCount() bei SELECT zurück!
        // $this->assertEquals($sth->rowCount(), 2);
        
        $rows = $sth->fetchAll(PDO::FETCH_CLASS, 'MyRow');
        $this->assertTrue(is_array($rows) and count($rows) == 2);
        
        $this->assertEquals((string)$rows[1], "11: b ist 1");
    }
    
    public function testTableDump() {
        $sth = $this->pdo->query("SELECT * FROM test ORDER BY i");
        $this->assertTrue($sth instanceof PDOStatement);
        
        //
        // Begin Table Dump from arbitrary $sth
        //
        
        // header
        $html = "<table border=1>\n";
        $html .= "<tr>\n  ";
        for ($i=0; $i<$sth->columnCount(); $i++) {
            $meta = $sth->getColumnMeta($i);
            $html .= '<th>'.htmlentities($meta['name']).' ';
        }
        $html .= "\n</tr>\n";
        
        // body
        while ($row = $sth->fetch(PDO::FETCH_NUM)) {
            $html .= "<tr>\n  ";
            for ($i=0; $i<count($row); $i++) {    
                $html .= '<td>'.htmlentities($row[$i]).' ';
            }
            $html .= "\n</tr>\n";
        }
        $html .= "</table>\n";
        
        $html_soll = <<<EOT
<table border=1>
<tr>
  <th>i <th>v <th>e 
</tr>
<tr>
  <td>10 <td>a <td>0 
</tr>
<tr>
  <td>11 <td>b <td>1 
</tr>
</table>

EOT;
        $this->assertEquals($html, $html_soll);
    }
}

?>
