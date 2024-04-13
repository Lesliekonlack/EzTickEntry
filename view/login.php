<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> Login </title>

</head>
<body>
<div class="form-container">
    <div class="logo">EzTickEntry</div>
    <h2>Welcome Back</h2>
    <?php if ($_SERVER['REQUEST_URI'] == "../action/register_user_action.php"): ?>
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    <?php else: ?>
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    <?php endif; ?>
    <form id="loginForm" action="../action/login_user_action.php" method="post">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <a href="forgot_password.php" class="forgot-password">Forgot your password?</a>
        <button type="submit">Log In</button>
        <div class="divider">
            <span>or</span>
        </div>
        <button type="button" class="social-login facebook">Sign in using Facebook</button>
    </form>
</div>


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
