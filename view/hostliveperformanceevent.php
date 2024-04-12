<?php
session_start(); // Ensure session_start() is only called once at the top

include '../settings/connection.php'; // Include your DB connection
include '../settings/core.php';
checkLogin();

// Fetch categories from the database and store them in an array
$eventCategories = [];
$sql = "SELECT CategoryID, CategoryName FROM EventCategories";
$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $eventCategories[$row['CategoryID']] = $row['CategoryName'];
    }
}

$countries = [];
$sql = "SELECT CountryID, CountryName FROM Countries ORDER BY CountryName";
$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $countries[$row['CountryID']] = $row['CountryName'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host an Event - EzTickEntry</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6vP0k0lVj8BcMxFpwW3KwPsAwVoiuBxHnxD4GKIObL5XsFL+2N8H5lyJvGoeK2o" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            position: sticky;
            top: 0;
            background-color: white; /* Deep purple theme */
            color: black;
            padding: 10px ;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000000;
        }


        .navbar .nav-items a {
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

        .user-dropdown {
            position: relative;
            display: inline-block;
            
        }

        .user-name-button {
            background-color: #4B0082; 
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

        .form-container {
            display: flex; /* Enables flexbox layout */
            align-items: stretch; /* Stretches items to fill the container vertically */
            min-height: 890px; /* Minimum height of the container */
        }

        .form-side {
            flex: 1; /* Takes up 50% of the space */
            background-image: url('https://rawcdn.githack.com/naomikonlack/WEBTECHGITDEMO/dccb68e43ece051e4b01ad41b121c945e049a524/rock-on-rear-view-of-a-music-fan-dancing-with-her-2023-01-04-20-11-49-utc.jpg');
            background-size: cover; /* Covers the entire div */
            background-position: center; /* Centers the background image */
            width: 68.5%; /* Set width to 68.5% */
            z-index: -1000; /* Set z-index to -1000 */
            -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent); /* Apply webkit mask image */
            mask-image: linear-gradient(to right, transparent, black 10%, black 100%, transparent); /* Apply mask image */
        }

        form {
            flex: 1; /* Also takes up 50% of the space */
            background: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centers form contents vertically */
        }

                

        form label {
            display: block;
            margin-top: 20px;
            color: ;
        }

        form input, form select, form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4B0098;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #5e35b1;
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

       .logo2 {
        color: #4B0082;
        font-size: 24px;
        font-weight: bold;
        margin-right: 20px;
        cursor: pointer;
        transition: transform 0.2s;
        }

        .navbar .logo2:hover {
        transform: scale(1.1);
        }

        .dropdown-content3 {
            display: block; /* Changed from 'none' */
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content3 div {
            padding: 12px 16px;
            cursor: pointer;
        }

        .dropdown-content3 div:hover {
            background-color: #ddd;
        }



    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">EzTickEntry</div>
    <div class="nav-items">
        <a href="#" class="active">Host An Event</a>
        <a href="userdashboard.php">Dashboard</a>
    </div>
    <div class="user-dropdown">
        <?php
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
            <a href="logout.php">Log Out</a>
        </div>
    </div>
</div>

<div class="form-container">
    <div class="form-side"></div>
    <form id="hostlive" action="../action/hostliveperformanceevent_action.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <div class="logo2">Create A Live Performances/Festivals Event</div>
        
        <label for="title">Event Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="category">Category:</label>
        <!-- Use a readonly input field for the category -->
        <input type="text" id="category" name="category" value="Live Performances/Festivals" readonly>

        <!-- Include a hidden input to actually send the category ID -->
        <input type="hidden" name="category_id" value="2"> <!-- Assuming 2 is the ID for Live Performances/Festivals -->



        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="start_time">Start Time:</label>
        <input type="datetime-local" id="start_time" name="start_time" required>

        <label for="end_time">End Time:</label>
        <input type="datetime-local" id="end_time" name="end_time" required>

        <h2 style="color: #4B0052;">Venue Details</h2>
        
        <label for="venueName">Venue Name:</label>
        <input type="text" id="venueName" name="venueName" required>

        <label for="venueCountry">Country:   (Currently, we just work with african countries and very few foreing countires)</label>
        <select id="venueCountry" name="venueCountry" required>
            <option value="">Please select a country</option>
            <?php foreach ($countries as $countryID => $countryName): ?>
                <option value="<?= htmlspecialchars($countryID); ?>"><?= htmlspecialchars($countryName); ?></option>
            <?php endforeach; ?>
        </select>


        <label for="venueLocation">Location:</label>
        <textarea id="venueLocation" name="venueLocation" required></textarea>

        <label for="venueCapacity">Capacity:</label>
        <input type="number" id="venueCapacity" name="venueCapacity" required>

        <label for="venueContact">Contact Info:</label>
        <input type="text" id="venueContact" name="venueContact" required>

        <label for="event_image">Event Image:</label>
        <input type="file" id="event_image" name="event_image" accept="image/*">

        <button type="submit">Host Event</button>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        var dropdownBtn = document.querySelector('.user-name-button');
        dropdownBtn.onclick = function() {
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        }

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
function validateForm() {
    const today = new Date();
    const currentYear = today.getFullYear();
    const title = document.getElementById('title').value.trim();
    const description = document.getElementById('description').value.trim();
    const startTime = new Date(document.getElementById('start_time').value);
    const endTime = new Date(document.getElementById('end_time').value);
    const venueName = document.getElementById('venueName').value.trim();
    const venueCountry = document.getElementById('venueCountry').value;
    const venueLocation = document.getElementById('venueLocation').value.trim();
    const venueCapacity = parseInt(document.getElementById('venueCapacity').value, 10);
    const venueContact = document.getElementById('venueContact').value.trim();
    const phoneRegex = /^\+(?:[1-9]{1}[0-9]{1,2})\s?(?:[0-9]{6,14})$/;

    // Check for empty fields
    if (!title || !description || !startTime || !endTime || !venueName || !venueCountry || !venueLocation || isNaN(venueCapacity) || !venueContact) {
        alert('Please fill in all required fields.');
        return false;
    }

    // Check if the event dates are not in the past and are reasonable
    if (startTime < today || endTime < today || startTime >= endTime) {
        alert('Please provide valid future dates for the event. The start time must be before the end time.');
        return false;
    }

    // Ensure the event year is not less than the current year
    if (startTime.getFullYear() < currentYear || endTime.getFullYear() < currentYear) {
        alert('Event dates cannot be in the past year.');
        return false;
    }

    // Check for valid venue capacity
    if (venueCapacity <= 0) {
        alert('Please enter a positive number for venue capacity.');
        return false;
    }

    // Validate the contact info using regex
    if (!phoneRegex.test(venueContact)) {
        alert('Please enter a valid international phone number.');
        return false;
    }

    return true; // All validations passed
}
</script>

</body>
</html>
