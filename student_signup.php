<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reg_number = trim($_POST['reg_number']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($reg_number) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('Please fill all fields.'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Check if reg_number or email already exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE reg_number = ? OR email = ?");
    $stmt->bind_param("ss", $reg_number, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Registration number or email already exists.'); window.history.back();</script>";
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO students (reg_number, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $reg_number, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful. Please login now.'); window.location.href='student_login.html';</script>";
    } else {
        echo "<script>alert('Error occurred during signup. Please try again.'); window.history.back();</script>";
    }
    $stmt->close();
}
?>
