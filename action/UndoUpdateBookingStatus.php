<?php
session_start(); // Start the session to access session variables
require_once '../settings/connection.php'; // Include your database connection script

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bookingId'])) {
    $bookingId = intval($_POST['bookingId']);

    // Begin transaction
    $connection->begin_transaction();
    try {
        // Update the BookingStatusID from 2 (checked in) to 4 (to be checked in)
        $stmt = $connection->prepare("UPDATE Bookings SET BookingStatusID = 4 WHERE BookingID = ? AND BookingStatusID = 2");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $connection->commit();
            header("Location: ../view/seeAttendees.php?success=checkedOut"); // Redirect with a success message
            exit();
        } else {
            throw new Exception("No changes made - booking might not be checked in or does not exist.");
        }
    } catch (Exception $e) {
        $connection->rollback();
        header("Location: ../view/seeAttendees.php?error=" . urlencode($e->getMessage())); // Redirect with an error message
        exit();
    } finally {
        $stmt->close();
        $connection->close();
    }
} else {
    // Redirect or error handling
    header("Location:../view/seeAttendees.php?error=InvalidRequest");
    exit();
}
?>
