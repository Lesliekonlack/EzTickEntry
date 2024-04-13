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

// SQL query to fetch event details along with associated ticket tiers and category
$sql = "SELECT e.EventID, e.Title, e.Description, e.StartTime, e.EndTime, 
v.Name AS VenueName, v.Location AS VenueLocation, v.Capacity AS VenueCapacity, 
v.ContactInfo AS VenueContact, c.CountryName, i.ImagePath,
t.TierID, t.Name AS TierName, t.Price, t.QuantityAvailable, t.IsActive,
ec.CategoryName
FROM Events e
JOIN Venues v ON e.VenueID = v.VenueID
LEFT JOIN Countries c ON v.CountryID = c.CountryID
LEFT JOIN Images i ON e.EventID = i.EventID
LEFT JOIN TicketTiers t ON e.EventID = t.EventID
LEFT JOIN EventCategories ec ON e.CategoryID = ec.CategoryID
WHERE e.OrganizerID = ? AND e.EventStatusID = 1 AND e.EndTime < NOW()
ORDER BY e.EventID, t.TierID ASC";



$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $eventId = $row['EventID'];

        // If this is the first time we're seeing this event, initialize its array
        if (!isset($myEvents[$eventId])) {
            $myEvents[$eventId] = [
                'EventID' => $row['EventID'],
                'Title' => $row['Title'],
                'Description' => $row['Description'],
                'StartTime' => $row['StartTime'],
                'EndTime' => $row['EndTime'],
                'VenueName' => $row['VenueName'],
                'VenueLocation' => $row['VenueLocation'],
                'VenueCapacity' => $row['VenueCapacity'],
                'VenueContact' => $row['VenueContact'],
                'CountryName' => $row['CountryName'],
                'ImagePath' => $row['ImagePath'],
                'CategoryName' => $row['CategoryName'], // Add category name to event details
                'TicketTiers' => [] // Initialize an empty array for ticket tiers
            ];
        }

        // Only add the ticket tier if it exists for this row
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
        <a href="#" class="active">My Past Events</a>
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
<div class="dashboard-content">
    <h1>My Past Events</h1>
    <!-- Add the button to toggle categories -->
    <!-- Add the search bar and toggle buttons -->
    <input type="text" id="searchInput" class="search-input" onkeyup="searchEvent()" placeholder="Search by event name">

    <button class="button2 category-button" onclick="toggleCategory('Cinema/Theatre')">View Cinema/Theatre Events</button>
    <button class="category-button" onclick="toggleCategory('Live Performances/Festivals')">View Live Performances/Festivals Events</button>
    <button class="view-all-button" onclick="toggleAll()">View All Events</button> <!-- Add the "View All" button -->


    <div class="dashboard-cards">
        <!-- Loop through each event managed by the user -->
        <?php foreach ($myEvents as $event): ?>
            <div class="event-card" data-event-title="<?php echo htmlspecialchars($event['Title']); ?>">
                <div class="card-image">
                    <img src="<?php echo $event['ImagePath']; ?>" alt="Event Image">
                </div>
                <div class="card-content">
                    <h3 class=event-title ><?php echo htmlspecialchars($event['Title']); ?></h3>
                    <p class="category" style="display: none;"><?php echo $event['CategoryName']; ?></p>
                    <p><?php echo htmlspecialchars($event['Description']); ?></p>
                    <p><strong>Start Time:</strong> <?php echo htmlspecialchars($event['StartTime']); ?></p>
                    <p><strong>End Time:</strong> <?php echo htmlspecialchars($event['EndTime']); ?></p>
                    <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['VenueName']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($event['VenueLocation']); ?>, <?php echo htmlspecialchars($event['CountryName']); ?></p>
                    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($event['VenueCapacity']); ?> seats</p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($event['VenueContact']); ?></p>
                    <p><strong>Category:</strong> <?php echo $event['CategoryName']; ?></p>
                    <!-- Buttons for Edit, Delete, and Manage Tickets & Seats -->
                    <div class="event-actions">

                        <!-- Delete button -->
                        <a href="../action/deleteeventaction.php?event_id=<?php echo $event['EventID']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                        <!-- Manage Tickets & Seats button -->
                   

                       <a href="whoattended.php?event_id=<?php echo $event['EventID']; ?>" class="btn view-attendees-btn2">See Who Attended</a>


                       
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<!-- The tickets Modal -->
<div id="ticketsModal" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2>Manage Ticket Tiers for Event: <span id="modalEventTitle"></span></h2>
    
    <form id="ticketTiersForm" action="../action/manageTicketTierAction.php" method="post">
      <input type="hidden" id="modalEventId" name="event_id">
      <div id="ticketTiersContainer">
        <!-- Existing ticket tiers will be dynamically added here via JavaScript -->
      </div>
      <button type="button" id="addTierButton" onclick="addTicketTier()">Add Ticket Tier</button>
      <button type="submit">Save Changes</button>
    </form>
  </div>
