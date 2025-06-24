<?php
session_start();
require 'config.php';

// Check if faculty is logged in
if (!isset($_SESSION['faculty_id'])) {
    header("Location: faculty_login.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];
$errors = [];
$success = "";

// Fetch current data
$stmt = $conn->prepare("SELECT name, email FROM faculty WHERE id = ?");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

// Handle update info form submission
if (isset($_POST['update_info'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);

    if (empty($new_name)) {
        $errors[] = "Full Name cannot be empty.";
    }
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        // Update in database
        $stmt = $conn->prepare("UPDATE faculty SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_name, $new_email, $faculty_id);
        if ($stmt->execute()) {
            $success = "Information updated successfully.";
            $name = $new_name;
            $email = $new_email;
        } else {
            $errors[] = "Database update failed.";
        }
        $stmt->close();
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        $errors[] = "All password fields are required.";
    } elseif ($new_pass !== $confirm_pass) {
        $errors[] = "New password and confirm password do not match.";
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM faculty WHERE id = ?");
        $stmt->bind_param("i", $faculty_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($current_pass, $hashed_password)) {
            $errors[] = "Current password is incorrect.";
        } else {
            // Update password
            $new_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE faculty SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hashed, $faculty_id);
            if ($stmt->execute()) {
                $success = "Password changed successfully.";
            } else {
                $errors[] = "Failed to update password.";
            }
            $stmt->close();
        }
    }
}

// For role display
$role = "Faculty";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Faculty Settings</title>
<style>
  body {
    font-family: Arial, Helvetica, sans-serif;
    background: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
    background-size: cover;
    margin: 0; padding: 0;
    color: #333;
  }
  .container {
    max-width: 500px;
    background: rgba(255, 255, 255, 0.9);
    margin: 60px auto;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    transition: background-color 0.3s, color 0.3s;
  }
  h1 {
    text-align: center;
    margin-bottom: 25px;
  }
  label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
  }
  input[type=text], input[type=email], input[type=password] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    box-sizing: border-box;
  }
  input[type=submit], button {
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 12px;
    font-size: 16px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  input[type=submit]:hover, button:hover {
    background-color: #0056b3;
  }
  .readonly {
    background-color: #eee;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 18px;
    color: #666;
  }
  .role {
    margin-bottom: 25px;
    font-weight: bold;
    font-size: 16px;
  }
  .messages {
    margin-bottom: 20px;
  }
  .error {
    background-color: #f8d7da;
    padding: 10px 15px;
    border-radius: 6px;
    color: #842029;
    margin-bottom: 10px;
  }
  .success {
    background-color: #d1e7dd;
    padding: 10px 15px;
    border-radius: 6px;
    color: #0f5132;
    margin-bottom: 10px;
  }
  .back-link {
    display: inline-block;
    margin-bottom: 25px;
    color: #007bff;
    cursor: pointer;
    text-decoration: none;
  }
  .back-link:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
<div class="container">

<a href="faculty_dashboard.html" class="back-link">&larr; Back to Dashboard</a>

<h1>Faculty Settings</h1>

<div class="role">Role: <?= htmlspecialchars($role) ?></div>

<div class="messages">
<?php foreach ($errors as $error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>
<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
</div>

<form method="post" action="">
  <label for="name">Full Name:</label>
  <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

  <label for="email">Email:</label>
  <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

  <input type="submit" name="update_info" value="Update Info">
</form>

<hr style="margin: 30px 0;">

<h2>Change Password</h2>
<form method="post" action="">
  <label for="current_password">Current Password:</label>
  <input type="password" id="current_password" name="current_password" required>

  <label for="new_password">New Password:</label>
  <input type="password" id="new_password" name="new_password" required>

  <label for="confirm_password">Confirm New Password:</label>
  <input type="password" id="confirm_password" name="confirm_password" required>

  <input type="submit" name="change_password" value="Change Password">
</form>

</div>
</body>
</html>
