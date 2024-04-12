<?php
$password = "Maman33"; // Replace this with the superadmin's intended password.
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo $hashedPassword;
?>
