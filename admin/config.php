<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admin_db";
$charset = "utf8mb4";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset($charset);
?>
