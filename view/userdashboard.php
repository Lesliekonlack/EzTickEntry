
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
        <a href="#" class="active">Dashboard</a>
        <a href="loginentrypage.php">Homepage</a>
        <!-- Categories Dropdown -->
        <div class="categories-dropdown">
    <button class="categories-button">Host An Event <i class="fa fa-chevron-down"></i></button>
      <div class="dropdown-content2">
          <?php foreach ($eventCategories as $categoryId => $categoryName): ?>
              <?php if($categoryName == "Live Performances/Festivals"): ?>
                  <a href="hostliveperformanceevent.php?category=<?php echo urlencode($categoryId); ?>"><?php echo htmlspecialchars($categoryName); ?></a>
              <?php elseif($categoryName == "Cinema/Theatre"): ?> 
                  <a href="hostcinemaevent.php?category=<?php echo urlencode($categoryId); ?>"><?php echo htmlspecialchars($categoryName); ?></a>
              <?php else: ?>
                 
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
      <a href="upcomingevents.php">Upcoming</a>
      <!-- ... other links ... -->
      <a href="logout.php">Log Out</a>
    </div>

  </div>

</div>

<div class="dashboard-content">
  <h1>Welcome to your Dashboard</h1>
    <div class="dashboard-cards">
    <a href="upcomingevents.php" class="card-link">
        <div class="card">
            <i class="fas fa-calendar-alt"></i>
            <p>Upcoming Events</p>
        </div>
        <a href="myTicketPurchases.php" class="card-link">
        <div class="card">
            <i class="fas fa-ticket-alt"></i>
            <p>My Tickets Purchases</p>
        </div>
        <!-- Making the My Events card clickable -->
        <a href="eventsmanagements.php" class="card-link">
            <div class="card">
                <i class="fas fa-user-cog"></i>
                <p>Managing My Events Tickets </p>
            </div>
        </a>
        <a href="pasteventsphp" class="card-link">
            <div class="card">
                <i class="fas fa-user-cog"></i>
                <p>My Past Events</p>
            </div>
        </a>
     
     
        
    </div>
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