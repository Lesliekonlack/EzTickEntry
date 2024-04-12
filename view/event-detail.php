<?php
session_start(); // Ensure session_start() is only called once at the top

include '../settings/connection.php'; 
include '../settings/core.php';
checkLogin();// Include your DB connection

// Fetch categories from the database and store them in an array
$eventCategories = [];
$sql = "SELECT CategoryID, CategoryName FROM EventCategories";
$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $eventCategories[$row['CategoryID']] = $row['CategoryName'];
    }
}

$superadminDetails = [];
$sql = "SELECT FirstName, LastName, ContactNumber FROM Users WHERE IsSuperAdmin = 1 LIMIT 1";
$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $superadminDetails['name'] = $row['FirstName'] . " " . $row['LastName'];
    $superadminDetails['phone'] = $row['ContactNumber'];
}


$bookingDetails = [];
$sql = "SELECT * FROM Bookings WHERE UserID = ? AND BookingStatusID = 1"; // Add condition for BookingStatusID
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $userId); // Assuming $userId is already set
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $bookingDetails = $result->fetch_assoc();
} else {
    // Handle case where no matching bookings are found
    $bookingDetails = [];
}


$user_id = $_SESSION['user_id'];



// Count the number of bookings for the current user
$user_id = $_SESSION['user_id']; // Assuming this is already set

// Count the number of bookings for the current user with BookingStatusID = 1
$sql = "SELECT COUNT(*) AS bookingCount FROM Bookings WHERE UserID = ? AND BookingStatusID = 1";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $bookingCount = $row['bookingCount'];
} else {
    // Handle error or no bookings found
    $bookingCount = 0;
}

$sql = "SELECT BookingID FROM Bookings WHERE UserID = ? AND BookingStatusID = 1";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    // Initialize an array to store the booking IDs
    $bookingIds = array();

    // Fetch each row and store the booking ID
    while ($row = $result->fetch_assoc()) {
        $bookingIds[] = $row['BookingID'];
    }
} else {
    // Handle error or no bookings found
    // You might want to set bookingIds to an empty array or handle the case differently
    $bookingIds = array();
}



// Output the booking count for the user
//echo "You currently have $bookingCount booking(s) for this event.";



$user_id = $_SESSION['user_id'];

// Fetch events managed by the logged-in user
$myEvents = [];

// SQL query to fetch event details along with associated ticket tiers and categories for all events excluding private events
$sql = "SELECT e.EventID, e.Title, e.Description, e.StartTime, e.EndTime, 
               v.Name AS VenueName, v.Location AS VenueLocation, v.Capacity AS VenueCapacity, 
               v.ContactInfo AS VenueContact, c.CountryName, i.ImagePath,
               ec.CategoryID, ec.CategoryName,  -- Include CategoryID and CategoryName
               t.TierID, t.Name AS TierName, t.Price, t.QuantityAvailable, t.IsActive
        FROM Events e
        JOIN Venues v ON e.VenueID = v.VenueID
        LEFT JOIN Countries c ON v.CountryID = c.CountryID
        LEFT JOIN Images i ON e.EventID = i.EventID
        LEFT JOIN EventCategories ec ON e.CategoryID = ec.CategoryID
        LEFT JOIN TicketTiers t ON e.EventID = t.EventID
        WHERE e.isPrivate = 0  -- Exclude private events
        ORDER BY e.EventID, t.TierID ASC";

$stmt = $connection->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $eventId = $row['EventID'];

        // Initialize event array if this is the first occurrence of the event ID
        if (!isset($myEvents[$eventId])) {
            $myEvents[$eventId] = [
                'EventID' => $row['EventID'],
                'Title' => $row['Title'],
                'Description' => $row['Description'],
                'StartTime' => $row['StartTime'],
                'EndTime' => $row['EndTime'],
                'Category' => [  // Include category information
                    'CategoryID' => $row['CategoryID'],
                    'CategoryName' => $row['CategoryName']
                ],
                'VenueName' => $row['VenueName'],
                'VenueLocation' => $row['VenueLocation'],
                'VenueCapacity' => $row['VenueCapacity'],
                'VenueContact' => $row['VenueContact'],
                'CountryName' => $row['CountryName'],
                'ImagePath' => $row['ImagePath'],
                'TicketTiers' => []  // Initialize an empty array for ticket tiers
            ];
        }

        // Add the ticket tier to the event if it exists
        if (!is_null($row['TierID'])) {
            $myEvents[$eventId]['TicketTiers'][] = [
                'TierID' => $row['TierID'],
                'TierName' => $row['TierName'],
                'Price' => $row['Price'],
                'QuantityAvailable' => $row['QuantityAvailable'],
                'IsActive' => $row['IsActive']
            ];

        }

    }
}

