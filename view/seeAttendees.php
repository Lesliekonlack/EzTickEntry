<?php
session_start();
include '../settings/connection.php'; // Adjust the path as needed for your connection settings

$eventId = $_GET['event_id'] ?? ''; // Get the event ID from the URL parameter with null coalesce for default

if (empty($eventId)) {
    die("Event ID is required.");
}

// Fetch the event title
$eventTitleQuery = "SELECT Title FROM Events WHERE EventID = ?";
$titleStmt = $connection->prepare($eventTitleQuery);
$titleStmt->bind_param("i", $eventId);
$titleStmt->execute();
$titleResult = $titleStmt->get_result();
$eventTitle = $titleResult->fetch_assoc()['Title'] ?? 'Unknown Event'; // Default to 'Unknown Event' if not found
$titleStmt->close();

$attendees = [];

$sql = "SELECT 
            TT.Name as TierName,
            U.FirstName,
            U.LastName,
            U.Email,
            P.PaymentDate,
            P.PaymentID
        FROM 
            Users U
        INNER JOIN 
            Bookings B ON U.UserID = B.UserID
        INNER JOIN 
            Tickets T ON B.BookingID = T.BookingID
        INNER JOIN 
            TicketTiers TT ON T.TierID = TT.TierID
        INNER JOIN 
            Payments P ON T.BookingID = P.BookingID
        WHERE 
            B.EventID = ? AND 
            T.TicketStatusID = 1 AND 
            B.BookingStatusID = 4
        ORDER BY 
            TT.Name, U.LastName, U.FirstName";


$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

$groupedAttendees = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $groupedAttendees[$row['TierName']][] = $row;
    }
} else {
    die("Error retrieving attendees list: " . $connection->error);
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Attendees - EzTickEntry</title>
    <!-- Add any required styles or scripts here -->
</head>
<body>

<div class="navbar">
    <div class="logo" style="color: #673ab7;">EzTickEntry</div> <!-- The style attribute seems to be missing the 'color' property -->
    <div class="nav-items">
        <a href="#" class="active">See Attendees</a>
        <a href="eventsmanagements.php" > Managing My Events</a>

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

<h1>Event "<?php echo htmlspecialchars($eventTitle); ?>" Attendees</h1>
<input type="text" id="searchInput" class="search-input" onkeyup="searchEvent()" placeholder="Search attendees by name or email">

<div class="card-grid">
    <!-- Tier cards to act as tabs -->
    <?php foreach ($groupedAttendees as $tierName => $attendees): ?>
        <div class="tier-card" onclick="toggleAttendeeList('<?php echo htmlspecialchars($tierName); ?>')">
            <h2><?php echo htmlspecialchars($tierName); ?></h2>
        </div>
    <?php endforeach; ?>
</div>

<!-- Attendee tables -->
<?php foreach ($groupedAttendees as $tierName => $attendees): ?>
    <div id="list-<?php echo htmlspecialchars($tierName); ?>" class="attendee-list" style="display:none;">
        <h3><?php echo htmlspecialchars($tierName); ?> Attendees</h3>
        <table class="attendee-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Payment Date</th>
                    <th>Seat ID</th>
                    <th>Check-In</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendees as $attendee): ?>
                    <tr>
                        <?php
                        // Abbreviate tier name. You might need a more sophisticated abbreviation mechanism for complex tier names.
                        $tierAbbreviation = strtoupper(substr($attendee['TierName'], 0, 3)); 
                        // Create the Seat ID
                        $seatId = $tierAbbreviation ."-". str_pad($attendee['PaymentID'], 3, '0', STR_PAD_LEFT);
                        
                        ?>
                        <td><?php echo htmlspecialchars($attendee['FirstName'] . " " . $attendee['LastName']); ?></td>
                        <td><?php echo htmlspecialchars($attendee['Email']); ?></td>
                        <td><?php echo htmlspecialchars($attendee['PaymentDate']); ?></td>
                        <td><?php echo htmlspecialchars($seatId); ?></td> <!-- Display Seat ID -->
                        <td>
                        <button class="check-in-btn" onclick="checkInAttendee(this.getAttribute('data-booking-id'))" data-booking-id="<?php echo $attendee['BookingID']; ?>">Check In</button>



                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endforeach; ?>





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

.attendee-list h3 {
            background-color: #673ab7; /* Deep purple theme */
            color: #ffffff;
            padding: 10px;
            margin: 0;
            border-radius: 5px 5px 0 0; /* Rounded corners on top */
        }
        
        .attendee-list li {
            background: #fff;
            padding: 10px;
            border-bottom: 1px solid #ddd; /* Separator between attendees */
        }
        
        .attendee-list li:last-child {
            border-radius: 0 0 5px 5px; /* Rounded corners on the last element */
        }
        
        /* Card Grid */
        .card-grid {
    display: flex;
    flex-wrap: wrap;
   /*margin-left:-950px; */
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem; /* Space between tier cards and tables */
}

.tier-card {
    cursor: pointer;
    padding: 1rem;
    border-radius: 8px;
    background-color: #4B0082;
    color: #fff;
    text-align: center;
    transition: background-color 0.3s;
    font-size: 1.2rem; /* Larger font size for readability */
    width: 200px; /* Fixed width for uniformity */
}

.tier-card:hover {
    background-color: #372c5f;
}

.attendee-table {
    width: 100%; /* Ensure the table takes full width */
    border-collapse: collapse;
    margin-bottom: 2rem; /* Space between tables */
}

.attendee-table th, .attendee-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.attendee-table th {
    background-color: #673AB7;
    color: white;
}

.attendee-table tr:nth-child(even) {
    background-color: #f2f2f2;
}

.attendee-table tr:hover {
    background-color: #ddd;
}

.attendee-table button {
    padding: 5px 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.attendee-table button:hover {
    background-color: #45a049;
}
.accordion-icon {
    transition: transform 0.3s ease-in-out;
}
.rotate-icon {
    transform: rotate(180deg);
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
function toggleAttendeeList(tierName) {
    var listId = 'list-' + tierName;
    var list = document.getElementById(listId);
    if (list.style.display === "none" || list.style.display === "") {
        list.style.display = "block";
    } else {
        list.style.display = "none";
    }
}

    </script>

<script>
function searchEvent() {
    var input, filter, attendeesLists, attendees, i, j;
    input = document.getElementById('searchInput');
    filter = input.value.toUpperCase();
    attendeesLists = document.getElementsByClassName('attendee-list');

    // Loop through all lists and list items to search for attendee information
    for (i = 0; i < attendeesLists.length; i++) {
        if (attendeesLists[i].style.display === 'block') { // Only search in the visible list
            attendees = attendeesLists[i].getElementsByTagName('li');
            for (j = 0; j < attendees.length; j++) {
                if (attendees[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                    attendees[j].style.display = "";
                } else {
                    attendees[j].style.display = "none";
                }
            }
        }
    }
}

</script>


<script>
function checkInAttendee(bookingId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../action/updateBookingStatus.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (this.status == 200) {
            alert('Check-In Successful!');
            // Optionally, update the button state here to indicate successful check-in
        } else {
            alert('Error during Check-In.');
        }
    };
    xhr.send('bookingId=' + bookingId);
}


</script>

</body>
</html>
