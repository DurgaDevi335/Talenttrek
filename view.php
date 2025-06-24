<?php
header('Content-Type: application/json');

$servername = "127.0.0.1";
$username = "root";
$password = "Devi@335";
$dbname = "talenttrek";
$port = 3307;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$category = $_GET['category'] ?? 'all';
$branch = $_GET['branch'] ?? 'all';

$sql = "SELECT * FROM events WHERE 1=1";
$params = [];
$types = "";

// Category filter with "all" support
if ($category !== 'all') {
    $sql .= " AND (category = ? OR category = 'all')";
    $params[] = $category;
    $types .= "s";
}

// Branch filter with "all" support
if ($branch !== 'all') {
    // branches field is comma separated, check if it contains selected branch OR contains 'all'
    $sql .= " AND (FIND_IN_SET(?, branches) OR FIND_IN_SET('all', branches))";
    $params[] = $branch;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "Prepare failed: " . $conn->error]);
    exit;
}

if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        "eventName" => $row['event_name'],
        "category" => $row['category'],
        "venue" => $row['venue'],
        "registrationFrom" => $row['registration_from'],
        "registrationTo" => $row['registration_to'],
        "fee" => (float)$row['fee'],
        "eligibility" => $row['eligibility'],
        "link" => $row['link'],
        "branches" => $row['branches'],
    ];
}

echo json_encode($events);

$stmt->close();
$conn->close();
?>
