<?php
session_start();
require_once '../settings/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$currentDateTime = date('Y-m-d H:i:s');

$sql = "SELECT
            e.EventID,
            e.Title AS EventTitle,
            COUNT(DISTINCT b.BookingID) AS TotalBookings,
            SUM(CASE WHEN b.BookingStatusID = 2 THEN 1 ELSE 0 END) AS TotalCheckedIn,
            SUM(CASE WHEN b.BookingStatusID = 4 THEN 1 ELSE 0 END) AS TotalNoShows,
            AVG(tt.Price) AS AverageTicketPrice,
            SUM(CASE WHEN b.BookingStatusID IN (2, 4) THEN b.TotalPrice ELSE 0 END) AS TotalRevenue,
            e.StartTime AS EventStartTime,
            e.EndTime AS EventEndTime,
            v.Name AS VenueName,
            SUM(CASE WHEN u.Gender = 'male' THEN 1 ELSE 0 END) AS MaleAttendees,
            SUM(CASE WHEN u.Gender = 'female' THEN 1 ELSE 0 END) AS FemaleAttendees,
            SUM(CASE WHEN u.Gender = 'other' THEN 1 ELSE 0 END) AS OtherGenderAttendees,
            SUM(CASE WHEN YEAR(CURRENT_DATE) - YEAR(u.DateOfBirth) BETWEEN 0 AND 18 THEN 1 ELSE 0 END) AS Age_0_18,
            SUM(CASE WHEN YEAR(CURRENT_DATE) - YEAR(u.DateOfBirth) BETWEEN 19 AND 30 THEN 1 ELSE 0 END) AS Age_19_30,
            SUM(CASE WHEN YEAR(CURRENT_DATE) - YEAR(u.DateOfBirth) BETWEEN 31 AND 50 THEN 1 ELSE 0 END) AS Age_31_50,
            SUM(CASE WHEN YEAR(CURRENT_DATE) - YEAR(u.DateOfBirth) > 50 THEN 1 ELSE 0 END) AS Age_50plus
        FROM 
            Events e
        JOIN 
            Bookings b ON e.EventID = b.EventID
        JOIN 
            Tickets t ON b.BookingID = t.BookingID
        JOIN 
            Users u ON b.UserID = u.UserID
        JOIN 
            TicketTiers tt ON t.TierID = tt.TierID
        JOIN 
            Venues v ON e.VenueID = v.VenueID
        WHERE 
            e.OrganizerID = ?
            AND e.EndTime < ?
            AND e.StartTime BETWEEN '2024-01-01' AND '2024-12-31'
            AND t.TicketStatusID = 1
        GROUP BY 
            e.EventID
        ORDER BY 
            e.StartTime DESC";

$stmt = $connection->prepare($sql);
$stmt->bind_param("is", $userId, $currentDateTime);
$stmt->execute();
$result = $stmt->get_result();

$eventDataAnalysis = [];
$genderCounts = ['Male' => 0, 'Female' => 0, 'Other' => 0];
$ageCounts = ['0-18' => 0, '19-30' => 0, '31-50' => 0, '50+' => 0];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $eventDataAnalysis[] = $row;
        $genderCounts['Male'] += $row['MaleAttendees'];
        $genderCounts['Female'] += $row['FemaleAttendees'];
        $genderCounts['Other'] += $row['OtherGenderAttendees'];
        $ageCounts['0-18'] += $row['Age_0_18'];
        $ageCounts['19-30'] += $row['Age_19_30'];
        $ageCounts['31-50'] += $row['Age_31_50'];
        $ageCounts['50+'] += $row['Age_50plus'];
    }
}

$stmt->close();
$connection->close();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Data Analysis</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
<style>
     
