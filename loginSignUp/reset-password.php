<?php
session_start();
include '../connect.php';

$message = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die('<div class="alert alert-danger text-center mt-5">❌ Invalid or missing reset token.</div>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $message = "❌ All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } else {
        $query = "SELECT * FROM signup WHERE reset_token = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $update = "UPDATE signup SET password = ?, reset_token = NULL WHERE id = ?";
            $stmt2 = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt2, 'si', $hashed_password, $user['id']);
            mysqli_stmt_execute($stmt2);

            $message = "✅ Password reset successfully. <a href='login.php' class='alert-link'>Login Now</a>";
        } else {
            $message = "❌ Invalid or expired token.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5 0%, #9face6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .form-container h2 {
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-custom {
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>🔒 Reset Password</h2>

        <?php if (!empty($message)) : ?>
            <div class="alert <?= strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger'; ?> text-center">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password" required>
            </div>
            <button type="submit" class="btn btn-success btn-custom">Reset Password</button>
        </form>
    </div>
</body>
</html>
