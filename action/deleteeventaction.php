<?php
session_start(); // Start the session to access session variables

require_once '../settings/connection.php'; // Include your database connection script
require_once '../settings/core.php'; // Include core functionalities

checkLogin(); // Ensure the user is logged in before proceeding

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if event_id is provided in the query parameters
if (!isset($_GET['event_id'])) {
    die("Event ID not provided.");
}

$event_id = $_GET['event_id'];

// Begin transaction
$connection->begin_transaction();

try {
    // Delete Event
    $deleteEventStmt = $connection->prepare("DELETE FROM Events WHERE EventID = ?");
    $deleteEventStmt->bind_param("i", $event_id);

    if (!$deleteEventStmt->execute()) {
        throw new Exception("Error deleting event: " . $deleteEventStmt->error);
    }

    // Commit transaction
    $connection->commit();
    echo "Event deleted successfully.";

} catch (Exception $e) {
    // Rollback transaction on error
    $connection->rollback();
    echo "Transaction failed: " . $e->getMessage();
}

$connection->close(); // Close the database connection
?>
