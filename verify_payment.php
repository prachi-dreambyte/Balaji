<?php
session_start();
include 'connect.php';
require('vendor/autoload.php'); // Load Razorpay SDK

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$order_success = false;
$error_message = "";
$order_id = $_GET['order_id'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

if (!isset($_GET['razorpay_payment_id'], $_GET['razorpay_order_id'], $_GET['razorpay_signature'], $_GET['order_id'], $_SESSION['user_id'])) {
    $error_message = "Invalid payment data.";
} else {
    $razorpay_payment_id = $_GET['razorpay_payment_id'];
    $razorpay_order_id = $_GET['razorpay_order_id'];
    $razorpay_signature = $_GET['razorpay_signature'];

    // Razorpay credentials
    $keyId = 'rzp_test_mcwl3oaRQerrOW';
    $keySecret = 'N3hp4Pr3imA502zymNNyIYGI';

    $api = new Api($keyId, $keySecret);

    // Verify payment signature
    $attributes = [
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature' => $razorpay_signature
    ];

    try {
        $api->utility->verifyPaymentSignature($attributes);

        // Update order status
        $update_stmt = $conn->prepare("UPDATE orders SET payment_id = ?, payment_status = 'PAID', created_at = NOW() WHERE order_id = ? AND user_id = ?");
        $update_stmt->bind_param("ssi", $razorpay_payment_id, $order_id, $user_id);
        $update_stmt->execute();

        $order_success = true;

    } catch (SignatureVerificationError $e) {
        // Update as failed
        $fail_stmt = $conn->prepare("UPDATE orders SET payment_status = 'failed', updated_at = NOW() WHERE created_at = ? AND user_id = ?");
        $fail_stmt->bind_param("si", $order_id, $user_id);
        $fail_stmt->execute();

        $error_message = "Payment verification failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f7fb;
            padding: 30px;
        }
        .status-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }
        .status-success {
            color: green;
        }
        .status-failed {
            color: red;
        }
        .btn {
            margin-top: 20px;
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="status-container">
    <?php if ($order_success): ?>
        <h2 class="status-success">✅ Payment Successful!</h2>
        <p>Your payment for Order ID <strong><?php echo htmlspecialchars($order_id); ?></strong> has been successfully processed.</p>
        <p>Payment ID: <strong><?php echo htmlspecialchars($razorpay_payment_id); ?></strong></p>
        <a class="btn" href="my-account.php">View My Orders</a>
    <?php else: ?>
        <h2 class="status-failed">❌ Payment Failed</h2>
        <p><?php echo htmlspecialchars($error_message); ?></p>
        <a class="btn" href="checkout.php">Try Again</a>
    <?php endif; ?>
</div>

</body>
</html>
