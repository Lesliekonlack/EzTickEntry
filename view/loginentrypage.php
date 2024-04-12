<?php
session_start(); // Ensure session_start() is only called once at the top

include '../settings/connection.php'; 
include '../settings/core.php';
checkLogin();// Include your DB connection

$user_id = $_SESSION['user_id'];

// Fetch events managed by the logged-in user
$myEvents = [];

// SQL query to fetch event details along with associated ticket tiers and categories for all events excluding private events
$sql = "SELECT e.EventID, e.Title, e.Description, e.StartTime, e.EndTime, 
v.Name AS VenueName, v.Location AS VenueLocation, v.Capacity AS VenueCapacity, 
v.ContactInfo AS VenueContact, c.CountryName, i.ImagePath,
ec.CategoryID, ec.CategoryName,
t.TierID, t.Name AS TierName, t.Price, t.QuantityAvailable, t.IsActive
FROM Events e
JOIN Venues v ON e.VenueID = v.VenueID
LEFT JOIN Countries c ON v.CountryID = c.CountryID
LEFT JOIN Images i ON e.EventID = i.EventID
LEFT JOIN EventCategories ec ON e.CategoryID = ec.CategoryID
LEFT JOIN TicketTiers t ON e.EventID = t.EventID
WHERE e.isPrivate = 0 
AND e.EndTime > NOW()  -- End time has not passed
AND e.EventStatusID = 1  -- Event status is active
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magical Event Entry</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6vP0k0lVj8BcMxFpwW3KwPsAwVoiuBxHnxD4GKIObL5XsFL+2N8H5lyJvGoeK2o" crossorigin="anonymous">
</head>
<body>
  
<?php

include_once '../settings/core.php'; 
checkLogin();

include_once '../settings/connection.php'; 
?>

<div class="navbar">
  <div class="logo">EzTickEntry</div>
  <div class="search-area">
  <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
    <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128s57.3-128 128-128 128 57.2 128 128-57.3 128-128 128z"/>
  </svg>
    <input type="text" class="search-box" id="search-box" placeholder="Search events">
    <div class="search-results-dropdown"></div>

  
    <input type="date" class="date-picker" id="date-picker">
  </div>
  <div class="locale-switcher">
    <button onclick="changeLanguage('EN')">EN</button>
    <button onclick="changeLanguage('FR')">FR</button>
  </div>
  <?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Check if the user is a super admin
    if ($_SESSION['is_super_admin'] == 1) {
        // If user is a super admin, link to superadmin.php
        $dashboardLink = "superadmindashboard.php";
    } else {
        // If user is not a super admin, link to userdashboard.php
        $dashboardLink = "userdashboard.php";
    }
} else {
    // If the user is not logged in, link to login.php
    $dashboardLink = "entrypage.php";
}
?>

<a href="<?php echo $dashboardLink; ?>" style="text-decoration: none;">
    <button class="dashboard">Dashboard</button>
</a>


  
  <!-- User Dropdown Menu -->
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



<div id="search-results"></div>