</div>


<!-- Edit Event Modal -->
<div id="editEventModal" class="modal" style="display: none; background-color: rgba(0,0,0,0.4);">
  <!-- Modal content -->
  <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: -500px; border: 1px solid #888; width: 35%; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); display: flex; justify-content: center; align-items: flex-start; flex-wrap: wrap;">
    
    <!-- Current Event Image Section -->
    <div style="flex: 1; margin-right: 20px; max-width: 500px;">
      <!-- Current Event Image -->
      <div class="event-image-preview" style="text-align: center; margin-bottom: 20px;">
          <label for="editEventImage">Current Event Image:</label>
          <img id="currentEventImage" src="" alt="Event Image" style="max-width: 100%; height: auto; max-height: 300px;">
          <input type="hidden" id="currentImagePath" name="current_image_path">
          

      </div>
      <div style="text-align: center;">
        <label for="editEventImage">Change Event Image:</label><br>
        <input type="file" id="editEventImage" name="event_image" accept="image/*">
      </div>
    </div>

    <!-- Form Section -->
    <div style="flex: 3; flex-basis: 60%; min-width: 320px;">
      <span class="close" onclick="closeEditModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
      <h2>Edit Event: <span id="editEventTitleDisplay"></span></h2>
      
      <!-- Edit Event Form -->
      <form id="editEventForm" action="../action/editEventAction.php" method="post" style="width: 100%;">
        <input type="hidden" id="editEventId" name="event_id">
        
        <label for="editEventTitle">Title:</label>
        <input type="text" id="editEventTitle" name="title" required style="width: 100%; padding: 10px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        
        <label for="editEventDescription">Description:</label>
        <textarea id="editEventDescription" name="description" required style="width: 100%; padding: 10px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"></textarea>
        
        <label for="editEventStartTime">Start Time:</label>
        <input type="datetime-local" id="editEventStartTime" name="start_time" required style="width: 100%; padding: 10px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        
        <label for="editEventEndTime">End Time:</label>
        <input type="datetime-local" id="editEventEndTime" name="end_time" required style="width: 100%; padding: 10px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        
        <label for="editVenueName">Venue Name:</label>
        <input type="text" id="editVenueName" name="venue_name" required style="width: 100%; padding: 10px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        
        <label for="editVenueLocation">Location:</label>
        <input type="text" id="editVenueLocation" name="venue_location" required style="width: 100%; padding: 10px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        
        <label for="editVenueCapacity">Capacity:</label>
        <input type="number" id="editVenueCapacity" name="venue_capacity" required style="width: 100%; padding: 10px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        
        <label for="editVenueContact">Contact:</label>
        <input type="text" id="editVenueContact" name="venue_contact" required style="width: 100%; padding: 10px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">

        <button type="submit" style="background-color: #4CAF50; color: white; padding: 14px 20px; margin: 8px 0; border: none; cursor: pointer; width: 100%; border-radius: 4px;">Save Changes</button>
      </form>
    </div>

  </div>
</div>


<script>
function openEditModal(eventId) {
  var modal = document.getElementById("editEventModal");
  modal.style.display = "block";
  document.getElementById("editEventId").value = eventId;
  
  // Fetch event details and populate form fields
  fetchEventDetails(eventId);
}

function closeEditModal() {
  var modal = document.getElementById("editEventModal");
  modal.style.display = "none";
}

// Dummy function to demonstrate fetching event details
// Replace this with your actual AJAX call
function fetchEventDetails(eventId) {


  // Populate form fields with the event data
  document.getElementById("editEventTitle").value = eventData.title;
  document.getElementById("editEventDescription").value = eventData.description;
  document.getElementById("editEventStartTime").value = eventData.start_time;
  document.getElementById("editEventEndTime").value = eventData.end_time;
  document.getElementById("editVenueName").value = eventData.venue_name;
  document.getElementById("editVenueLocation").value = eventData.venue_location;
  document.getElementById("editVenueCapacity").value = eventData.venue_capacity;
  document.getElementById("editVenueContact").value = eventData.venue_contact;
  document.getElementById("editEventTitleDisplay").textContent = eventData.title; // Display event title in modal header
}

// Ensure the modal closes when clicking outside of it
window.onclick = function(event) {
  var modal = document.getElementById("editEventModal");
  if (event.target == modal) {
    closeEditModal();
  }
}
</script>



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

.dashboard-content {
    padding: 20px;
}

.event-card {
    display: flex;
    background:#fff;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-image img {
    width: 600px;
    height: auto;
    border-radius: 8px 0 0 8px;
}

