<?php
session_start(); // Start the session to access session variables

require_once '../settings/connection.php'; // Include your database connection script
require_once '../settings/core.php'; // Include core functionalities

checkLogin(); // Ensure the user is logged in before proceeding

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check for POST request and event_id
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['event_id'])) {
    die("This action requires a POST request and an event ID.");
}

$event_id = $_POST['event_id'];
$title = $connection->real_escape_string($_POST['title']);
$description = $connection->real_escape_string($_POST['description']);
$startTime = $connection->real_escape_string($_POST['start_time']);
$endTime = $connection->real_escape_string($_POST['end_time']);
$venueName = $connection->real_escape_string($_POST['venue_name']);
$venueLocation = $connection->real_escape_string($_POST['venue_location']);
$venueCapacity = (int)$_POST['venue_capacity'];
$venueContact = $connection->real_escape_string($_POST['venue_contact']);

// Begin transaction
$connection->begin_transaction();

try {
    // Update Event Details
    $eventStmt = $connection->prepare("UPDATE Events SET Title = ?, Description = ?, StartTime = ?, EndTime = ? WHERE EventID = ?");
    $eventStmt->bind_param("ssssi", $title, $description, $startTime, $endTime, $event_id);

    if (!$eventStmt->execute()) {
        throw new Exception("Error updating event: " . $eventStmt->error);
    }

    // Update Venue Details - Assuming venue details are stored in the Venues table and linked to the event
    $venueStmt = $connection->prepare("UPDATE Venues SET Name = ?, Location = ?, Capacity = ?, ContactInfo = ? WHERE VenueID = (SELECT VenueID FROM Events WHERE EventID = ?)");
    $venueStmt->bind_param("ssisi", $venueName, $venueLocation, $venueCapacity, $venueContact, $event_id);

    if (!$venueStmt->execute()) {
        throw new Exception("Error updating venue: " . $venueStmt->error);
    }

    
    if (isset($_FILES['event_image']['name']) && $_FILES['event_image']['error'] == 0) {
        $uploadDir = '../uploads/';
        $imageFileType = strtolower(pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION));
        $imageFileName = uniqid('event_img_', true) . '.' . $imageFileType;
        $imageFilePath = $uploadDir . $imageFileName;

        // Check if file is an actual image
        $check = getimagesize($_FILES['event_image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $imageFilePath)) {
                // Check if an image record already exists for the event
                $imageCheckStmt = $connection->prepare("SELECT ImageID FROM Images WHERE EventID = ?");
                $imageCheckStmt->bind_param("i", $event_id);
                $imageCheckStmt->execute();
                $result = $imageCheckStmt->get_result();

                if ($result->num_rows > 0) {
                    // Update existing image record
                    $imageUpdateStmt = $connection->prepare("UPDATE Images SET ImagePath = ?, ImageType = ? WHERE EventID = ?");
                    $imageUpdateStmt->bind_param("ssi", $imageFilePath, $imageFileType, $event_id);
                } else {
                    // Insert new image record
                    $imageInsertStmt = $connection->prepare("INSERT INTO Images (EventID, ImagePath, ImageType) VALUES (?, ?, ?)");
                    $imageInsertStmt->bind_param("iss", $event_id, $imageFilePath, $imageFileType);
                }

                if (isset($imageUpdateStmt) && !$imageUpdateStmt->execute()) {
                    throw new Exception("Error updating image record.");
                }
                if (isset($imageInsertStmt) && !$imageInsertStmt->execute()) {
                    throw new Exception("Error inserting new image record.");
                }
            } else {
                throw new Exception("Sorry, there was an error uploading your file.");
            }
        } else {
            throw new Exception("File is not an image.");
        }
    }


    // Commit transaction
    $connection->commit();
    header("Location: ../view/eventsmanagements.php");
    echo "Event, venue, and image (if provided) updated successfully.";

} catch (Exception $e) {
    // Rollback transaction on error
    $connection->rollback();
    echo "Transaction failed: " . $e->getMessage();
}

$connection->close(); // Close the database connection
?>
