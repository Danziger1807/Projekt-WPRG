<?php
$host = "localhost";
$db_user = "s32835";
$db_password = "Paw.Cent";
$db_name = "s32835";

$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno) {
    die("B��d po��czenia: " . $connection->connect_error);
}


$connection->set_charset("utf8");
?>

