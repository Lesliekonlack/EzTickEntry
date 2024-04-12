<?php
include '../settings/connection.php'; // Ensure this path correctly points to your connection script

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieving and sanitizing form data
    $firstName = mysqli_real_escape_string($connection, $_POST['firstName'] ?? '');
    $lastName = mysqli_real_escape_string($connection, $_POST['lastName'] ?? '');
    $email = mysqli_real_escape_string($connection, $_POST['email'] ?? '');
    $contactNumber = mysqli_real_escape_string($connection, $_POST['tel'] ?? ''); // Assuming 'tel' is the field name for the contact number in your form
    $password = $_POST['password'] ?? ''; // Assuming 'password' is the field name in your form
    $gender = mysqli_real_escape_string($connection, $_POST['gender'] ?? ''); // Assuming 'gender' is the field name in your form
    $dateOfBirth = mysqli_real_escape_string($connection, $_POST['dob'] ?? ''); // Assuming 'dob' is the field name for date of birth in your form

    // Hashing the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Preparing the insert statement to include gender and date of birth
    $stmt = $connection->prepare("INSERT INTO Users (FirstName, LastName, Email, ContactNumber, PasswordHash, Gender, DateOfBirth) VALUES (?, ?, ?, ?, ?, ?, ?)");
    // Adjust the 'ssssss' to match the number and types of columns you have in your database, 's' for string, 'i' for integer, and 'd' for double.
    $stmt->bind_param("sssssss", $firstName, $lastName, $email, $contactNumber, $hashedPassword, $gender, $dateOfBirth);

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
