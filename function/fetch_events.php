<?php
// function/fetch_events.php

function fetchUserManagedEvents($connection, $userId) {
    $events = [];
    $sql = "SELECT 
                e.EventID, e.Title, e.Description, e.StartTime, e.EndTime, 
                v.Name AS VenueName, v.Location AS VenueLocation, 
                i.ImagePath
            FROM Events e
            JOIN Venues v ON e.VenueID = v.VenueID
            LEFT JOIN Images i ON e.EventID = i.EventID
            WHERE e.OrganizerID = ?
            ORDER BY e.StartTime DESC";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }

    $stmt->close();
    return $events;
}
?>