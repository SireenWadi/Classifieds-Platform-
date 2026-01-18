<?php
$servername = "localhost";
$username = "root";        // XAMPP default
$password = "";            // Usually empty for XAMPP
$dbname = "classifieds1"; // Make sure this matches your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
