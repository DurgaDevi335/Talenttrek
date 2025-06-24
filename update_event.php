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

$id = $_POST['eventId'] ?? '';
$event_name = trim($_POST['eventName'] ?? '');
$category = trim($_POST['category'] ?? '');
$venue = trim($_POST['venue'] ?? '');
$registration_from = $_POST['registrationFrom'] ?: null;
$registration_to = $_POST['registrationTo'] ?: null;
$fee = $_POST['fee'] ?? 0;
$eligibility = trim($_POST['eligibility'] ?? '');
$link = trim($_POST['link'] ?? '');
$branches_array = $_POST['branchSelect'] ?? [];

if (empty($id) || empty($event_name) || empty($category) || empty($branches_array)) {
    echo "Error: Event ID, name, category, and at least one branch are mandatory.";
    exit;
}

$branches = implode(',', $branches_array);

$sql = "UPDATE events SET event_name=?, category=?, venue=?, registration_from=?, registration_to=?, fee=?, eligibility=?, link=?, branches=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssdsssi", $event_name, $category, $venue, $registration_from, $registration_to, $fee, $eligibility, $link, $branches, $id);

if ($stmt->execute()) {
    echo "Event updated successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
