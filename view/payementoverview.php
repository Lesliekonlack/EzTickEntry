
<?php
session_start(); // Ensure session_start() is only called once at the top

include '../settings/connection.php'; 
include '../settings/core.php';
checkLogin();// Include your DB connection

$user_id = $_SESSION['user_id'];

// Fetch categories from the database and store them in an array
$eventCategories = [];
$sql = "SELECT CategoryID, CategoryName FROM EventCategories";
$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $eventCategories[$row['CategoryID']] = $row['CategoryName'];
    }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - EzTickEntry</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6vP0k0lVj8BcMxFpwW3KwPsAwVoiuBxHnxD4GKIObL5XsFL+2N8H5lyJvGoeK2o" crossorigin="anonymous">
</head>
<body>

<div class="navbar">
    <div class="logo" style="color: #673ab7;">EzTickEntry</div> <!-- The style attribute seems to be missing the 'color' property -->
    <div class="nav-items">
        <a href="superadmindashboard.php" class="active">Dashboard</a>
        <a href="loginentrypage.php">Homepage</a>
        <!-- Categories Dropdown -->


            
</div>


<!-- Notification message -->
<?php
if (isset($_POST['confirmPayment'])) {
    echo '<div class="notification">Payment confirmed!</div>';
}
?>


    
  <div class="user-dropdown">
  <?php
    session_start();
    if (isset($_SESSION['fname']) && isset($_SESSION['lname'])) {
      echo '<button class="user-name-button"><i class="fa fa-user-circle" aria-hidden="true"></i> ' . htmlspecialchars($_SESSION['fname']) . ' ' . htmlspecialchars($_SESSION['lname']) . ' <i class="fa fa-chevron-down"></i></button>';
    } else {
      echo '<button class="login-button">Login</button>';
    }
    ?>
    <div class="dropdown-content">
      <a href="upcoming_events.php">Upcoming</a>
      <a href="orders.php">Orders</a>
      <a href="waitlists.php">Waitlists</a>
      <a href="memberships.php">Memberships</a>
      <!-- ... other links ... -->
      <a href="logout.php">Log Out</a>
    </div>

  </div>

</div>


<!-- Your existing navbar goes here -->

<div class="dashboard-content">
    <h1>Payment Notifications</h1>
    <div class="notifications-container">
        <?php if (!empty($paymentsAwaitingConfirmation)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paymentsAwaitingConfirmation as $payment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['PaymentID']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($payment['Amount'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($payment['PaymentMethod']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($payment['PaymentDate']))); ?></td>
                            <td><?php echo htmlspecialchars($payment['FirstName'] . ' ' . $payment['LastName']); ?></td>
                            <td><?php echo htmlspecialchars($payment['EventTitle']); ?></td>
                            <td>
                                <button class="btn confirm-payment-btn" data-payment-id="<?php echo $payment['PaymentID']; ?>">Confirm</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No payment notifications awaiting confirmation.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Your existing footer goes here -->

<script>
// JavaScript for handling payment confirmation actions
document.addEventListener('DOMContentLoaded', function() {
    const confirmButtons = document.querySelectorAll('.confirm-payment-btn');

    confirmButtons.forEach(button => {
        button.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-payment-id');
            // Implement the logic to confirm the payment and update the database
            // This might involve sending an AJAX request to a PHP script that handles the confirmation logic
            console.log(`Confirming payment with ID: ${paymentId}`);
        });
    });
});
</script>

<style>

body, html {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    color: #333;
    background: #f4f4f4; /* Light background for contrast */
}

/* Navbar styles */
.navbar {
    position: sticky;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: white; /* Deep purple theme */
    color: black;
    padding: 10px ;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1000000;
}

.navbar .logo {
    font-size: 24px;
    font-weight: bold;
    color:  #4B0082;
}

.nav-items a {
    color: black;
    margin: 0 15px;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.nav-items a:hover, .nav-items a.active {
    background-color: #ddd;/* Lighter purple for hover and active states */
}

/* Dashboard Content */
.dashboard-content {
    padding: 20px;
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(5, minmax(250px, 1fr)); /* Define 5 columns per row */
    gap: 20px;
    margin-top: 20px;
}

.card-link {
    text-decoration: none; /* Remove underline from links */
    color: white; /* Keep text color consistent with other cards */
}

.card-link .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for hover effect */
}

