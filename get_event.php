<?php
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(null);
    exit;
}

$servername = "127.0.0.1";
$username = "root";
$password = "Devi@335";
$dbname = "talenttrek";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    echo json_encode(null);
    exit;
}

$stmt = $conn->prepare("SELECT id, event_name, category, venue, registration_from, registration_to