$eventId = $_GET['eventId'] ?? null;
$selectedEvent = [];

if ($eventId && array_key_exists($eventId, $myEvents)) {
    $selectedEvent = $myEvents[$eventId];
}

$sql = "SELECT BookingID, TotalPrice FROM Bookings WHERE UserID = ? ORDER BY BookingTime DESC LIMIT 1";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id); // 'i' indicates the type is integer
$stmt->execute();
$result = $stmt->get_result();

$currentBooking = [];

if ($result && $result->num_rows > 0) {
    $currentBooking = $result->fetch_assoc();
    // Now $currentBooking['BookingID'] and $currentBooking['TotalPrice'] contain the booking ID and total amount of the latest booking for the current user
} else {
    // Handle case where no booking exists for the user
    // You can set default values or display a message as needed
    $currentBooking['BookingID'] = 'No bookings';
    $currentBooking['TotalPrice'] = 0.00;
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
        <a href="userdashboard.php">Dashboard</a>
        <a href="loginentrypage.php">Homepage</a>
        <a href="upcomingevents.php" class="active">UpComing Events</a>

        <!-- Categories Dropdown -->
        <div class="categories-dropdown">
    <button class="categories-button">Host An Event <i class="fa fa-chevron-down"></i></button>
      <div class="dropdown-content2">
          <?php foreach ($eventCategories as $categoryId => $categoryName): ?>
              <?php if($categoryName == "Live Performances/Festivals"): ?>
                  <a href="hostliveperformanceevent.php?category=<?php echo urlencode($categoryId); ?>"><?php echo htmlspecialchars($categoryName); ?></a>
              <?php elseif($categoryName == "Cinema/Theatre"): ?>
                  <a href="hostcinemaevent.php?category=<?php echo urlencode($categoryId); ?>"><?php echo htmlspecialchars($categoryName); ?></a>
              <?php endif; ?>
          <?php endforeach; ?>
      </div>
  </div>
</div>

    
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
<?php if (!empty($selectedEvent)): ?>
    <div class="event-detail-container">
        <!-- Event image and title -->
        <div class="event-header" style="background-image: url('<?php echo htmlspecialchars($selectedEvent['ImagePath']); ?>');">
            <h1><?php echo htmlspecialchars($selectedEvent['Title']); ?></h1>
        </div>
        <br>
        <!-- Main event details -->
        <div style= "margin-top: -5%;"class="event-main-info">
            <h2>Date and Time</h2>
            <p><?php echo date('l, F jS, Y, g:i A', strtotime($selectedEvent['StartTime'])); ?> - <?php echo date('g:i A', strtotime($selectedEvent['EndTime'])); ?> MDT</p>
            
            <h2>Location</h2>
            <p><?php echo htmlspecialchars($selectedEvent['VenueName']); ?>, <?php echo htmlspecialchars($selectedEvent['VenueLocation']); ?></p>
            
            <h2>About</h2>
            <p><?php echo htmlspecialchars($selectedEvent['Description']); ?></p>
        </div>

        <br>
        <h2>Buy Tickets </h2>
        <!-- Ticket Tiers -->
        <div class="event-ticket-tiers">
    <?php if (!empty($selectedEvent['TicketTiers'])): ?>
        <?php $optionCounter = 1; ?>
        <?php foreach ($selectedEvent['TicketTiers'] as $tier): ?>
            <?php if ($tier['IsActive'] == 1): ?>
                <div class="ticket-tier">
                    <h2 class="ticket-option-title">Ticket Option <?php echo $optionCounter++; ?></h2>
                    <h3 class="tier-name"><?php echo htmlspecialchars($tier['TierName']); ?></h3>
                    <p>Price: $<?php echo htmlspecialchars(number_format($tier['Price'], 2)); ?></p>
                    <p>Available: <?php echo htmlspecialchars($tier['QuantityAvailable']); ?></p>
                    <!-- Update the "Make Payment" button to include a data attribute containing the tier ID -->
            <button class="btn make-payment-btn" data-tier-id="<?php echo $tier['TierID']; ?>">Buy Ticket</button>

                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($optionCounter == 1): ?> <!-- No active ticket tiers were found -->
            <div class="no-tickets-message">
                <p>Tickets are not yet available for this event.</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="no-tickets-message">
            <p>Tickets are not yet available for this event.</p>
        </div>
    <?php endif; ?>
</div>
</div>

    </div>
<?php else: ?>
    <p>Event details not found. Please choose a different event.</p>
<?php endif; ?>


<div id="paymentModal" class="modal2">
    <div class="modal2-content">
        <span class="close">&times;</span>
        <h2>Payment Instructions</h2>
        <p>To complete your payment, please send the ticket amount to the following MoMo number:</p>
        <p class="superadmin-contact"><strong><?php echo $superadminDetails['phone']; ?></strong> (<?php echo $superadminDetails['name']; ?>)</p>
        <p><strong>Your payment is secure.</strong> In the event of cancellation, <strong>refunds will be processed</strong> back to your Mobile Money account.</p>
        <p>After sending the payment, click "Confirm Payment" to notify the admin. Your ticket will be processed upon confirmation.</p>
        <p class ="currentbookingcount">You currently have <strong><?php echo $bookingCount; ?></strong> booking(s) for this event.</p>
        <button id="confirmPaymentButton" class="btn confirm-payment" data-booking-id="<?php echo $currentBooking['BookingID']; ?>" data-total-price="<?php echo $currentBooking['TotalPrice']; ?>" data-tier-id="<?php echo $tierId; ?>">Confirm Payment</button>


    </p>
    


    </div>
</div>

<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Confirm Action</h2>
        <p id="confirmationText">Would you like to make a booking?</p>
        <div class="modal-actions">
            <button id="confirmYes" class="btn"> Option 1/Yes</button>
            <button id="confirmNo" class="btn">Option 2/No</button>
        </div>
    </div>
</div>


 <style>

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


/* Footer styles */
.footer {
    background-color: #673ab7; /* Deep purple theme */
    color: #ffffff;
    text-align: center;
    padding: 20px 0;
    position: relative;
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

/* General styles and typography enhancements */
.event-detail-container {
    max-width: 1200px;
    margin: 2rem auto;
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}

.event-header {
    position: relative;
    height: 60vh;
    background-size: cover;
    background-position: center;
    display: flex;
    justify-content: flex-end;
    align-items: flex-end;
    padding: 3rem;
    transition: height 0.3s ease-in-out;
}

.event-header::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent, rgba(50, 50, 50, 0.7));
}

