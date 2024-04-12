<?php
// Defining database connection parameters as constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'QlA]rs+N8Gt9');
define('DB_NAME', 'EzTickEntry');

// Attempting to connect to MySQL database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS , DB_NAME);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    $successMessage = "Connected successfully"; // Storing the success message in a variable
    // return $successMessage; // Returning the success message
    // echo "success" ;
}
?>