<div class="content-wrapper">
  

  <div class="parent-container">
    <div class="container">
      <div class="image1"></div>
      <div class="image2"></div>
    </div>
  </div>


  <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/d23fd3d71881b09759c77c0eb655c169375a1e22/happy-multicultural-friends-having-fun-summer-festival-cheerful-multiracial-students-embrace_158001-4204.jpg.avif" style="position: absolute; left: -2%; top: 10px; width: 1300px; z-index: 10001;-webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent);
  mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent);"> 




    
  <img src="https://raw.githubusercontent.com/naomikonlack/WEBTECHGITDEMO/d23fd3d71881b09759c77c0eb655c169375a1e22/oakImage-1556811830330-superJumbo.jpg" style="position: absolute; left: 44%; top: 8px; width: 1120px; z-index: 10001;-webkit-mask-image: linear-gradient(to right, transparent, black 24%, black 90%, transparent);
  mask-image: linear-gradient(to right, transparent, black 24%, black 90%, transparent);">


  <div class="w_container" style="margin-top: -350px; z-index: 100000; margin-left:-1%">
    <div class="word"><div class="letter">G</div><div class="letter">E</div><div class="letter">T</div></div>
    <div class="word"><div class="letter">Y</div><div class="letter">O</div><div class="letter">U</div><div class="letter">R</div></div>
    <div class="word"><div class="letter">E</div><div class="letter">A</div><div class="letter">Z</div><div class="letter">Y</div></div>
    <div class="word"><div class="letter">T</div><div class="letter">I</div><div class="letter">C</div><div class="letter">K</div><div class="letter">E</div><div class="letter">T</div></div>
    <div class="word"><div class="letter">E</div><div class="letter">N</div><div class="letter">T</div><div class="letter">R</div><div class="letter">I</div><div class="letter">E</div><div class="letter">S</div></div>
    <div class="word"><div class="letter">F</div><div class="letter">O</div><div class="letter">R</div></div>
    <div class="word"><div class="letter">E</div><div class="letter">V</div><div class="letter">E</div><div class="letter">N</div><div class="letter">T</div><div class="letter">S</div></div>
  </div>

  <div class="w2_container" style="margin-top: 50px; z-index: 1000000; margin-left:-1%">
    <div class="word"><div class="letter">A</div><div class="letter">L</div><div class="letter">L</div></div>
    <div class="word"><div class="letter">A</div><div class="letter">R</div><div class="letter">O</div><div class="letter">U</div><div class="letter">N</div><div class="letter">D</div></div>
    <div class="word"><div class="letter">T</div><div class="letter">H</div><div class="letter">E</div></div>
    <div class="word"><div class="letter">W</div><div class="letter">O</div><div class="letter">R</div><div class="letter">L</div><div class="letter">D</div></div>
    </div>
    <div class="about" >
    <p style="font-size: 1.2em; text-align: justify;">
    Embark on an effortless journey to entertainment with EzTickEntry, your ultimate portal to the world of events. We're here to transform how you discover, book, and manage tickets for an extensive array of entertainment spectacles. Whether it's immersing yourself in cinema, feeling the pulse of live performances, or celebrating momentous private occasions, EzTickEntry is your ticket to an unforgettable experience.
    </p>
  </div>

  <br>
  <h2 class="Top Events" style="color: #4B0082; margin-top: 100px; padding-left: 174px;">Our Categories</h2>
  <div class="categories-container" style="padding-left: 174px;">
    <div class="category">
      <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/dccb68e43ece051e4b01ad41b121c945e049a524/people-s-emotions-cinema1-1024x683.jpg" alt="Cinema Tickets">
      <span class="category-name">Cinema <br>/Theatres Tickets</span>
    </div>
    <div  class="category">
      <img  src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/dccb68e43ece051e4b01ad41b121c945e049a524/rock-on-rear-view-of-a-music-fan-dancing-with-her-2023-01-04-20-11-49-utc.jpg" alt="Live Performances">
      <span class="category-name">Live Performances<br>/Festivals/Other Events Tickets</span>
    </div>
   
  </div>

  <h2 class="Top Events" style="color: #4B0082; margin-top: 10px; padding-left: 174px;">Upcoming Events</h2>
