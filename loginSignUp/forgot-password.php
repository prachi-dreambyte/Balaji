<?php
session_start();
include '../connect.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        $query = "SELECT * FROM signup WHERE LOWER(email) = LOWER(?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            $token = bin2hex(random_bytes(32));
            $update = "UPDATE signup SET reset_token = ? WHERE id = ?";
            $stmt2 = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt2, 'si', $token, $user['id']);
            mysqli_stmt_execute($stmt2);

            $resetLink = "http://localhost/vonia/loginSignUp/reset-password.php?token=" . $token;

            // Send email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'yourgmail@gmail.com'; // Your Gmail address
                $mail->Password   = 'your_app_password';   // App Password (not your Gmail password)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                //Recipients
                $mail->setFrom('yourgmail@gmail.com', 'Vonia Support');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Reset your Vonia Password';
                $mail->Body    = "Click this link to reset your password:<br><a href='$resetLink'>$resetLink</a>";

                $mail->send();
                $message = "✅ A reset link has been sent to your email.";
            } catch (Exception $e) {
                $message = "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        } else {
            $message = "❌ Email not found.";
        }
    } else {
        $message = "❌ Please enter your email.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        body {
            padding: 40px;
            background: #f7f7f7;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        a { color: #007bff; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Forgot Password</h2>
        <?php if (!empty($message)) : ?>
            <p style="color:<?= strpos($message, '✅') !== false ? 'green' : 'red'; ?>">
                <?= $message ?>
            </p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Enter your email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>
        <br>
        <a href="login.php">🔙 Back to Login</a>
    </div>
</body>
</html>

