<?php
include 'settings/connection.php'; // Include your database connection settings

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['bookingId'])) {
    $bookingId = $_POST['bookingId'];

      // Debugging line to check the received booking ID
      error_log("Received booking ID: " . $bookingId);
      // Rest of the code...
  

    $sql = "UPDATE Bookings SET BookingStatusID = 2 WHERE BookingID = ?";
    $stmt = $connection->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $bookingId);
        $success = $stmt->execute();
        
        if ($success) {
            echo "Check-In Successful";
        } else {
            echo "Failed to update booking status";
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $connection->error;
    }

    $connection->close();
} else {
    echo "No booking ID provided";
}
?>
