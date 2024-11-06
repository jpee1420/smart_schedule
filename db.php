<?php
// db.php

$servername = "localhost";
$username = "root"; // Change to your username
$password = ""; // Change to your password
$dbname = "smart_schedule";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