.card-content {
    padding: 20px;
    flex-grow: 1;
    position: relative;
    color: black;
}

.event-actions {
    position: absolute;
    bottom: 20px;
    right: 20px;
}

.btn {
    padding: 10px 20px;
    margin-left: 10px;
    text-decoration: none;
    color: #fff;
    border-radius: 5px;
    font-size: 14px;
}

.edit-btn {
    background-color: #4CAF50;
}

.delete-btn {
    background-color: #f44336;
}

.manage-tickets-btn {
    background-color: #008CBA;
}
/* Dashboard Content */
.dashboard-content {
    padding: 20px;
}

.dashboard-cards {
    display: grid;
    flex-direction: column; repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
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

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.modal-content {
  background-color: #fefefe;
  margin: 15% auto; /* 15% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 50%; /* Could be more or less, depending on screen size */
  position: relative;
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

/* Additional styles for form and buttons */
#ticketTiersForm {
  display: flex;
  flex-direction: column;
}

.ticket-tier {
  display: flex;
  margin-bottom: 10px;
}

.ticket-tier input {
  margin-right: 10px;
}

.btn {
  cursor: pointer;
  padding: 10px;
  margin-top: 10px;
}



.delete-btn {
  background-color: #f44336; /* Red */
  color: white;
}

/* Adjustments for the modal actions */
.event-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

/* Style for the search input */
.search-input {
    padding: 10px;
    margin: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

/* Style for the category buttons */
.category-button {
    background-color: #673ab7; /* Purple */
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

/* Change the background color of the category button when hovered */
.category-button:hover {
    background-color: #45a049; /* Darker purple */
}

/* Style for the "View All" button */
.view-all-button {
    background-color: #673ab7; /* Purple */
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

/* Change the background color of the "View All" button when hovered */
.view-all-button:hover {
    background-color: #45a049; /* Darker purple */
}

.event-image-preview {
    text-align: center;
    margin-bottom: 20px;
}

.event-image-preview img {
    max-width: 10%;
    height: auto;
    border-radius: 4px; /* Optional: for rounded corners */
}

.edit-content {
    display: flex;
    justify-content: space-between; /* Space out the children */
    align-items: flex-start; /* Align items at the top */
}

.event-image-preview {
    flex: 1; /* Take up 1/3 of the flex container */
    padding-right: 20px; /* Add some space between the image and the form */
}

#editEventForm {
    flex: 2; /* Take up 2/3 of the flex container */
}

.view-attendees-btn {
    background-color: #32CD32; /* A green color for the 'See Attendees' button */
    color: white;
    padding: 8px 15px;
    margin-top: 10px; /* Provide some space above the button */
    border: none;
    border-radius: 5px;
    text-decoration: none; /* Remove underline from link */
    cursor: pointer;
    display: inline-block; /* Allows padding and margins to affect the link */
    transition: background-color 0.3s ease;
}

.view-attendees-btn:hover {
    background-color: #28a745; /* A darker green on hover */
}


/* Style for the 'See Who's Attending' button */
.view-attendees-btn2 {
    background-color: #007BFF; /* Vivid blue background */
    color: white; /* White text */
    padding: 10px 20px; /* Padding around the text */
    text-decoration: none; /* No underline */
    border-radius: 5px; /* Rounded corners */
    border: none; /* No border */
    font-weight: bold; /* Bold font */
    transition: all 0.3s ease; /* Smooth transition for hover effects */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for 3D effect */
}

/* Hover effect for the button */
.view-attendees-btn2:hover {
    background-color: #0056b3; /* Darker shade of blue on hover */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Larger shadow on hover */
    transform: translateY(-2px); /* Slight raise on hover */
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
function openModal(eventId, eventTitle, ticketTiers) {
    var modal = document.getElementById("ticketsModal");
    modal.style.display = "block";

    document.getElementById("modalEventId").value = eventId;
    document.getElementById("modalEventTitle").textContent = eventTitle;

    // Clear existing ticket tiers
    var container = document.getElementById("ticketTiersContainer");
    container.innerHTML = '';

    // Populate with the provided ticket tiers
    ticketTiers.forEach(function(tier) {
        addTicketTier(tier);
    });
}

function addTicketTier(tier = { isNew: true }) {
    var container = document.getElementById("ticketTiersContainer");
    var currentTierCount = container.childElementCount;

    // Check if adding another tier would exceed the total limit of 20
    if (currentTierCount >= 20) {
        alert("Maximum of 20 ticket tiers allowed in total (active and archived).");
        return;
    }

    var div = document.createElement('div');
    div.className = 'ticket-tier';
    
    // If the tier is new (not pre-populated from the database), add 'new-tier' class
    if (tier.isNew) {
        div.classList.add('new-tier');
    }

    // Generate the inner HTML for the ticket tier
    // Note: the 'Remove' button is only added for new ticket tiers
    div.innerHTML = `
        <input type="hidden" name="tierId[]" value="${tier.TierID || ''}">
        <input type="text" name="tierName[]" value="${tier.TierName || ''}" placeholder="Tier Name" required>
        <input type="number" name="tierPrice[]" value="${tier.Price || ''}" placeholder="Price" min="0" step="0.01" required>
        <input type="number" name="tierQuantity[]" value="${tier.QuantityAvailable || ''}" placeholder="Quantity" min="1" step="1" required>
        <select name="tierIsActive[]">
            <option value="1" ${tier.IsActive ? 'selected' : ''}>Active</option>
            <option value="0" ${!tier.IsActive ? 'selected' : ''}>Archived</option>
        </select>
        ${tier.isNew ? '<button type="button" onclick="removeTicketTier(this)">Remove</button>' : ''}
    `;

    container.appendChild(div);
}


// Function to remove a ticket tier
function removeTicketTier(button) {
    button.closest(".ticket-tier").remove();
}


function closeModal() {
  var modal = document.getElementById("ticketsModal");
  modal.style.display = "none";
}

// Add more functions as needed, such as for dynamically loading ticket tiers for a selected event

</script>


<script>
ffunction openEditModal(eventId, eventDetails) {
  var modal = document.getElementById("editEventModal");
  modal.style.display = "block";

  // Populate form with event details
  document.getElementById("editModalEventId").value = eventId;
  document.getElementById("editModalEventTitle").textContent = eventDetails.title;
  document.getElementById("editEventTitle").value = eventDetails.title;
  document.getElementById("editEventDescription").value = eventDetails.description;
  document.getElementById("editEventStartTime").value = eventDetails.startTime;
  document.getElementById("editEventEndTime").value = eventDetails.endTime;
  document.getElementById("editVenueName").value = eventDetails.venueName;
  document.getElementById("editVenueLocation").value = eventDetails.venueLocation;
  document.getElementById("editVenueCapacity").value = eventDetails.venueCapacity;
  document.getElementById("editVenueContact").value = eventDetails.venueContact;

  // Add more fields as necessary
}

function closeEditModal() {
  var modal = document.getElementById("editEventModal");
  modal.style.display = "none";
}

// Ensure the modal closes when clicking outside of it
window.onclick = function(event) {
  var modal = document.getElementById("editEventModal");
  if (event.target == modal) {
    closeEditModal();
  }
}

// Dummy function to demonstrate fetching event details
// Replace this with your actual AJAX call
function fetchEventDetails(eventId) {
  // This is just a dummy function, replace with actual AJAX call to fetch event details from the server
  var eventDetails = {
    title: "Event Title",
    description: "Event Description",
    startTime: "Event Start Time",
    endTime: "Event End Time",
    venueName: "Event Venue Name",
    venueLocation: "Event Venue Location",
    venueCapacity: "Event Venue Capacity",
    venueContact: "Event Venue Contact"
    // Add more details as necessary
  };

  openEditModal(eventId, eventDetails);
}

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
    var i, category, events;
    category = categoryName.toUpperCase();
    events = document.getElementsByClassName('event-card');
  
    for (i = 0; i < events.length; i++) {
        var eventCategory = events[i].getElementsByClassName('category')[0].textContent.toUpperCase();
        if (eventCategory.indexOf(category) > -1) {
            events[i].style.display = "";
        } else {
            events[i].style.display = "none";
        }
    }
}

function toggleAll() {
    var i, events;
    events = document.getElementsByClassName('event-card');
  
    for (i = 0; i < events.length; i++) {
        events[i].style.display = "";
    }
}
</script>


<script>
function openEditModal(eventId, title, description, startTime, endTime, venueName, venueLocation, venueCapacity, venueContact, ImagePath) {
  var modal = document.getElementById("editEventModal");
  modal.style.display = "block";

  // Set the form field values
  document.getElementById("editEventId").value = eventId;
  document.getElementById("editEventTitle").value = title;
  document.getElementById("editEventDescription").value = description;
  document.getElementById("editEventStartTime").value = startTime;
  document.getElementById("editEventEndTime").value = endTime;
  document.getElementById("editVenueName").value = venueName;
  document.getElementById("editVenueLocation").value = venueLocation;
  document.getElementById("editVenueCapacity").value = venueCapacity;
  document.getElementById("editVenueContact").value = venueContact;
  document.getElementById("currentEventImage").src = ImagePath;
  document.getElementById('currentImagePath').value = imagePath;


  // Set the modal title to the event title
  document.getElementById("editEventTitleDisplay").textContent = title;
}
</script>


</body>
</html>
