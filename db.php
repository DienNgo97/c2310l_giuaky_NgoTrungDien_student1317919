<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

// Create 
$conn = new mysqli($servername, $username, $password, $dbname);

// Check 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// UTF-8
$conn->set_charset("utf8");
?>
