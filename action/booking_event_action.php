<?php
session_start();
require_once '../settings/connection.php'; // Adjust the path as needed

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        // Get user ID from session
        $userId = $_SESSION['user_id'];

        // Get tier ID from POST data
        $tierId = $_POST['tier_id'];

        // Set booking time
        $bookingTime = date('Y-m-d H:i:s');

        // Fetch tier details from the database
        $stmt = $connection->prepare("SELECT EventID, Price FROM TicketTiers WHERE TierID = ?");
        $stmt->bind_param("i", $tierId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $eventId = $row['EventID'];
            $price = $row['Price'];

            // Modify the INSERT statement to include TierID
            $insertStmt = $connection->prepare("INSERT INTO Bookings (UserID, EventID, BookingTime, TotalPrice, BookingStatusID, TierID) VALUES (?, ?, ?, ?, 1, ?)");
            $insertStmt->bind_param("iisdi", $userId, $eventId, $bookingTime, $price, $tierId); // Added tierId as a parameter
            if ($insertStmt->execute()) {
                // Booking inserted successfully
                $response = array('success' => true, 'message' => 'Booking successful.');
            } else {
                // Failed to insert booking
                $response = array('success' => false, 'message' => 'Failed to insert booking.');
            }
            $insertStmt->close();
        } else {
            // Tier not found
            $response = array('success' => false, 'message' => 'Tier not found.');
        }
        $stmt->close();
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