<div class="events-grid" style="margin-top: -150px;">
    <?php foreach ($myEvents as $event): ?>
        <div class="event-card" 
             data-event-title="<?php echo htmlspecialchars($event['Title']); ?>" 
             data-event-date="<?php echo date('Y-m-d', strtotime($event['StartTime'])); ?>">
            <div class="event-image">
                <img src="<?php echo htmlspecialchars($event['ImagePath']); ?>" alt="<?php echo htmlspecialchars($event['Title']); ?>">
            </div>
            <div class="event-info">
                <div class="event-date"><?php echo date('M d Y', strtotime($event['StartTime'])); ?></div>
                <h2 class="event-title"><?php echo htmlspecialchars($event['Title']); ?></h2>
                <div class="event-location"><?php echo htmlspecialchars($event['VenueName']); ?>, <?php echo htmlspecialchars($event['VenueLocation']); ?></div>
                <div class="event-pricing">
                <?php if (!empty($event['TicketTiers'])): ?>
                    <?php
                    $activeTiers = array_filter($event['TicketTiers'], function ($tier) {
                        return $tier['IsActive']; // Filter only active tiers
                    });

                    if (!empty($activeTiers)) {
                        $prices = array_column($activeTiers, 'Price'); // Extract prices from active tiers
                        $minPrice = min($prices);
                        $maxPrice = max($prices);
                    ?>
                        <span class="price">$<?php echo htmlspecialchars(number_format($minPrice, 2)); ?>
                            - $<?php echo htmlspecialchars(number_format($maxPrice, 2)); ?></span>
                    <?php } ?>
                <?php endif; ?>
                    <span class="start-time"><?php echo date('g:i A', strtotime($event['StartTime'])); ?> MDT</span>
                </div>
            </div>
            <div class="event-card-footer">
                <button class="btn view-tickets-btn" onclick="window.location.href='event-detail.php?eventId=<?php echo $event['EventID']; ?>'">View Tickets</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>


  <h2 style="color: #4B0082;  margin-top: 70px; padding-left: 174px;">Why Choose EzTickEntry?</h2>

  <div>
  <div style = "margin-top: -40px;" class="about" > 
  
    <h2 style= "display: flex; color: #FF8C00; justify-content: space-between; align-items: center; margin-bottom: -150px;" > Buying Of Tickets </h2>

 
  
    <div style="display: flex; justify-content: space-between; align-items: center;">
    <p style="flex: 1; margin-right: 20px; padding-left: 150px; text-align: left;"><strong>Secure your spot effortlessly for your next great adventure </strong> <br> <strong>With Ease from all around the world.<strong></p>
      <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/685520d3d02debcee05ea7c8ca7652dd4f2417d6/EZITIC%20KENTRY.heic" alt="Buy Tickets" style="width: 500px; margin-left: -190px;">
      <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/966e1544270dc9d978ba8394988d4b2907bb43d4/Buy%20ticket%202.heic" alt="Buy Tickets" style="width: 500px; margin-left: -100px; ">
    </div>
  </div>
  </div>
  
  <br>
    
  <div style = "margin-top: 50px;" class="about1" > 
    
  <h2 style= "display: flex; color: #FF8C00; justify-content: space-between; align-items: center; margin-bottom: -50px;" > Organizing Events</h2>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">


    <div class="w3_container" style="flex: 1; margin-right: 20px; padding-left: 150px; text-align: left;">
      <div class="line">
        <div class="word3"><div class="letter3">H</div><div class="letter3">O</div><div class="letter3">S</div><div class="letter3">T</div></div>
        <div class="spacer"></div>
        <div class="word3"><div class="letter3">A</div><div class="letter3">N</div><div class="letter3">D</div></div>
      </div>
      <div class="line">
        <div class="word3"><div class="letter3">M</div><div class="letter3">A</div><div class="letter3">N</div><div class="letter3">A</div><div class="letter3">G</div><div class="letter3">E</div></div>
        <div class="spacer"></div>
        <div class="word3"><div class="letter3">Y</div><div class="letter3">O</div><div class="letter3">U</div><div class="letter3">R</div></div>
      </div>
      <div class="line">
        <div class="word3"><div class="letter3">E</div><div class="letter3">V</div><div class="letter3">E</div><div class="letter3">N</div><div class="letter3">T</div><div class="letter3">S</div></div>
        <div class="spacer"></div>
        <div class="word3"><div class="letter3">W</div><div class="letter3">I</div><div class="letter3">T</div><div class="letter3">H</div></div>
        <div class="spacer"></div>
        <div class="word3"><div class="letter3">E</div><div class="letter3">A</div><div class="letter3">S</div><div class="letter3">E</div></div>
     </div>
    </div>
      <img src="https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/d23fd3d71881b09759c77c0eb655c169375a1e22/EZTICKEN.png" alt="Create Events" style="width: 800px; -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent);
      mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent);"> 

    </div>
  </div>
  
</div>


</div>

<div class="footer">
    <div class="footer-content"> <!-- Wrapper for flexbox -->
        <ul class="social-links">
            <li><a href="https://www.instagram.com/yourusername" target="_blank"><i class="fab fa-instagram"></i> Instagram</a></li>
            <li><a href="https://www.youtube.com/yourchannel" target="_blank"><i class="fab fa-youtube"></i> YouTube</a></li>
            <!-- Add more social links here -->
        </ul>
        <ul class="footer-links">
            <li><a href="/about">About Us</a></li>
            <li><a href="/contact">Contact</a></li>
            <li><a href="/privacy">Privacy Policy</a></li>
            <!-- Add more internal links here -->
        </ul>
    </div>
    <p>© 2024 EzTickEntry. All rights reserved.</p>
