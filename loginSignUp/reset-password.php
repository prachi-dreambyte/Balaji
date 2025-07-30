<?php
session_start();
include '../connect.php';

$message = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die('❌ Invalid or missing reset token.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $message = "❌ All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } else {
        // Find user by token
        $query = "SELECT * FROM signup WHERE reset_token = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password and clear token
            $update = "UPDATE signup SET password = ?, reset_token = NULL WHERE id = ?";
            $stmt2 = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt2, 'si', $hashed_password, $user['id']);
            mysqli_stmt_execute($stmt2);

            $message = "✅ Password reset successfully. <a href='login.php'>Login Now</a>";
        } else {
            $message = "❌ Invalid or expired token.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
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
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Reset Password</h2>
        <?php if (!empty($message)) : ?>
            <p style="color:<?= strpos($message, '✅') !== false ? 'green' : 'red'; ?>">
                <?= $message ?>
            </p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Reset Password</button>
        </form>
    </div>
</body>
</html>
