<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reg_number = trim($_POST['reg_number']);
    $password = $_POST['password'];

    if (empty($reg_number) || empty($password)) {
        echo "<script>alert('Please fill all fields.'); window.history.back();</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT id, password FROM students WHERE reg_number = ?");
    $stmt->bind_param("s", $reg_number);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Password correct, set session and redirect to student dashboard or home page
            $_SESSION['student_id'] = $id;
            $_SESSION['reg_number'] = $reg_number;

            echo "<script>alert('Login successful. Redirecting...'); window.location.href='student_view.html';</script>";
        } else {
            echo "<script>alert('Invalid password.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No account found with that registration number.'); window.history.back();</script>";
    }
    $stmt->close();
}
?>