</div>

</div>


<style>
/* General Styles */
body, html {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    font-family: 'Arial', sans-serif;
    background: linear-gradient(to top right, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.6)), 
            linear-gradient(to top right, rgba(33, 150, 243, 0.3) 0%, rgba(200, 200, 200, 0.3) 100%);
background: linear-gradient(to top right, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.6)), 
            linear-gradient(to top right, rgba(106, 17, 203, 0.3) 0%, rgba(37, 117, 252, 0.3) 50%, rgba(200, 200, 200, 0.3) 100%);
background: linear-gradient(to top right, rgba(255, 255, 255, 0.7) 0%, rgba(200, 200, 200, 0.3) 100%);
    overflow-x: hidden;
}

.content-wrapper {
    /* This is a general suggestion, adjust based on your actual content */
    min-height: 100vh; /* Minimum height to fill the viewport */
    position: relative; /* Establish a stacking context */
    padding-bottom: 1000px; /* Space for the footer */
}

.container {
  position: relative;
  width: 1000px;
  height: 562px; /* Adjust based on your images' aspect ratio */
  left: 20%; 
  top: -50px;
  
}


.word {
  display: flex;
  justify-content: flex-start; /* Align items to the start */
  gap: 1px; /* Reduced space between letters */
  margin-left: 100px; 
  /* Slight margin to push content to the right */
  z-index: 100000;
}



.word {
    display: flex;
    justify-content: flex-start;
    gap: 2px; /* Adjust the space between letters */
    z-index: 100000;
  left: 100%;
  }
  .letter {
    color: #4B0082; /* Indigo */
    font-size: 2em;
    font-weight: bold;
    text-shadow: 0 0 8px #FFD700, /* Golden glow */
                 0 0 10px #FFD700, 
                 0 0 12px #FFD700, 
                 0 0 14px #FF8C00, /* Slight fiery glow */
                 0 0 16px #FF8C00;
    transform: rotate(-10deg);
    animation: dance 2s infinite alternate;
    z-index: 100000;
  }
  @keyframes dance {
    0% { transform: rotate(-5deg) translateY(0px); }
    50% { transform: rotate(5deg) translateY(-10px); }
    100% { transform: rotate(-5deg) translateY(0px); }
  }

  .word3 {
    display: flex;
    justify-content: flex-start;
    gap: 2px; /* Adjust the space between letters */
    z-index: 100000;
  left: 100%;
  }
  .letter3 {
    color: white; /* Indigo */
    font-size: 2em;
    font-weight: bold;
 
    z-index: 100000;
  }

  .line {
  display: flex;
  }

  .spacer {
    width: 10px; /* Adjust this value to control the gap size */
    display: inline-block; /* This makes sure the spacer is in line with the words */
  }


.modal {
    display: none;
    position: fixed;
    z-index: 1000; /* Higher z-index to be above floating envelopes */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
    z-index: 10001;
}


.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    z-index: 10001;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover, .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Login/Register Button Styles */
.login-register {
    position: fixed;
    top: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
}

.btn {
    border: none;
    padding: 10px 20px;
    background-color: #0000FF;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #e91e63;
}

/* About Section Styles */
.about {
    position: relative;
    bottom: -90px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    background-color: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 10000;
}

.about h2, .about p {
    margin: 10px 0;
}


.about1 {
    position: relative;
    bottom: -90px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    background: linear-gradient(to bottom, rgb(115, 85, 175), rgb(58, 0, 103), rgb(25, 25, 112)); 
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 10000;
}


.about1 h2, .about1 p {
    margin: 10px 0;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .modal-content {
        width: 90%;
    }
}

/* Footer Styles */
.footer {
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);/* Dark background for contrast */
    color: #fff; /* White text color */
    text-align: center; /* Center-align the text */
    padding: 20px; /* Padding for spacing */
    width: 100%; /* Full width */
    position: relative; /* Added to make z-index effective */
    z-index: 10003333; /* High z-index to ensure it's above most elements */
}


.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    /* Ensure the footer is at least at the bottom */
    position: relative;
    height: 60px; /* Set this to the height of your footer */
    bottom: -100;
    clear: both;
}


