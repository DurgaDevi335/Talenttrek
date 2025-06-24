<?php
$servername = "127.0.0.1";
$username = "root";
$password = "Devi@335";
$dbname = "talenttrek";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die(json_encode([])); // Return empty json on connection error
}

// Fetch all events
$sql = "SELECT * FROM events ORDER BY created_at DESC";
$result = $conn->query($sql);

$events = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
echo json_encode($events);

$conn->close();
?>
