<?php
include '../settings/connection.php'; // Ensure this path correctly points to your connection script

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieving and sanitizing form data
    $firstName = mysqli_real_escape_string($connection, $_POST['firstName'] ?? '');
    $lastName = mysqli_real_escape_string($connection, $_POST['lastName'] ?? '');
    $email = mysqli_real_escape_string($connection, $_POST['email'] ?? '');
    $contactNumber = mysqli_real_escape_string($connection, $_POST['tel'] ?? ''); // Assuming 'tel' is the field name for contact number in your form
    $password = $_POST['password'] ?? ''; // Assuming 'password' is the field name in your form

    // Hashing the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Preparing the insert statement
    $stmt = $connection->prepare("INSERT INTO Users (FirstName, LastName, Email, ContactNumber, PasswordHash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $contactNumber, $hashedPassword);

    // Execute and check the result
    if ($stmt->execute()) {
        echo "Registration successful!"; // Printing a success message
        // Redirect to login page after successful registration
        header("Location: ../view/login.php"); 
        exit(); // Ensure no further execution after redirect
    } else {
        echo "Error: " . $stmt->error; // Printing the error message from MySQL
    }

    $stmt->close();
    $connection->close();
} else {
    // Printing an error if not accessed via POST
    echo "This script must be accessed via a POST request.";
}
?>

