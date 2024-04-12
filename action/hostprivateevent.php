<?php
session_start(); // Start the session to access session variables

require_once '../settings/connection.php'; // Include your database connection script
require_once '../settings/core.php'; // Include core functionalities

checkLogin(); // Ensure the user is logged in before proceeding

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check for POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("This action requires a POST request.");
}

// Define required fields for the venue and initialize an error array
$venueRequiredFields = ['venueName', 'venueCountry', 'venueLocation', 'venueCapacity', 'venueContact'];
$eventRequiredFields = ['title', 'category_id', 'description', 'start_time', 'end_time'];
$errors = [];

// Validate each required field for both venue and event
foreach (array_merge($venueRequiredFields, $eventRequiredFields) as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "The '$field' field is required.";
    }
}

// Check for errors so far
if (!empty($errors)) {
    die("Errors: " . implode(", ", $errors));
}

// Venue details
$venueName = $connection->real_escape_string($_POST['venueName']);
$venueCountry = $connection->real_escape_string($_POST['venueCountry']);
$venueLocation = $connection->real_escape_string($_POST['venueLocation']);
$venueCapacity = (int)$_POST['venueCapacity'];
$venueContact = $connection->real_escape_string($_POST['venueContact']);

// Event details
$title = $connection->real_escape_string($_POST['title']);
$category_id = (int)$_POST['category_id'];
$description = $connection->real_escape_string($_POST['description']);
$startTime = $connection->real_escape_string($_POST['start_time']);
$endTime = $connection->real_escape_string($_POST['end_time']);
$organizerID = $_SESSION['user_id'];
$defaultEventStatusID = 1;

// Set IsPrivate to true by default
$isPrivate = true;

// Image upload process
$uploadDir = '../uploads/';
$uploadStatus = false;
$imageFilePath = '';

if (isset($_FILES['event_image']['name']) && $_FILES['event_image']['error'] == 0) {
    // Process upload
    $imageFileName = basename($_FILES['event_image']['name']);
    $imageFilePath = $uploadDir . $imageFileName;
    $imageFileType = strtolower(pathinfo($imageFilePath, PATHINFO_EXTENSION));
    
    // Check if file is an actual image
    $check = getimagesize($_FILES['event_image']['tmp_name']);
    if ($check !== false) {
        // File is an image - move it to the uploads directory
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $imageFilePath)) {
            $uploadStatus = true;
        } else {
            $errors[] = "Sorry, there was an error uploading your file.";
        }
    } else {
        $errors[] = "File is not an image.";
    }
} else {
    $errors[] = "No file was uploaded or there was an upload error.";
}

// Insert Venue
$venueStmt = $connection->prepare("INSERT INTO Venues (Name, CountryID, Location, Capacity, ContactInfo) VALUES (?, ?, ?, ?, ?)");
if (!$venueStmt) {
    die("Error preparing venue statement: " . $connection->error);
}
$venueStmt->bind_param("sssii", $venueName, $venueCountry, $venueLocation, $venueCapacity, $venueContact);
if (!$venueStmt->execute()) {
    die("Error executing venue statement: " . $venueStmt->error);
}
$venueID = $venueStmt->insert_id;
$venueStmt->close();

// Check for any errors before inserting event and image
if (!empty($errors)) {
    die("Errors: " . implode(", ", $errors));
}

// Insert Event with the new VenueID
$eventStmt = $connection->prepare("INSERT INTO Events (OrganizerID, CategoryID, Title, Description, StartTime, EndTime, VenueID, IsPrivate, EventStatusID, CreationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
if (!$eventStmt) {
    die("Error preparing event statement: " . $connection->error);
}
$eventStmt->bind_param("iissssiii", $organizerID, $category_id, $title, $description, $startTime, $endTime, $venueID, $isPrivate, $defaultEventStatusID);
if ($eventStmt->execute()) {
    $eventID = $eventStmt->insert_id;
    echo "Event inserted successfully with Venue ID: $venueID. Event ID: $eventID.<br>";
} else {
    die("Error executing event statement: " . $eventStmt->error);
}
$eventStmt->close();

// Insert Image if upload was successful
if ($uploadStatus) {
    $imageStmt = $connection->prepare("INSERT INTO Images (EventID, ImagePath, ImageType, UploadDate) VALUES (?, ?, ?, NOW())");
    if (!$imageStmt) {
        die("Error preparing image statement: " . $connection->error);
    }
    $imageStmt->bind_param("iss", $eventID, $imageFilePath, $imageFileType);
    if (!$imageStmt->execute()) {
        die("Error executing image statement: " . $imageStmt->error);
    }
    $imageStmt->close();
    echo "Image uploaded successfully.<br>";
}

$connection->close(); // Close the database connection
?>
