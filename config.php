<?php
$conn = new mysqli("localhost", "root", "Devi@335", "talenttrek", 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
