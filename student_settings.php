<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "Devi@335"; // Your DB password
$db = "talenttrek";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_SESSION['student_id'];

$sql = "SELECT reg_number, email, password FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $student = $result->fetch_assoc();
} else {
    echo "Student not found.";
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $update_email = false;
    $update_password = false;

    if ($new_email !== $student['email']) {
        $update_email = true;
    }

    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if (!password_verify($current_password, $student['password'])) {
            $msg = "<p style='color:red;'>Current password is incorrect.</p>";
        } elseif ($new_password !== $confirm_password) {
            $msg = "<p style='color:red;'>New passwords do not match.</p>";
        } else {
            $update_password = true;
        }
    }

    if ($update_email && $update_password) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE students SET email = ?, password = ? WHERE id = ?");
        $update->bind_param("ssi", $new_email, $hashed, $student_id);
        if ($update->execute()) {
            $msg = "<p style='color:green;'>Email and password updated successfully.</p>";
            $student['email'] = $new_email;
        } else {
            $msg = "<p style='color:red;'>Failed to update both fields.</p>";
        }
    } elseif ($update_email) {
        $update = $conn->prepare("UPDATE students SET email = ? WHERE id = ?");
        $update->bind_param("si", $new_email, $student_id);
        if ($update->execute()) {
            $msg = "<p style='color:green;'>Email updated successfully.</p>";
            $student['email'] = $new_email;
        } else {
            $msg = "<p style='color:red;'>Failed to update email.</p>";
        }
    } elseif ($update_password) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE students SET password = ? WHERE id = ?");
        $update->bind_param("si", $hashed, $student_id);
        if ($update->execute()) {
            $msg = "<p style='color:green;'>Password updated successfully.</p>";
        } else {
            $msg = "<p style='color:red;'>Failed to update password.</p>";
        }
    } elseif (empty($msg)) {
        $msg = "<p style='color:orange;'>No changes made.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Settings</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQGk8KCN0rdQnzS6fChm9Y9PeWjOm0g_TX-ZQ&s');
      background-size: cover;
      background-position: center;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #333;
    }

    .container {
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 10px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    input[readonly] {
      background-color: #f2f2f2;
    }

    button {
      margin-top: 20px;
      padding: 10px 20px;
      width: 100%;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background-color: #0056b3;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 15px;
      color: #007bff;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    .msg {
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Student Settings</h2>
  <?= $msg ? "<div class='msg'>$msg</div>" : "" ?>
  <form method="POST">
    <label>Registration Number</label>
    <input type="text" value="<?= htmlspecialchars($student['reg_number']) ?>" readonly>

    <label>Role</label>
    <input type="text" value="Student" readonly>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

    <label>Current Password </label>
    <input type="password" name="current_password" placeholder="Enter current password">

    <label>New Password</label>
    <input type="password" name="new_password" placeholder="Enter new password">

    <label>Confirm New Password</label>
    <input type="password" name="confirm_password" placeholder="Re-enter new password">

    <button type="submit">Update Settings</button>
  </form>
  <a class="back-link" href="student_view.html">← Back to Dashboard</a>
</div>
</body>
</html>
