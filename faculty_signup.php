<?php
$host = "localhost";
$user = "root";
$password = "Devi@335";
$dbname = "talenttrek";
$port = 3307;

$conn = new mysqli($host, $user, $password, $dbname, $port);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $password = trim($_POST["password"]);
  $confirm_password = trim($_POST["confirm_password"]);
  $invite_code = trim($_POST["invite_code"]);

  // Define the correct invite code (you can change this)
  $correct_invite_code = "FACULTY2025";

  if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($invite_code)) {
    echo "<script>alert('All fields including invite code are required.'); window.location.href='faculty_signup.html';</script>";
    exit;
  }

  // Check invite code correctness
  if ($invite_code !== $correct_invite_code) {
    echo "<script>alert('Invalid invite code. Please contact admin.'); window.location.href='faculty_signup.html';</script>";
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.location.href='faculty_signup.html';</script>";
    exit;
  }

  if ($password !== $confirm_password) {
    echo "<script>alert('Passwords do not match!'); window.location.href='faculty_signup.html';</script>";
    exit;
  }

  // Check if email already exists
  $check = $conn->prepare("SELECT id FROM faculty WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    echo "<script>alert('Email already registered. Please login.'); window.location.href='faculty_login.html';</script>";
    exit;
  }

  // Hash the password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Insert into database
  $stmt = $conn->prepare("INSERT INTO faculty (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $hashed_password);

  if ($stmt->execute()) {
    echo "<script>alert('Signup successful! Please login.'); window.location.href='faculty_login.html';</script>";
  } else {
    echo "<script>alert('Something went wrong. Please try again.'); window.location.href='faculty_signup.html';</script>";
  }

  $stmt->close();
  $check->close();
}
$conn->close();
?>
