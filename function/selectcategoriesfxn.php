<?php
include '../settings/connection.php'; // Include your DB connection

$eventCategories = [];
$sql = "SELECT CategoryID, CategoryName FROM EventCategories";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $eventCategories[$row['CategoryID']] = $row['CategoryName'];
    }
}

// Display the categories for debugging purposes
echo '<pre>'; // Using <pre> to format the output
print_r($eventCategories);
echo '</pre>';
?>



