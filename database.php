<?php

$hostName = "127.0.0.1";
$dbUser = "root";
$dbPassword = "root";
$dbName = "cs4347_group_project";
$dbPort = 3306;
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName, $dbPort);
if (!$conn) {
    die("Something went wrong.");
} else {
}
?>