.event-header h1 {
    z-index: 2;
    font-size: 3.5rem;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    margin-bottom: 1rem;
    transition: font-size 0.3s ease-in-out;
}

/* Main content styling with improved spacing and layout */
.event-main-info, .event-ticket-tiers {
    padding: 3rem;
    background: #fafafa;
    margin-top: 4rem;
    border-radius: 8px;
    transition: margin-top 0.3s ease-in-out, padding 0.3s ease-in-out;
}

.event-main-info h2, .ticket-tier h3 {
    font-size: 2rem;
    color: #673AB7;
    margin-bottom: 1.5rem;
}

.event-main-info p, .ticket-tier p {
    font-size: 1.1rem;
    color: #333;
    line-height: 1.7;
}

/* Refined ticket tier layout */
.ticket-tier {
    padding-bottom: 20px; /* Space for content above the border */
    margin-bottom: 20px; /* Space to the next ticket tier */
    border-bottom: 1px solid #ccc; /* Light grey border for separation */
}

.ticket-tier:last-child {
    border-bottom: none; /* No border for the last item */
}


.ticket-option-title {
    font-size: 1.5rem;
    font-weight: bold;
    background: -webkit-linear-gradient(45deg, #673AB7, #FF6F61);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    margin-bottom: 1rem;
    display: inline-block;
    padding: 0.2rem 0.5rem;
    border-radius: 5px;
    position: relative;
}

.ticket-option-title::before {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(to right, #673AB7, #FF6F61);
    border-radius: 2px;
}

.tier-name {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 1.6rem;
    color: #4A148C; /* Deep purple for a rich, elegant look */
    font-weight: 600;
    position: relative;
    padding: 10px 0; /* Adequate spacing */
    transition: color 0.3s ease, transform 0.2s ease-out;
    cursor: pointer; /* Indicates interactivity */
}

.tier-name::before, .tier-name::after {
    content: '';
    position: absolute;
    bottom: -2px; /* Positioning the underline just below the text */
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(to right, #7B1FA2, #E1BEE7); /* Gradient from deep purple to light lavender */
    transform: scaleX(0); /* Initially scaled to 0 */
    transition: transform 0.3s ease-in-out;
}

.tier-name::before {
    transform-origin: bottom right; /* Animates from right to left on hover */
}

.tier-name::after {
    transform-origin: bottom left; /* Animates from left to right on hover */
}

.tier-name:hover::before, .tier-name:hover::after {
    transform: scaleX(1); /* Scales to full width on hover */
}

.tier-name:hover {
    color: #7B1FA2; /* Lightens the color on hover */
    transform: translateY(-5px); /* Elevates the text slightly for a subtle lift effect */
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Soft shadow for depth */
}


.btn {
    background-color: #673AB7;
    color: #fff;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: transform 0.3s ease-out, background-color 0.3s ease;
}

.btn:hover {
    transform: translateY(-3px);
    background-color: #5e2d9c;
}

.make-payment-btn {
    width: auto;
    display: block;
    text-align: center;
    margin: -5rem auto 0;
    margin-left: 900px;
}

/* Refined media queries for responsiveness and dynamic resizing */
@media (max-width: 768px) {
    .event-header {
        height: 40vh;
    }

    .event-header h1 {
        font-size: 2.5rem;
    }

    .event-main-info, .event-ticket-tiers {
        margin-top: -5rem;
        padding: 2rem;
    }
}

@media (max-width: 576px) {
    .event-detail-container {
        margin: 1rem;
        border-radius: 0;
    }

    .event-header h1 {
        font-size: 2rem;
    }
}

/* Additional interactive effects for desktop */
@media (hover: hover) {
    .event-header:hover::after {
        background: linear-gradient(180deg, transparent, rgba(50, 50, 50, 0.6));
    }
}

.modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%; 
    max-width: 500px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.modal-actions {
    text-align: center;
    margin-top: 20px;
}




/* The Modal (background) */
.modal2 {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 2; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal2-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
    max-width: 500px; /* Maximum width */
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.superadmin-contact {
    margin: 20px 0;
    font-size: 18px;
    font-weight: bold;
}

.confirm-payment {
    background-color: #4CAF50; /* Green */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = document.getElementById('paymentModal');
    const confirmationModal = document.getElementById('confirmationModal');
    const closeConfirmationButton = confirmationModal.querySelector('.close');
    const closePaymentButton = paymentModal.querySelector('.close');
    const confirmYesButton = document.getElementById('confirmYes');
    const confirmNoButton = document.getElementById('confirmNo');
    const confirmationText = document.getElementById('confirmationText');
    const paymentButtons = document.querySelectorAll('.make-payment-btn');
    const bookingCountElement = paymentModal.querySelector('.currentbookingcount'); // Selecting the paragraph for booking count

    function showConfirmationModal(message, onConfirm, onCancel) {
        confirmationText.innerText = message;
        confirmationModal.style.display = "block";

        confirmYesButton.onclick = function() {
            confirmationModal.style.display = "none";
            onConfirm();
        };

        confirmNoButton.onclick = function() {
            confirmationModal.style.display = "none";
            if (onCancel) onCancel();
        };

        closeConfirmationButton.onclick = function() {
            confirmationModal.style.display = "none";
            if (onCancel) onCancel();
        };

        window.onclick = function(event) {
            if (event.target == confirmationModal) {
                confirmationModal.style.display = "none";
                if (onCancel) onCancel();
            } else if (event.target == paymentModal) {
                paymentModal.style.display = "none";
            }
        };
    }

    closePaymentButton.onclick = function() {
        paymentModal.style.display = "none";
        location.reload(); // Reload the page when the payment modal is closed
    };

    paymentButtons.forEach(button => button.addEventListener('click', function() {
        const tierId = this.getAttribute('data-tier-id');

        showConfirmationModal(`You currently have ${extractBookingCount()} booking(s) for this event. Would you like to add a new booking or manage existing ones?`, 
            function() {
                makeBooking(tierId);
            }, 
            function() {
                manageBookingsFlow(tierId);
            }
        );
    }));

        function makeBooking(tierId) {
        var data = new FormData();
        data.append('tier_id', tierId);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../action/booking_event_action.php', true);
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    updateBookingCount(extractBookingCount() + 1); 
                    // Show the payment modal after successful booking
                    showPaymentModal(); 
                } else {
                    alert('Booking failed: ' + response.message);
                }
            } else {
                alert('Error: ' + xhr.statusText);
            }
        };
        xhr.onerror = function() {
            alert('Request failed');
        };
        xhr.send(data);
    }

    function showPaymentModal() {
        // Perform any actions needed to show the payment modal
        paymentModal.style.display = 'block';
    }


    function manageBookingsFlow(tierId) {
        showConfirmationModal('Would you like to delete existing bookings?', function() {
            deleteBookingsFlow(tierId);
        }, function() {
            // No action if user cancels the deletion process
        });
    }
    function deleteBookingsFlow(tierId) {
    let validInput = false;

    while (!validInput) {
        const deleteCount = prompt(`Enter the number of bookings to delete (1-${extractBookingCount()}), or enter '0' to delete all:`);

        if (deleteCount === null) { // User pressed cancel on the prompt
            validInput = true; // Break the loop
        } else {
            const deleteNumber = parseInt(deleteCount);

            if (!isNaN(deleteNumber) && deleteNumber >= 0 && deleteNumber <= extractBookingCount()) {
                validInput = true; // Valid input, break the loop
                deleteBookings(deleteNumber, () => {
                    if (deleteNumber > 0) {
                        alert(`${deleteNumber} booking(s) have been deleted.`);
                        location.reload(); // Reload the page after showing the deletion success message
                    } else {
                        location.reload(); // Reload the page even if no bookings were deleted (e.g., when deleting 0 bookings)
                    }
                });
            } else {
                alert(`Invalid input. Please enter a number between 0 and ${extractBookingCount()} (0 to delete all).`);
                // The loop will continue, prompting the user to try again
            }
        }
    }
}


    function deleteBookings(count, callback) {
    var data = new FormData();
    data.append('delete_count', count);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../action/delete_bookings_action.php', true);
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 400) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert(response.message); // Show success message after deletion
                callback();
                location.reload(); // Reload the page after successful deletion
            } else {
                alert('Deletion failed: ' + response.message);
            }
        } else {
            alert('Error: ' + xhr.statusText);
        }
    };
    xhr.onerror = function() {
        alert('Request failed');
    };
    xhr.send(data);
}

    function extractBookingCount() {
        return parseInt(bookingCountElement.innerText.match(/\d+/)[0], 10);
    }

    function updateBookingCount(count) {
        bookingCountElement.innerHTML = `You currently have <strong>${count}</strong> booking(s) for this event.`;
    }
});
</script>

