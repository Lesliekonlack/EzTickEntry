<?php
session_start();
require_once '../settings/connection.php'; // Ensure the path is correct

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        // Get user ID from session
        $userId = $_SESSION['user_id'];

        // Get the number of bookings to delete from POST data
        $deleteCount = isset($_POST['delete_count']) ? (int)$_POST['delete_count'] : 0;

        if ($deleteCount == 0) {
            // Delete all bookings for the user with BookingStatusID = 1
            $deleteStmt = $connection->prepare("DELETE FROM Bookings WHERE UserID = ? AND BookingStatusID = 1");
            $deleteStmt->bind_param("i", $userId);
            if ($deleteStmt->execute()) {
                $deletedCount = $deleteStmt->affected_rows; // Get the number of affected rows
                $response = array('success' => true, 'message' => "All $deletedCount pending booking(s) deleted successfully.");
            } else {
                $response = array('success' => false, 'message' => "Failed to delete bookings.");
            }
            $deleteStmt->close();
        } else {
            // Fetch the most recent bookings for the user with BookingStatusID = 1 to delete specific count
            $stmt = $connection->prepare("SELECT BookingID FROM Bookings WHERE UserID = ? AND BookingStatusID = 1 ORDER BY BookingTime DESC LIMIT ?");
            $stmt->bind_param("ii", $userId, $deleteCount);
            $stmt->execute();
            $result = $stmt->get_result();

            $deletedCount = 0;
            while ($row = $result->fetch_assoc()) {
                // Delete each booking
                $deleteStmt = $connection->prepare("DELETE FROM Bookings WHERE BookingID = ?");
                $bookingId = $row['BookingID'];
                $deleteStmt->bind_param("i", $bookingId);
                if ($deleteStmt->execute()) {
                    $deletedCount++;
                }
                $deleteStmt->close();
            }

            if ($deletedCount == $deleteCount) {
                // All specified pending bookings deleted successfully
                $response = array('success' => true, 'message' => "$deletedCount pending booking(s) deleted successfully.");
            } else {
                // Some bookings might not have been deleted
                $response = array('success' => false, 'message' => "Only $deletedCount out of $deleteCount pending booking(s) were deleted.");
            }
            $stmt->close();
        }
    } else {
        // User not logged in
        $response = array('success' => false, 'message' => 'User not logged in.');
    }
} else {
    // Invalid request method
    $response = array('success' => false, 'message' => 'Invalid request method.');
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>