.social-links, .footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 20px;
}

.social-links li a, .footer-links li a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.social-links li a:hover, .footer-links li a:hover {
    color: #e91e63; /* A bright color for hover states */
}

.footer p {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
    opacity: 0.8;
}



.navbar {
  position: sticky;
  top: 0;
  display: flex;
  align-items: center;
  background-color: #ffffff;
  padding: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  z-index: 1000000;
}

.navbar .logo {
  color: #4B0082;
  font-size: 24px;
  font-weight: bold;
  margin-right: 20px;
  cursor: pointer;
  transition: transform 0.2s;
}

.navbar .logo:hover {
  transform: scale(1.1);
}

.navbar .search-box {
  flex: 1;
  padding: 10px;
  margin: 0 15px;
  border: 2px solid #4B0082;
  border-radius: 2px;
  transition: border-width 0.2s;
}

.navbar .search-box:focus {
  border-width: 3px;
}

.navbar button {
  background-color: #e7e7e7;
  border: none;
  padding: 10px 15px;
  margin-left: 10px;
  cursor: pointer;
  transition: background-color 0.2s, box-shadow 0.2s;
}

.navbar button:hover {
  background-color: #ddd;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.navbar .login {
  background-color: #4B0082;
  color: white;
}

.navbar .locale-switcher {
  display: flex;
}

.navbar .search-area {
  display: flex;
  flex: 1;
  align-items: center;
  margin: 0 15px;
}

.navbar .search-box,
.navbar .date-picker {
  padding: 10px;
  border: 2px solid #4B0082;
  border-radius: 2px;
  margin-right: 5px;
}

.navbar .date-picker {
  cursor: pointer;
}

#search-results {
  position: absolute;
  top: 50px;
  left: calc(50% - 150px);
  width: 300px;
  background: white;
  border: 1px solid #ddd;
  border-radius: 0 0 4px 4px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  display: none;
}

#search-results p {
  padding: 10px;
  margin: 0;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: background-color 0.2s;
}

#search-results p:last-child {
  border-bottom: none;
}

#search-results p:hover {
  background-color: #f9f9f9;
}

.categories-container {
    display: flex;
    overflow-x: auto;
    gap: 20px; /* Increase space between items */
    padding: 20px 0; /* Add padding for top and bottom */
    align-items: center; /* Align items vertically */
    top: 100000px;
  }

  .category {
    border-radius: 10px; /* Larger border-radius */
    overflow: hidden;
    position: relative;
    width: 450px; /* Increase width */
    height: 200px; /* Specify height */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add shadow for depth */
    flex-shrink: 0; /* Prevents resizing */

  }

  .category img {
    width: 100%;
    height: 100%; /* Match height of parent */
    object-fit: cover; /* Ensure image covers the area */
    display: block;
  }

  .category-name {
    position: absolute;
    bottom: 20px; /* Increase spacing from the bottom */
    left: 20px; /* Increase spacing from the left */
    color: white;
    font-size: 24px; /* Increase font size */
    font-weight: bold;
    text-shadow: 2px 2px 6px #000000; /* Enhance text shadow */
  }

  /* The Modal (background) */
.rmodal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content Box */
.rmodal-content {
  position: relative;
  background-color: #fefefe; 
  margin: 10% auto; /* 10% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 50%; /* Adjust based on your preference */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  border-radius: 5px; /* Rounded corners */
  z-index: 20000; /* Increased z-index */
}

  /* The Modal (background) */
  .lmodal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content Box */
.lmodal-content {
  position: relative;
  background-color: #fefefe; 
  margin: 10% auto; /* 10% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 50%; /* Adjust based on your preference */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  border-radius: 5px; /* Rounded corners */
  z-index: 20000; /* Increased z-index */
}


/* The Close Button */
.close {
  color: #aaa;
  position: absolute;
  top: 10px;
  right: 25px;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
}

/* Prevent scrolling when modal is open */
.no-scroll {
  overflow: hidden;
}

.user-name-button {
    background-color: #673ab7;
    color: black;
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

.events-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr); /* Set to 5 columns */
    grid-gap: 20px;
    padding: 160px;
}

