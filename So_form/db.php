<?php
$host = "localhost";
$user = "root"; // change if your MySQL username is different
$pass = "";     // change if you have a MySQL password
$dbname = 'po_management_new';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
    