<?php
session_start();
if (!isset($_SESSION['otp'])) {
    echo "<script>alert('Session expired or invalid access.'); window.location.href='forgot_password.html';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Faculty | TalentTrek</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/reset-bg.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .container {
            width: 400px;
            margin: 7% auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 10px #666;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }
        input[type=text], input[type=password], input[type=submit] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        input[type=submit] {
            background-color: #27ae60;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background-color: #1e8449;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #555;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        <form action="update_password.php" method="post">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <input type="submit" value="Reset Password">
        </form>
        <a class="back-link" href="faculty_login.html">← Back to Login</a>
    </div>
</body>
</html>