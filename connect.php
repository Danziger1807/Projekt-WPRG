<?php
$host = "localhost";
$db_user = "-----";
$db_password = "-----";
$db_name = "-----";

$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno) {
    die("B��d po��czenia: " . $connection->connect_error);
}


$connection->set_charset("utf8");
?>

