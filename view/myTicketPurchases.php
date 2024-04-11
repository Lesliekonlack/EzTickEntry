<?php
session_start();
include '../settings/connection.php';

$userId = $_SESSION['user_id'];
$confirmedTickets = [];

$stmt = $connection->prepare("
SELECT 
    U.FirstName, 
    T.TicketID, 
    TT.Name AS TierName, 
    TT.Price, 
    E.Title AS EventTitle, 
    E.Description, 
    E.StartTime, 
    E.EndTime, 
    V.Name AS VenueName, 
    V.Location AS VenueLocation,
    P.PaymentDate,
    P.PaymentID,
    I.ImagePath
FROM 
    Tickets T
INNER JOIN 
    TicketTiers TT ON T.TierID = TT.TierID
INNER JOIN 
    Bookings B ON T.BookingID = B.BookingID
INNER JOIN 
    Events E ON B.EventID = E.EventID
INNER JOIN 
    Venues V ON E.VenueID = V.VenueID
INNER JOIN 
    Payments P ON T.BookingID = P.BookingID
INNER JOIN 
    Users U ON B.UserID = U.UserID
LEFT JOIN 
    Images I ON E.EventID = I.EventID
WHERE 
    B.UserID = ? AND B.BookingStatusID = 4");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    while ($ticket = $result->fetch_assoc()) {
        $confirmedTickets[] = $ticket;
    }
} else {
    exit('Error with SQL query.');
}

// Debugging: Dump the confirmed tickets to see if we're getting results


$stmt->close();



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
        <a style= "margin-left: 300px;" href="userdashboard.php">Dashboard</a>
        <a href="#"  class="active">My Ticket Purchases</a>
        <a href="upcomingevents.php">Upcoming Events</a>
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
      <a href="upcomingevents.php">Upcoming</a>
      <!-- ... other links ... -->
      <a href="logout.php">Log Out</a>
    </div>

  </div>
<br>
</div>
<!-- Search input -->
<div class="dashboard-content">
    <h1>My Event Tickets</h1>
    <div class="accordion-container">
        <?php if (!empty($confirmedTickets)): ?>
            <?php 
                $groupedTickets = [];
                foreach ($confirmedTickets as $ticket) {
                    $groupedTickets[$ticket['EventTitle']][] = $ticket;
                }
            ?>
            <?php foreach ($groupedTickets as $eventTitle => $tickets): ?>
                <div class="accordion-item">
                    <div class="accordion-title" onclick="toggleAccordion(this)">
                        <h2><?php echo htmlspecialchars($eventTitle); ?></h2>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </div>
                    <div class="accordion-content">
                        <div class="ticket-grid">
                            <?php foreach ($tickets as $ticket): ?>
                                <div class="ticket-card">
                                    <div class="ticket-details">
                                        <div class="ticket-tier">
                                        <h2 style= "margin-left: 800px;" class="event-title"><?php echo htmlspecialchars($eventTitle); ?></h2>
                                        <h3 class="tier-name"><?php echo htmlspecialchars($ticket['TierName']); ?></h3>
                                            <div class="ticket-pricing">
                                                <p class="ticket-price">Price: $<?php echo htmlspecialchars(number_format($ticket['Price'], 2)); ?></p>
                                                <p class="seat-id">Seat ID: <?php echo strtoupper(substr(htmlspecialchars($ticket['TierName']), 0, 3)) . '-' . htmlspecialchars($ticket['PaymentID']); ?></p>
                                            </div>
                                        </div>
                                        <div class="ticket-timing">
                                            <p><strong>Date:</strong> <?php echo htmlspecialchars(date('Y-m-d', strtotime($ticket['StartTime']))); ?></p>
                                            <p><strong>Time:</strong> <?php echo htmlspecialchars(date('H:i', strtotime($ticket['StartTime']))); ?> - <?php echo htmlspecialchars(date('H:i', strtotime($ticket['EndTime']))); ?></p>
                                        </div>
                                        <div class="ticket-venue">
                                            <p><?php echo htmlspecialchars($ticket['VenueName'] . ', ' . $ticket['VenueLocation']); ?></p>
                                        </div>
                                        <div class="ticket-image">
                                        <img src="<?php echo htmlspecialchars($ticket['ImagePath']); ?>" alt="<?php echo htmlspecialchars($eventTitle); ?>" class="event-image">
                                    </div>
                                    </div>
                                    <div class="welcome-message">
                                        <?php echo htmlspecialchars("From EzTiickEntry: Thankyou, " . $ticket['FirstName'] . ' ' . $ticket['LastName'] . "! for continuing trusting us! Enjoy."); ?>
                                    </div>
                                    <div class="ticket-download">
                                        <button class="download-ticket-btn">Download</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tickets found. If you have recently confirmed a booking, please allow a few moments for processing.</p>
        <?php endif; ?>
    </div>
</div>



<!-- Your existing footer goes here -->

