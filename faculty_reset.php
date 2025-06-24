<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = $_POST['email'];
    $otp      = $_POST['otp'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['otp'] == $otp && new DateTime() < new DateTime($user['otp_expiry'])) {
        $update = $pdo->prepare("UPDATE users SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
        $update->execute([$password, $email]);

        echo "<script>
                alert('Password reset successful!');
                window.location.href='login.php';
              </script>";
    } else {
        echo "<script>alert('Invalid or expired OTP.');</script>";
    }
}
?>
