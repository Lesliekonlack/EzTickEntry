<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="form-container">
    <div class="logo">EzTickEntry</div>
    <h2>Create Account</h2>
    <p>Already have an account? <a href="login.php">Log In</a></p>

    <form id="registrationForm" action="./action/register_user_action.php" method="post" onsubmit="return validateRegistrationForm()">
        <div class="form-row">
            <input  type="text" id= "firstName" name="firstName" placeholder="First Name" required>
            <input type="text"id= "lastname" name="lastName" placeholder="Last Name" required>
        </div>
        <input type="email" id= "email" name="email" placeholder="Email Address" required>
        <input type="tel" id= "phone" name="tel" placeholder="tel" required>

        <!-- Date of Birth Field -->
        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" placeholder="Date of Birth" required>


    <!-- Gender selection -->
    <select id="gender" name="gender" required>
        <option value="">Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
    </select>

        <div class="form-row">
            <input type="password" id= "password" name="password" placeholder="Password" required>
            <input type="password" id= "confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
        </div>
        <button type="submit">Create Account</button>
        <div class="divider">
        <span>or</span>
        </div>
        <button type="button" class="social-login facebook">Sign up using Facebook</button>
        <p class="terms">By creating an account you agree to our <a href="privacy_policy.php">privacy policy</a> &amp; <a href="terms_of_service.php">terms of service</a>.</p>

        
    </form>
</div>

<script>
function validateRegistrationForm() {
    var firstName = document.getElementById('firstName').value.trim();
    var lastName = document.getElementById('lastName').value.trim();
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirmPassword').value;

    var emailRegex = /^[a-z0-9._%+-]+@(ashesi\.edu\.gh|gmail\.com|yahoo\.com|hotmail\.com|outlook\.com)$/;
    var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return false;
    }

    if (!passwordRegex.test(password)) {
        alert('Password must be at least 8 characters long, including one uppercase letter, one lowercase letter, one number, and one special character.');
        return false;
    }

    if (password !== confirmPassword) {
        alert('Passwords do not match.');
        return false;
    }

    // If all validations pass
    alert('Registration successful!');
    // Consider adding form submission here if needed
    return true; // Proceed with form submission
}
</script>



<style>
body {
  font-family: Arial, sans-serif;
  background: #f7f7f7;
  margin: 0;
  padding: 20px;
}
.form-container {
    background: #fff;
    max-width: 400px;
    margin: 50px auto;
    padding: 30px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.form-container .logo {
    color: #4B0082; 
    font-size: 36px; 
    font-weight: bold;
    margin-bottom: 20px;
}

.form-container h2 {
    color: #4B0082; 
    font-weight: bold;
    margin: 10px 0;
}

.form-container p a {
    color: #4B0082;
    text-decoration: none;
}

.form-container .form-row {
    display: flex;
    gap: 10px;
}

.form-container .form-row input,
.form-container input[type=email],
.form-container input[type=password] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.form-container button {
    width: 100%;
    padding: 15px;
    margin-top: 20px;
    border: none;
    border-radius: 5px;
    background-color: #4B0082;
    color: white;
    cursor: pointer;
    font-size: 16px;
}

.form-container button.social-login {
    background-color: #3b5998;
    margin-bottom: 20px;
}

.form-container button.social-login.facebook {
    padding: 10px;
    font-size: 16px;
}

.divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 20px 0;
}

.divider:before,
.divider:after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #ccc;
}

.divider:before {
    margin-right: .25em;
}

.divider:after {
    margin-left: .25em;
}

.divider span {
    color: #bbb;
}

.terms {
    font-size: 12px;
    color: #666;
    margin-top: 20px;
}

.terms a {
    color: #4B0082;
    text-decoration: none;
}

/* Adjust the button styles on hover */
.form-container button:hover {
    background-color: #372b62; /* A darker shade for hover effect */
}

.form-container button.social-login.facebook:hover {
    background-color: #314d86; /* A darker shade for Facebook button hover effect */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .form-container {
        width: 90%;
        padding: 20px;
    }
}

</style>

<script src="script.js"></script>
</body>
</html>
