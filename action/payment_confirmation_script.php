<?php
session_start(); // Start the session to access session variables

require_once '../settings/connection.php'; // Include your database connection script
require_once '../settings/core.php'; // Include core functionalities

checkLogin(); // Ensure the user is logged in before proceeding

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the data sent from the client-side
    $userId = $_POST['userId'];
    $bookingId = $_POST['bookingId'];
    $eventName = $_POST['eventName'];
    $tierName = $_POST['tierName'];
    $tierPrice = $_POST['tierPrice'];
    $paymentMethod = $_POST['paymentMethod'];
    $userName = $_POST['userName'];

    // Assuming other required variables are available in your environment

    // Insert the payment record into the database
    $sql = "INSERT INTO Payments (BookingID, Amount, PaymentMethod, PaymentStatusID, PaymentDate) 
            VALUES (?, ?, ?, ?, NOW())"; // Assuming PaymentStatusID for pending status is predefined in the database
    $stmt = $connection->prepare($sql);
    // Assuming PaymentStatusID for pending status is predefined in the database
    $paymentStatusPending = 1; // Adjust the value based on your database
    $stmt->bind_param("idis", $bookingId, $tierPrice, $paymentMethod, $paymentStatusPending);
    $success = $stmt->execute();

    // Check if the payment record was inserted successfully
    if ($success) {
        $response = array('success' => true);
        // You can perform additional actions here if needed
    } else {
        $response = array('success' => false);
        // Handle the case where the payment record insertion fails
    }

    // Send the response back to the client-side
    echo json_encode($response);
} else {
    // Handle the case where the request method is not POST
    http_response_code(405); // Method Not Allowed
    echo json_encode(array('error' => 'Method Not Allowed'));
}
?>