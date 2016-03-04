<?php
error_reporting(E_ALL);

$global_obj = null;

class my_class {

    var $my_value;

    function my_class() {
        global $global_obj;
        $global_obj = &$this; // Dieser Teil ginge zwar schon....
    }

}

$a = new my_class; // ... aber new liefert nicht das Objekt sondern eine Kopie!
$a->my_value = 5;
$global_obj->my_value = 10;
echo "a->my_value = ".$a->my_value."\n";
