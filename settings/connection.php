<?php
// Database credentials
    $servername = "localhost";
    $username = "root";
    $password = "QlAlrs+N8Gt9";
    $dbname = "EzTickEntry";
    
    // Create connection
    $connection = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        echo "Error connecting";
    } 

// Always good to close the database connection if not needed anymore

?>

