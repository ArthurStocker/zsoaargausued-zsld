<?php
/* Database connection */
$servername = "localhost";
$username = "rest_user";
$password = "rest2020";
$dbname = "rest_demo";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connect failed: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
?>