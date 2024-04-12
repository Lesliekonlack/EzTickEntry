<?php
include '../settings/connection.php'; // Include the connection settings
echo "REAChed";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookingIds']) && is_array($_POST['bookingIds']) && isset($_POST['totalPrice']) && isset($_POST['numberOfBookings'])) {
    $bookingIds = $_POST['bookingIds'];
    $totalPrice = $_POST['totalPrice'];
    $numberOfBookings = $_POST['numberOfBookings'];

    if (count($bookingIds) < $numberOfBookings) {
        echo 'Error: The number of selected bookings does not match the number of bookings to be paid for.';
        exit;
    }

    $connection->begin_transaction();

    try {
        foreach ($bookingIds as $index => $bookingId) {
            if ($index >= $numberOfBookings) break;

            $paymentSql = "INSERT INTO Payments (BookingID, Amount, PaymentMethod, PaymentStatusID, PaymentDate) VALUES (?, ?, 'Online', 2, NOW())";
            $paymentStmt = $connection->prepare($paymentSql);
            $paymentStmt->bind_param("id", $bookingId, $totalPrice);
            if (!$paymentStmt->execute()) throw new Exception("Error inserting payment record: " . $connection->error);

            $updateSql = "UPDATE Bookings SET BookingStatusID = 4 WHERE BookingID = ?";
            $updateStmt = $connection->prepare($updateSql);
            $updateStmt->bind_param("i", $bookingId);
            if (!$updateStmt->execute()) throw new Exception("Error updating booking status: " . $connection->error);

            // Fetch the TierID for the current booking
            $tierFetchSql = "SELECT TierID FROM Bookings WHERE BookingID = ?";
            $tierFetchStmt = $connection->prepare($tierFetchSql);
            $tierFetchStmt->bind_param("i", $bookingId);
            $tierFetchStmt->execute();
            $tierResult = $tierFetchStmt->get_result();
            if ($tierRow = $tierResult->fetch_assoc()) {
                $tierId = $tierRow['TierID'];

                // Insert into Tickets table
                $ticketSql = "INSERT INTO Tickets (BookingID, TierID, TicketStatusID) VALUES (?, ?, 1)"; // Assuming 1 as a default TicketStatusID
                $ticketStmt = $connection->prepare($ticketSql);
                $ticketStmt->bind_param("ii", $bookingId, $tierId);
                if (!$ticketStmt->execute()) throw new Exception("Error inserting ticket record: " . $connection->error);
            } else {
                throw new Exception("TierID not found for the booking.");
            }
        }

        $connection->commit();
        
        echo 'Payment recorded, booking status updated, and tickets created successfully.';

    } catch (Exception $e) {
        $connection->rollback();
        echo 'Error: ' . $e->getMessage();
    }

} else {
    echo 'Invalid request.';
}
?>
