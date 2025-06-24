
<?php
session_start();
include 'config.php';

if (!isset($_SESSION['otp']) || !isset($_SESSION['email'])) {
    echo "<script>alert('Session expired or invalid access. Please try forgot password again.'); window.location.href='forgot_password.html';</script>";
    exit();
}

if (isset($_POST['otp'], $_POST['new_password'], $_POST['confirm_password'])) {
    $otp = $_POST['otp'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($otp != $_SESSION['otp']) {
        echo "<script>alert('Invalid OTP entered. Please try again.'); window.location.href='reset_password.php';</script>";
        exit();
    }

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.'); window.location.href='reset_password.php';</script>";
        exit();
    }

    // Hash the new password before saving
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $email = $_SESSION['email'];

    $sql = "UPDATE faculty SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        // Clear session variables after successful reset
        unset($_SESSION['otp']);
        unset($_SESSION['email']);

        echo "<script>alert('Password updated successfully. Please login now.'); window.location.href='faculty_login.html';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating password. Please try again later.'); window.location.href='reset_password.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Please fill all fields.'); window.location.href='reset_password.php';</script>";
    exit();
}
?>
