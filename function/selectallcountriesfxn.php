<?php
include '../settings/connection.php'; // Include your DB connection

$countries = [];
$sql = "SELECT CountryID, CountryName FROM Countries ORDER BY CountryName";
$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $countries[$row['CountryID']] = $row['CountryName'];
    }
}

// Display the countries for debugging purposes
echo '<pre>'; // Using <pre> to format the output
print_r($countries);
echo '</pre>';
?>