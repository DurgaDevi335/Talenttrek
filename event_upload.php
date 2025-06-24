<?php
$servername = "127.0.0.1";
$username = "root";
$password = "Devi@335";
$dbname = "talenttrek";
$port = 3307;

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data safely
$event_name = trim($_POST['eventName'] ?? '');
$category = trim($_POST['category'] ?? '');
$venue = trim($_POST['venue'] ?? '');
$registration_from = $_POST['registrationFrom'] ?? null;
$registration_to = $_POST['registrationTo'] ?? null;
$fee = $_POST['fee'] ?? 0;
$eligibility = trim($_POST['eligibility'] ?? '');
$link = trim($_POST['link'] ?? '');
$branches_array = $_POST['branchSelect'] ?? [];

if (empty($event_name) || empty($category) || empty($branches_array)) {
    echo "Error: Event name, category, and at least one branch are mandatory.";
    exit;
}

// Convert branches array to comma-separated string
$branches = implode(',', $branches_array);

// Allow NULL dates if empty
$registration_from = $registration_from ?: null;
$registration_to = $registration_to ?: null;

// Prepare statement with parameters
$stmt = $conn->prepare("INSERT INTO events (event_name, category, venue, registration_from, registration_to, fee, eligibility, link, branches) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param(
    "sssssdsss",
    $event_name,
    $category,
    $venue,
    $registration_from,
    $registration_to,
    $fee,
    $eligibility,
    $link,
    $branches
);

// Execute and check success
if ($stmt->execute()) {
    echo "Event uploaded successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
