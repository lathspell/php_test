<?php

session_start();

setcookie(session_name(), '', time()-42000);
$_SESSION = array();
session_destroy();

echo "Logout!";

echo '<a href="Session3.php">weiter</a>';