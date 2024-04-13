<?php

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
    // Update Event status to 'Cancelled'
    $cancelEventStmt = $connection->prepare("UPDATE Events SET EventStatusID = 2 WHERE EventID = ?");
    $cancelEventStmt->bind_param("i", $event_id);

    if (!$cancelEventStmt->execute()) {
        throw new Exception("Error updating event status: " . $cancelEventStmt->error);
    }


    // Commit transaction
    $connection->commit();
    echo "Event status updated to 'Cancelled' successfully.";
    header("location: ../view/eventsmanagements.php");

} catch (Exception $e) {
    // Rollback transaction on error
    $connection->rollback();
    echo "Transaction failed: " . $e->getMessage();
    header("location: ../view/eventsmanagements.php");
}

$connection->close(); // Close the database connection
?>