.event-card {
    display: flex;
    flex-direction: column;
    border-radius: 10px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.event-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.event-info {
    padding: 15px;
}

.event-date {
    font-size: 0.85rem;
    color: #888;
    margin-bottom: 5px;
}

.event-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.event-location {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 10px;
}

.event-pricing {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.price {
    font-weight: 700;
    color: #4CAF50;
}

.start-time {
    font-size: 0.9rem;
    color: #555;
}

.event-card-footer {
    margin-top: auto;
    background-color: #673AB7;
    padding: 10px;
    text-align: center;
}

.view-tickets-btn {
    background-color: transparent;
    border: 1px solid #fff;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.2s, color 0.2s;
}

.view-tickets-btn:hover {
    background-color: #fff;
    color: #673AB7;
}


/* Adjustments for responsiveness */
@media (max-width: 1200px) {
    .events-grid {
        grid-template-columns: repeat(4, 1fr); /* 4 columns for smaller screens */
    }
}

@media (max-width: 992px) {
    .events-grid {
        grid-template-columns: repeat(3, 1fr); /* 3 columns for tablets */
    }
}

@media (max-width: 768px) {
    .events-grid {
        grid-template-columns: repeat(2, 1fr); /* 2 columns for very small tablets and large phones */
    }
}

@media (max-width: 576px) {
    .events-grid {
        grid-template-columns: 1fr; /* 1 column for small phones */
    }
}

/* Style for the search results dropdown */
.search-results-dropdown {
    display: none; /* Hidden by default */
    position: absolute;
    top: 100%; /* Position it below the search box */
    left: 0;
    z-index: 1000;
    background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background */
    border: 1px solid #ccc; /* Optional border */
    width: 100%; /* Full width */
    max-height: 400px; /* Adjustable max height for scrolling */
    overflow-y: auto; /* Enable vertical scrolling */
    box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Subtle shadow for depth */
    border-radius: 4px; /* Optional border radius for styled corners */
}

.mini-event-card {
    display: flex;
    align-items: center;
    padding: 10px; /* Spacing inside each card */
    border-bottom: 1px solid #eee; /* Separator between cards */
    background-color: #fff; /* Solid background for content */
}

.mini-event-card:last-child {
    border-bottom: none; /* Remove bottom border for the last card */
}

.mini-event-card img {
    width: 60px; /* Adjusted image size */
    height: 60px; /* Adjusted image size */
    object-fit: cover; /* Ensure the image covers the area */
    border-radius: 30px; /* Make image round */
    margin-right: 10px; /* Space between image and text */
}

.mini-event-card .event-info {
    display: flex;
    flex-direction: column;
}

.mini-event-card .event-title {
    font-size: 16px; /* Larger font size for readability */
    font-weight: bold; /* Bold title */
}

.mini-event-card .event-date {
    font-size: 12px; /* Smaller font size for the date */
    color: #666; /* Subdued color for the date */
}


</style>

<script>
function searchEvent() {
  var input, filter, events, title, i;
  var dropdown = document.querySelector('.search-results-dropdown');
  dropdown.innerHTML = ''; // Clear previous results
  input = document.getElementById('search-box');
  filter = input.value.toUpperCase();
  events = document.getElementsByClassName('event-card');
  var found = false;

  for (i = 0; i < events.length; i++) {
    title = events[i].getAttribute('data-event-title').toUpperCase();
    if (title.indexOf(filter) > -1) {
      var miniCard = events[i].cloneNode(true);
      miniCard.classList.add('mini-event-card');
      miniCard.classList.remove('event-card'); // Remove original event-card class to avoid style conflicts
      dropdown.appendChild(miniCard);
      found = true;
    }
  }

  if (found) {
    dropdown.style.display = 'block';
  } else {
    dropdown.style.display = 'none';
  }
}

// Close the dropdown when clicking outside
document.addEventListener('click', function(event) {
  var dropdown = document.querySelector('.search-results-dropdown');
  var searchBox = document.getElementById('search-box');
  // Close the dropdown if the click is outside of the dropdown and the search box
  if (!dropdown.contains(event.target) && !searchBox.contains(event.target)) {
    dropdown.style.display = 'none';
  }
});


// Adding event listener for the search box
document.getElementById('search-box').addEventListener('input', searchEvent);
</script>




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

</body>
</html>


