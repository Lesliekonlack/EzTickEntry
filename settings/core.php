<?php
session_start(); // Starting the session at the beginning of the file


// Function to check if the user is logged in
function checkLogin() {
    // Check if the user_id session is set, which indicates the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // If the user_id session does not exist, redirecting to the login page
        header('Location: ../view/entrypage.php');
        die(); // Using die() to terminate the script after redirection to prevent further script execution
    }
}
?>
