<?php
session_start(); 

include '../settings/connection.php'; 
$email = $_POST['email'];
$password = $_POST['password']; // Assuming 'password' is the field name in your form

// Adjusting the prepared statement to include FirstName, LastName, and IsSuperAdmin
// Write a query to SELECT a record from the People table using the email
$sql = "SELECT * FROM Users WHERE Email = '$email'";

// Execute the query
$result = $conn->query($sql);

// Check if any row was returned
if ($result->num_rows == 0) {
    
    echo '<script>
            alert("User email is incorrect or not registered!");
            setTimeout(function() {
                window.location.href = "../g_view/home.php";
            }, 100); // Delay in milliseconds
          </script>';
    exit(); // Make sure to exit after the alert and redirection
}
else{
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
        exit();
    }
} else {
    // If no record found with the provided email
    echo 'User not registered or incorrect email.';
}

$stmt->close();
$connection->close();
?>

