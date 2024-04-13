<?php
session_start();
require_once '../settings/connection.php'; // Adjust the path as needed


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventId = $_POST['event_id'];

    // Begin transaction
    $connection->begin_transaction();

    try {
        // Process each ticket tier from the form
        foreach ($_POST['tierName'] as $index => $name) {
            $price = $_POST['tierPrice'][$index];
            $quantity = $_POST['tierQuantity'][$index];
            $isActive = $_POST['tierIsActive'][$index]; // Capture the isActive status from the form

            // Check if this tier already exists
            $checkStmt = $connection->prepare("SELECT TierID FROM TicketTiers WHERE EventID = ? AND Name = ?");
            $checkStmt->bind_param("is", $eventId, $name);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                // Tier exists, update it
                $row = $result->fetch_assoc();
                $tierId = $row['TierID'];
                $updateTierStmt = $connection->prepare("UPDATE TicketTiers SET Price = ?, QuantityAvailable = ?, IsActive = ? WHERE TierID = ?");
                $updateTierStmt->bind_param("diii", $price, $quantity, $isActive, $tierId);
                $updateTierStmt->execute();
                $updateTierStmt->close();
            } else {
                // Tier doesn't exist, insert new tier
                $insertTierStmt = $connection->prepare("INSERT INTO TicketTiers (EventID, Name, Price, QuantityAvailable, IsActive) VALUES (?, ?, ?, ?, ?)");
                $insertTierStmt->bind_param("isdii", $eventId, $name, $price, $quantity, $isActive);
                $insertTierStmt->execute();
                $insertTierStmt->close();
            }
            $checkStmt->close();
        }

        // Commit the transaction
        $connection->commit();
        header('Location: ../view/eventsmanagements.php');
        exit();
    } catch (Exception $e) {
        // Roll back the transaction on error
        $connection->rollback();
        echo "Error: " . $e->getMessage();
    }

    $connection->close();
} else {
    echo "Invalid request.";
}

?>


?>
