<?php
session_start();
include '../connect.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

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

            // Send email
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'princeraj908071@gmail.com';
                $mail->Password   = 'avvl pbpq rsnb naxg';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('yourgmail@gmail.com', 'Balaji Support');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Reset your Balaji Password';
                $mail->Body    = "Click this link to reset your password:<br><a href='$resetLink'>$resetLink</a>";

                $mail->send();
                $message = "<div class='alert alert-success'>✅ A reset link has been sent to your email.</div>";
            } catch (Exception $e) {
                $message = "<div class='alert alert-danger'>❌ Mailer Error: {$mail->ErrorInfo}</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>❌ Email not found.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>❌ Please enter your email.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forget Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body {
        background: 
            radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1), transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(0,0,0,0.2), transparent 50%),
            linear-gradient(135deg, #ff9a9e 0%, #fad0c4 25%, #fad0c4 25%, #a18cd1 50%, #fbc2eb 75%, #8fd3f4 100%);
        background-size: cover;
        background-attachment: fixed;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', sans-serif;
    }
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        padding: 25px;
        background: #fff;
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card h2 {
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }
    .form-control {
        height: 48px;
        font-size: 16px;
        border-radius: 10px;
        border: 1px solid #ddd;
    }
    .btn-custom {
        height: 48px;
        font-size: 16px;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border: none;
        border-radius: 10px;
        transition: background 0.3s ease;
    }
    .btn-custom:hover {
        background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
    }
</style>

</head>
<body>
    <div class="container">
        <div class="col-md-6 col-lg-5 mx-auto">
            <div class="card">
                <h2 class="text-center">Forget Password</h2>
                <p class="text-center text-muted">Enter your email and we'll send you a reset link.</p>
                
                <?= $message ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" class="btn btn-custom w-100 text-white">Send Reset Link</button>
                </form>
                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
