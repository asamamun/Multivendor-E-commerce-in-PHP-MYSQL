<?php
$conn = new mysqli('localhost','root','','mul_ven_ec_68');
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

/* $conn->query("SET NAMES 'utf8mb4'");
$conn->query("SET CHARACTER SET utf8mb4");
$conn->query("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'"); */
?>