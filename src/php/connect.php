<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");


$db_host = "localhost";
$db_user = "root";
$db_pass = " ";
$db_name = "your_database_name";

$con =  mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (mysqli_connect_error()) {
    echo 'connect to database failed';
}
error_reporting(0);