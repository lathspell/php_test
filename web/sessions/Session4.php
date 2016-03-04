<?php

session_start();

if (empty($_SESSION['name'])) {
    echo "kein name, geh weg";
    exit(0);
}

echo "Erneut Hallo ".$_SESSION['name']."!<br>\n";

echo '<a href="Session-logout.php">logout</a> oder <a href="Session3.php">weiter</a>';
