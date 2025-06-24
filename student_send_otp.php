<?php
session_start();
include 'config.php'; // Your DB connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if email exists in faculty table
    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Email not registered
        echo "<script>alert('This email is not registered. Please check and try again.'); window.location.href='student_forgot_password.html';</script>";
        exit();
    }

    // Email exists — proceed with OTP generation and sending

    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;

    // Send email using PHPMailer (your existing code below)
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ravipatidurgadevi6@gmail.com';      // Replace with your Gmail
        $mail->Password = 'kayteskzyqdkefkz';         // Replace with your App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your_email@gmail.com', 'Talenttrek otp');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body = "Your OTP to reset the password is <b>$otp</b>. If you didn't request this, please ignore.";

        $mail->send();
        echo "<script>alert('OTP sent to your email. Please check.'); window.location.href='student_reset_password.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Mailer Error: {$mail->ErrorInfo}'); window.location.href='student_forgot_password.html';</script>";
    }
} else {
    // If email not set in POST
    header("Location: student_forgot_password.html");
    exit();
}
?>
