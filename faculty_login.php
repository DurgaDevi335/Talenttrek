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

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST["email"]);
  $password = trim($_POST["password"]);

  $stmt = $conn->prepare("SELECT * FROM faculty WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row["password"])) {
      $_SESSION["faculty_id"] = $row["id"];
      $_SESSION["faculty_name"] = $row["name"];
      echo "<script>alert('Login successful!'); window.location.href='faculty_dashboard.html';</script>";
    } else {
      echo "<script>alert('Incorrect password.'); window.location.href='faculty_login.html';</script>";
    }
  } else {
    echo "<script>alert('No account found with that email.'); window.location.href='faculty_login.html';</script>";
  }
}
$conn->close();
?>
