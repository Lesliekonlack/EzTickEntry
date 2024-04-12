<?php
session_start(); // Starting the session

include '../settings/connection.php'; // Ensure this path correctly points to your connection script

// Checking if the login form was submitted

    // Sanitizing email input to prevent SQL Injection
    $email = $_POST['email'];
    $password = $_POST['password']; // Assuming 'password' is the field name in your form

    // Adjusting the prepared statement to include FirstName, LastName, and IsSuperAdmin
    $stmt ="SELECT UserID, FirstName, LastName, PasswordHash, IsSuperAdmin FROM Users WHERE Email = ?";
    $result = $stmt->$conn->query($stmt);

    // Checking if any row was returned
    if ($result->num_rows > 0) {
        // Fetching the record
        $user = $result->fetch_assoc();

        // Verifying the password user provided against the hash stored in the database
        if (password_verify($password, $user['PasswordHash'])) {
            // If it's a match, storing user's details in the session
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['fname'] = $user['FirstName'];
            $_SESSION['lname'] = $user['LastName'];
            $_SESSION['is_super_admin'] = $user['IsSuperAdmin']; // Storing IsSuperAdmin in the session

            // Redirecting to a specific page after successful login
            header("Location: ../view/loginentrypage.php");
            exit();
        } else {
            // If password verification fails
            echo 'Incorrect password.';
            header("Location: ../view/entrypage.php");
        }
    } else {
        // If no record found with the provided email
        echo 'User not registered or incorrect email.';
    }

    $stmt->close();
    $connection->close();
} else {
    // If the form wasn't submitted properly
    echo 'Please submit the login form.';
}
?>


