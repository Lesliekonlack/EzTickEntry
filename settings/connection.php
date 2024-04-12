 <?php
// Database credentials
   $servername = "localhost";
   $username = "root";
   $password = "QlA]rs+N8Gt9";
   $dbname = "EzTickEntry";
   
   // Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);
   
   // Check connection
   if ($conn->connect_error) {
       echo "Error connecting";
   }
   else{
       echo "connected";
   
?>
