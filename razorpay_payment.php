<?php
session_start();
include 'connect.php';
require('vendor/autoload.php'); // Load Razorpay SDK // Include Razorpay SDK

use Razorpay\Api\Api;

// Verify order exists and belongs to user
if (!isset($_GET['order_id']) || !isset($_SESSION['user_id'])) {
    header("Location: checkout.php");
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Verify order exists and belongs to user
$order_stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$order_stmt->bind_param("si", $order_id, $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    header("Location: checkout.php");
    exit();
}

$order = $order_result->fetch_assoc();
$amount = $order['amount'] * 100; // Convert to paise

// Razorpay API credentials (replace with your actual keys)
$keyId = 'rzp_test_mcwl3oaRQerrOW';
$keySecret = 'N3hp4Pr3imA502zymNNyIYGI';
$displayCurrency = 'INR';

$api = new Api($keyId, $keySecret);

// Create Razorpay Order
$razorpayOrder = $api->order->create([
    'receipt'         => $order_id,
    'amount'          => $amount, // amount in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
]);

$razorpayOrderId = $razorpayOrder['id'];
$_SESSION['razorpay_order_id'] = $razorpayOrderId;
$status = 'pending';

// Save Razorpay order ID to your database
$update_stmt = $conn->prepare("UPDATE orders SET razorpay_order_id = ?, status = ? WHERE order_id = ?");
$update_stmt->bind_param("sss", $razorpayOrderId, $status , $order_id);
$update_stmt->execute();

$delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$delete_cart->bind_param("i", $user_id);
$delete_cart->execute();


?>
<!DOCTYPE html>
<html>

<head>
    <title>Complete Payment with Razorpay</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        .payment-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }

        .btn-pay {
            background-color: #528FF0;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <h2>Complete Your Payment</h2>
        <p>Order ID: <?php echo htmlspecialchars($order_id); ?></p>
        <p>Amount: ₹<?php echo number_format($order['amount'], 2); ?></p>

        <button id="rzp-button" class="btn-pay">Pay with Razorpay</button>

        <p style="margin-top: 20px;">
            <a href="checkout.php">Return to Checkout</a>
        </p>
    </div>

    <script>
        var options = {
            "key": "<?php echo $keyId; ?>",
            "amount": "<?php echo $amount; ?>",
            "currency": "INR",
            "name": "Your Store Name",
            "description": "Payment for Order #<?php echo $order_id; ?>",
            "image": "https://your-store-logo.png",
            "order_id": "<?php echo $razorpayOrderId; ?>",
            "handler": function(response) {
                // Redirect to verify payment after successful payment
                window.location.href = "verify_payment.php?razorpay_payment_id=" + response.razorpay_payment_id +
                    "&razorpay_order_id=" + response.razorpay_order_id +
                    "&razorpay_signature=" + response.razorpay_signature +
                    "&order_id=<?php echo $order_id; ?>";
            },
            "prefill": {
                "name": "Customer Name",
                "email": "customer@example.com",
                "contact": "<?php echo htmlspecialchars($order['contact_no']); ?>"
            },
            "notes": {
                "address": "<?php echo htmlspecialchars($order['address_line']); ?>",
                "order_id": "<?php echo $order_id; ?>"
            },
            "theme": {
                "color": "#528FF0"
            }
        };

        var rzp = new Razorpay(options);

        document.getElementById('rzp-button').onclick = function(e) {
            rzp.open();
            e.preventDefault();
        }

        // Auto-open Razorpay checkout (optional)
        // window.onload = function() {
        //     rzp.open();
        // }
    </script>
</body>

</html>