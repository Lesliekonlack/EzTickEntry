<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "QlAlrs+N8Gt9";
$dbname = "EzTickEntry";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo "Error connecting";
} else {
    echo "Connected";
}

// Always good to close the database connection if not needed anymore
$conn->close();
?>