body, html {
font-family: 'Arial', sans-serif;
 margin: 0;
padding: 0;
background: #f4f4f4;
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
    table {
    margin: 20px auto;
    width: 95%;
    border-collapse: collapse;
    }
    
    th, td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ccc;
    }
    th {
    background-color: #673ab7;
    color: white;
    }
    tr:hover {
    background-color: #f5f5f5;
    }
    .chart-container {
    width: 80%;
    margin: 0 auto;
    }
    canvas {
    background-color: #fff;
    border: 1px solid #ccc;
    }

    .insights {
            width: 80%;
            margin: 20px auto;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 5px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

</style>


</head>
<body>
<div class="navbar">
    <div class="logo" style="color: #673ab7;">EzTickEntry</div> <!-- The style attribute seems to be missing the 'color' property -->
    <div class="nav-items">
        <a href="#" class="active">Data Analysis</a>
        <a href="userdashboard.php" > Dashboard</a>

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
<h1>Event Data Analysis</h1>
<table>
    <thead>
        <tr>
            <th>Event Title</th>
            <th>Total Bookings</th>
            <th>Total Checked In</th>
            <th>No Show Rate (%)</th>
            <th>Average Ticket Price</th>
            <th>Total Revenue</th>
            <th>Event Start Time</th>
            <th>Event End Time</th>
            <th>Venue Name</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($eventDataAnalysis as $event):
            $noShowRate = ($event['TotalBookings'] > 0) ? ($event['TotalNoShows'] / $event['TotalBookings']) * 100 : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($event['EventTitle']) ?></td>
            <td><?= htmlspecialchars($event['TotalBookings']) ?></td>
            <td><?= htmlspecialchars($event['TotalCheckedIn']) ?></td>
            <td><?= number_format($noShowRate, 2) ?>%</td>
            <td>$<?= number_format($event['AverageTicketPrice'], 2) ?></td>
            <td>$<?= number_format($event['TotalRevenue'], 2) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($event['EventStartTime'])) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($event['EventEndTime'])) ?></td>
            <td><?= htmlspecialchars($event['VenueName']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($eventDataAnalysis)): ?>
        <tr>
            <td colspan="9">No data available for this period.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>


<div class="insights">
<?php
    // Display insights and advice after the table
    echo "<h2>Event Analysis Insights and Advice</h2>";
    echo "<ul>";
    echo "<li>Overall Total Revenue for the year: $" . number_format(array_sum(array_column($eventDataAnalysis, 'TotalRevenue')), 2) . "</li>";

    if ($lastEvent['TotalRevenue'] > $firstEvent['TotalRevenue']) {
        echo "<li>Congratulations on the improvement in total revenue in the latest events compared to earlier in the year.</li>";
    } else {
        echo "<li>There is room for improvement in event revenue generation strategies.</li>";
    }

    if ($lastEventNoShowRate < $firstEventNoShowRate) {
        echo "<li>Congratulations! The no-show rate has improved over the course of the year.</li>";
    } else {
        echo "<li>Consider strategies to reduce no-shows, such as reminder emails or SMS notifications closer to event dates.</li>";
    }

    echo "<li>Consider targeting marketing efforts more on " . array_keys($genderCounts, max($genderCounts))[0] . " as they represent the majority of attendees.</li>";
    echo "<li>Enhance promotional activities for the age group " . array_keys($ageCounts, max($ageCounts))[0] . " as they form the bulk of your attendees.</li>";
    echo "</ul>";
    ?>

    <br>
</div>

<!-- Canvas for the Revenue Chart -->
<div class="chart-container">
    <canvas id="revenueChart"></canvas>
</div>



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
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($eventDataAnalysis, 'EventTitle')) ?>,
            datasets: [{
                label: 'Total Revenue',
                backgroundColor: 'rgb(63, 81, 181)',
                borderColor: 'rgb(63, 81, 181)',
                data: <?= json_encode(array_column($eventDataAnalysis, 'TotalRevenue')) ?>
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>

<!-- Gender Distribution Chart -->
<canvas id="genderChart"></canvas>
<script>
const genderCtx = document.getElementById('genderChart').getContext('2d');
const genderChart = new Chart(genderCtx, {
    type: 'pie',
    data: {
        labels: ['Male', 'Female', 'Other'],
        datasets: [{
            label: 'Gender Distribution',
            data: [<?= $genderCounts['Male'] ?>, <?= $genderCounts['Female'] ?>, <?= $genderCounts['Other'] ?>],
            backgroundColor: [
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 99, 132, 0.6)',
                'rgba(255, 205, 86, 0.6)'
            ],
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});
</script>


<!-- Age Demographics Chart -->
<canvas id="ageChart"></canvas>
<script>
const ageCtx = document.getElementById('ageChart').getContext('2d');
const ageChart = new Chart(ageCtx, {
    type: 'line',
    data: {
        labels: ['0-18', '19-30', '31-50', '50+'],
        datasets: [{
            label: 'Age Demographics',
            data: [<?= $ageCounts['0-18'] ?>, <?= $ageCounts['19-30'] ?>, <?= $ageCounts['31-50'] ?>, <?= $ageCounts['50+'] ?>],
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<footer class="footer">
   
</footer>



</body>
</html>
