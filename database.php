<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "cs4347_group_project";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong.");
}

?>