<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Create a new PHPMailer object
$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@gmail.com';         // 🔁 your Gmail address
    $mail->Password   = 'your_gmail_app_password';      // 🔁 Gmail App Password (NOT your Gmail login password)
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Email content
    $mail->setFrom('your_email@gmail.com', 'Your Name');
    $mail->addAddress('recipient@example.com');         // 🔁 recipient email

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Link';
    $mail->Body    = 'Click here to reset your password: <a href="http://yourwebsite.com/reset-password.php?token=xyz">Reset Password</a>';

    $mail->send();
    echo '✅ Message has been sent';
} catch (Exception $e) {
    echo '❌ Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
?>
