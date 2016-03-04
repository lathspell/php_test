<?php

if ($_POST['password'] != "geheim") {
    echo "go away";
    exit(0);
}

session_start();
$_SESSION['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

printf('<a href="Session3.php">weiter</a>');