<script>
document.getElementById('confirmPaymentButton').addEventListener('click', function() {
    var tierId = this.getAttribute('data-tier-id'); 
    localStorage.setItem('showPaymentModal', 'true');
    location.reload();
});

window.onload = function() {
    if (localStorage.getItem('showPaymentModal') === 'true') {
        document.getElementById('paymentModal').style.display = 'block';
        localStorage.removeItem('showPaymentModal');

        var confirmButton = document.getElementById('confirmPaymentButton');
        var bookingId = confirmButton.getAttribute('data-booking-id');
        var totalPrice = confirmButton.getAttribute('data-total-price');
        var tierId = confirmButton.getAttribute('data-tier-id'); // Fetch the TierID from the button

        if (bookingId !== 'No bookings') {
            var paymentOption = prompt("How many pending bookings would you like to pay for? Enter a number from 1 to <?php echo $bookingCount; ?>, or enter 'all' to pay for all pending bookings:");

            if (paymentOption !== null) {
                var numberOfBookings;
                if (paymentOption.toLowerCase() === 'all') {
                    numberOfBookings = <?php echo $bookingCount; ?>;
                } else {
                    numberOfBookings = parseInt(paymentOption);
                }

                if (!isNaN(numberOfBookings) && numberOfBookings > 0 && numberOfBookings <= <?php echo $bookingCount; ?>) {
                    var formData = new FormData();
                    formData.append('bookingId', bookingId);
                    formData.append('totalPrice', totalPrice);
                    formData.append('tierId', tierId); // Append the TierID to the form data
                    formData.append('numberOfBookings', numberOfBookings);
                    
                    <?php foreach ($bookingIds as $id) { ?>
                        formData.append('bookingIds[]', '<?php echo $id; ?>');
                    <?php } ?>

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '../action/insert_payment.php', true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            alert('Paymennt Successful and Ticket(s) created');
                            location.href ='../view/myTicketPurchases.php';
                                
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    };
                    xhr.send(formData);
                } else {
                    alert('Invalid input. Please enter a valid number of bookings to pay for.');
                }
            } else {
                alert('Payment cancelled.');
            }
        } else {
            alert('No booking ID available to confirm payment.');
        }
    }
};
</script>




</body>
</html>
