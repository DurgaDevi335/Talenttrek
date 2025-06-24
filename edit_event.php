<?php
$servername = "127.0.0.1";
$username = "root";
$password = "Devi@335";
$dbname = "talenttrek";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eventId = $_GET['id'] ?? '';
if (empty($eventId)) {
    echo json_encode(["error" => "Missing event ID"]);
    exit;
}

$sql = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Event not found"]);
    exit;
}

$row = $result->fetch_assoc();
echo json_encode($row);
$stmt->close();
$conn->close();
?>