<script>
// JavaScript for handling payment confirmation actions
function toggleAccordion(element) {
    // This function simply toggles the display of the clicked accordion content
    var content = element.nextElementSibling;
    var icon = element.querySelector('.accordion-icon');

    // Toggle the display of the accordion content
    if (content.style.display === 'block') {
        content.style.display = 'none';
        icon.classList.remove('rotate-icon');
    } else {
        content.style.display = 'block';
        icon.classList.add('rotate-icon');
    }
}

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
    justify-content: center;
    

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
  right: 0; 
}

.dropdown-content a:hover {background-color: #ddd;}

/* User Dropdown Menu */
.user-dropdown {
    position: relative;
    margin-left: auto;
}

.tickets-container {
    padding: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.ticket {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    overflow: hidden;
    width: 300px; /* Adjust based on your preference */
}

.ticket-header {
    background-color: #4B0082;
    color: #fff;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ticket-header h2 {
    margin: 0;
    font-size: 1.2rem;
}

.ticket-price {
    font-weight: bold;
    font-size: 1.1rem;
}

.ticket-body {
    padding: 15px 20px;
    line-height: 1.5;
}

.ticket-footer {
    padding: 10px 20px;
    text-align: right;
}

.view-ticket-btn {
    background-color: #008CBA;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.view-ticket-btn:hover {
    background-color: #007B9E;
}

@media (max-width: 768px) {
    .tickets-container {
        flex-direction: column;
        align-items: center;
    }
}


/* Search Bar Styles */
.search-bar-container {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

.search-input {
    width: 100%;
    max-width: 300px; /* Set a max-width if needed */
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Add a bit of right margin if you want a search button next to it */
.search-input + .search-button {
    margin-left: 8px;
}

/* Style the search button if you have one */
.search-button {
    padding: 10px 15px;
    background-color: #4B0082; /* Deep purple theme */
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.search-button:hover {
    background-color: #673ab7; /* A lighter shade for hover effect */
}

/* Base setup */
* {
  box-sizing: border-box;
}


/* Ticket styling */
.dashboard-content {
    padding: 20px;
    max-width: 1200px;
    margin: auto;


}

.accordion-container {
    margin-top: 20px;
    
}



.accordion-item {
    background: #fff;
    border: 1px solid #eaeaea;
    border-radius: 8px;
    margin-bottom: 10px;
    overflow: hidden;
}

.accordion-title {
    background: #4B0082;
    color: white;
    cursor: pointer;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background-color 0.3s;
}

.accordion-title h2 {
    margin: 0;
}

.accordion-title:hover {
    background-color: #673ab7;
}

.accordion-icon {
    transition: transform 0.3s;
}

.rotate-icon {
    transform: rotate(180deg);
}

.accordion-content {
    display: none;
    padding: 20px;
    background: #f7f7f7;
}

.ticket-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(700px, 1fr));
    gap: 15px;
}

.ticket-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background-color: #fff;


}

.ticket-header {
    background-color: #fff; /* Pure white for contrast with the base */
    color: #333; /* Darker text for readability */
    padding: 15px;
    border-bottom: 1px solid #e2dedb; /* Separator */
}


.ticket-info {
    color: #666; /* Subtle, dark grey for information text */
    font-size: 0.9rem;
    margin: 15px 0; /* Space between sections */
}

.seat-id {
    color: #957DAD; /* Lighter lavender shade for a soft printed look */
    font-weight: bold;
}


.ticket-details {
    margin-bottom: 15px;
}

.ticket-tier h3 {
    margin: 0 0 5px;
    color: #4B0082;
}

.ticket-pricing,
.ticket-timing,
.ticket-venue {
    font-size: 0.9rem;
    color: #666;
}

.welcome-message {
    font-style: italic;
    background: #e7f4e8;
    padding: 10px;
    margin-bottom: 15px;
}

.download-ticket-btn {
    background-color: #4CAF50; /* A vibrant color for the primary action */
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px; /* Space from the last info to the button */
}

.download-ticket-btn:hover {
    background-color: #45a049;
}

.tier-name, .event-title {
    margin: 0; /* Remove default margins from headings */
}


.event-image {
    max-width: 180px; /* Adjust the maximum width as needed */
    height: auto;
    margin-left: 850px; /* Adjust the margin as needed */
    margin-top: -190px; //* Adjust the margin to push the image up */
    position: sticky; /* Make the image sticky */
    top: 20px; /* Adjust the distance from the top */
    border: 4px solid #430ACD; /* Add a border */
    border-radius: 10px; /* Add border radius for rounded corners */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); /* Add a shadow */
}

.event-title {
    background-image: linear-gradient(to right, #430ACD, #FFA500);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
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
    function searchEvent() {
        var input, filter, eventCards, title, i;
        input = document.getElementById('searchInput');
        filter = input.value.toUpperCase();
        eventCards = document.getElementsByClassName('event-card');
    
        for (i = 0; i < eventCards.length; i++) {
            title = eventCards[i].getAttribute('data-event-title').toUpperCase();
            if (title.indexOf(filter) > -1) {
                eventCards[i].style.display = "";
            } else {
                eventCards[i].style.display = "none";
            }
        }
    }
</script>
</body>
</html>
