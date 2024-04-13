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
    <title>User Dashboard - EzTickEntry</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6vP0k0lVj8BcMxFpwW3KwPsAwVoiuBxHnxD4GKIObL5XsFL+2N8H5lyJvGoeK2o" crossorigin="anonymous">
</head>
<body>

<div class="navbar">
    <div class="logo" style="color: #673ab7;">EzTickEntry</div> <!-- The style attribute seems to be missing the 'color' property -->
    <div class="nav-items">
        <a href="upcomingevents.php" class="active">UpComing Events</a>
        <a href="userdashboard.php">Dashboard</a>

        <!-- Categories Dropdown -->
        <div class="categories-dropdown">
    <button class="categories-button">Host An Event <i class="fa fa-chevron-down"></i></button>
      <div class="dropdown-content2">
          <?php foreach ($eventCategories as $categoryId => $categoryName): ?>
              <?php if($categoryName == "Live Performances/Festivals"): ?>
                  <a href="hostliveperformanceevent.php?category=<?php echo urlencode($categoryId); ?>"><?php echo htmlspecialchars($categoryName); ?></a>
              <?php elseif($categoryName == "Cinema/Theatre"): ?>
                  <a href="hostcinemaevent.php?category=<?php echo urlencode($categoryId); ?>"><?php echo htmlspecialchars($categoryName); ?></a>
              <?php elseif($categoryName == "Private Events"): ?>
                  <a href="hostprivateevent.php?category=<?php echo urlencode($categoryId); ?>"><?php echo htmlspecialchars($categoryName); ?></a>
              <?php else: ?>
                  <a href="#"><?php echo htmlspecialchars($categoryName); ?></a>
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
      <a href="logout.php">Log Out</a>
    </div>

  </div>
</div>
<input type="text" id="searchInput" class="search-input" onkeyup="searchEvent()" placeholder="Search by event name">
<button class="button2 category-button" onclick="toggleCategory('Cinema/Theatre')">View Cinema/Theatre Events</button>
<button class="category-button" onclick="toggleCategory('Live Performances/Festivals')">View Live Performances/Festivals Events</button>

<button class="view-all-button" onclick="toggleAll()">View All Events</button> <!-- Add the "View All" button -->


<div class="events-grid">
    <?php foreach ($myEvents as $event): ?>
        <div class="event-card" data-event-title="<?php echo htmlspecialchars($event['Title']); ?>" data-category="<?php echo htmlspecialchars($event['Category']['CategoryName']); ?>">
        <p class="category" style="display: none;"><?php echo $event['CategoryName']; ?></p>
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
                        <span class="price"> $<?php echo htmlspecialchars(number_format($minPrice, 2)); ?>
                            - $<?php echo htmlspecialchars(number_format($maxPrice, 2)); ?></span>
                    <?php } ?>
                <?php endif; ?>
                    <span class="start-time"><?php echo date('g:i A', strtotime($event['StartTime'])); ?> MDT</span>
                </div>
            </div>
            <div class="event-card-footer">
                <!-- This goes in your main event listing page inside the loop -->
            <button class="btn view-tickets-btn" onclick="window.location.href='event-detail.php?eventId=<?php echo $event['EventID']; ?>'">View Tickets</button>

            </div>
        </div>
    <?php endforeach; ?>



    
</div>
<footer class="footer">
    <!-- Footer content similar to the entry page -->
</footer>

<style>
/* Base styles */
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


/* Adjustments for the modal actions */
.event-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr); /* Set to 5 columns */
    grid-gap: 20px;
    padding: 40px;
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

input[type=text] {
        padding: 10px;
        margin: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

button {
        background-color: #673ab7; /* Green */
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 5px;
    }

    /* Change the background color of the button when hovered */
    button:hover {
        background-color: #45a049;
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
    var input, filter, events, eventCards, title, i;
    input = document.getElementById('searchInput');
    filter = input.value.toUpperCase();
    events = document.getElementsByClassName('event-card');
  
    for (i = 0; i < events.length; i++) {
        title = events[i].getAttribute('data-event-title').toUpperCase();
        if (title.indexOf(filter) > -1) {
            events[i].style.display = "";
        } else {
            events[i].style.display = "none";
        }
    }
}

function toggleCategory(categoryName) {
    var eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach(function(card) {
        var category = card.dataset.category;
        if (category === categoryName || categoryName === 'All') {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}


function toggleAll() {
    var i, events;
    events = document.getElementsByClassName('event-card');
  
    for (i = 0; i < events.length; i++) {
        events[i].style.display = "";
    }
}
</script>


</body>
</html>