.card-link .card:hover {
    transform: translateY(-5px); /* Lift card on hover */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); /* Increase shadow for depth effect */
}

.card {
    background: #4B0082;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: start;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
}

.card i {
    font-size: 30px;
    margin-right: 15px;
    color: #673ab7; /* Icon color to match the theme */
}


/* Footer styles */
.footer {
    background-color: #673ab7; /* Deep purple theme */
    color: #ffffff;
    text-align: center;
    padding: 20px 0;
    position: absolute;
    bottom: 0;
    width: 100%;
}
.user-name-button {
    background-color: #673ab7;
    color: white;
    padding: 10px;
    font-size: 16px;
    border: none;
    cursor: pointer;
}

.user-name-button:hover, .user-name-button:focus {
    background-color: #7e57c2;
}


.dropdown-content {
  display: none; /* Hide by default */
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  right: 0; /* Align to the right of the button */
  border-radius: 5px; /* Optional */
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  white-space: nowrap; /* Ensure the text doesn't wrap */
}

.dropdown-content a:hover {background-color: #ddd;}

/* User Dropdown Menu */
.user-dropdown {
    position: relative;
    display: inline-block;
}

.categories-button {
    background-color: white;
    color: black
    padding: 10px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    
}

.categories-button:hover, .categories-button:focus {
    background-color:  #ddd;
    box-shadow: 0 4px 100px rgba(0,0,0,0.2);
  
  /* Increased padding */

}



.categories-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content2 { /* Adjusted class name */
  display: none; /* Hide by default */
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  right: 5; /* Align to the right of the button */
  border-radius: 5px; /* Optional */
}

.dropdown-content2 a { /* Adjusted class name */
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    white-space: nowrap; 
}


.dropdown-content2 a:hover {background-color: #ddd;}


.notifications-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    overflow-x: auto; /* Ensures table is scrollable on small screens */
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    text-align: left;
    padding: 12px;
    border-bottom: 1px solid #ddd; /* Adds a light line between rows */
}

th {
    background-color: #4B0082;
    color: white;
}

tr:hover {
    background-color: #f2f2f2; /* Adds a hover effect for each row */
}

.confirm-payment-btn {
    background-color: #4CAF50; /* Green color for confirmation buttons */
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.confirm-payment-btn:hover {
    background-color: #45a049; /* Darker green on hover for confirmation buttons */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dashboard-content {
        padding: 20px 10px; /* Reduces padding on smaller screens */
    }

    .notifications-container {
        padding: 15px; /* Adjusts padding for the container */
    }

    th, td {
        padding: 10px; /* Reduces padding inside table cells */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
  // Get the button that opens the dropdown
  var dropdownBtn = document.querySelector('.user-name-button');
  
  // When the user clicks on the button, toggle the dropdown
  dropdownBtn.onclick = function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  }

  // Close the dropdown if the user clicks outside of it
  window.onclick = function(event) {
    if (!event.target.matches('.user-name-button')) {
      var dropdownContents = document.getElementsByClassName("dropdown-content");
      for (var i = 0; i < dropdownContents.length; i++) {
        var openDropdown = dropdownContents[i];
        if (openDropdown.style.display === "block") {
          openDropdown.style.display = "none";
        }
      }
    }
  }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var categoriesButton = document.querySelector('.categories-button');
    // Adjust the selector to target '.dropdown-content2' within '.categories-dropdown'
    var dropdownContent = document.querySelector('.categories-dropdown .dropdown-content2');
    
    categoriesButton.onclick = function(event) {
        event.stopPropagation(); // Stop the click event from propagating to the window
        // Toggle the display of the dropdown content
        dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
    };

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        // Check if the click is outside the dropdown content
        if (!event.target.closest('.categories-dropdown')) {
            dropdownContent.style.display = 'none';
        }
    };
});
</script>
</body>
</html>
