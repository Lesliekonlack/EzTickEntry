<?php

// Include the file that establishes the database connection
require_once "../settings/connection.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

$eventId = isset($_POST['eventId']) ? $_POST['eventId'] : 0;

$ticketTiers = [];
$sql = "SELECT TierID, Name, Price, QuantityAvailable FROM TicketTiers WHERE EventID = ? AND IsActive = 1";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ticketTiers[] = $row;
    }
}

echo json_encode($ticketTiers);
