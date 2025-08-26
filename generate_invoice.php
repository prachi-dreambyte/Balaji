<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connect.php';

$user_id = $_SESSION['user_id'];
$order_id = filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_STRING);

if (!$order_id) {
    die("Invalid order ID");
}

// Get order details
$stmt_order = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt_order->bind_param("si", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows === 0) {
    die("Order not found or access denied");
}

$order = $result_order->fetch_assoc();
$stmt_order->close();

// Get order items
$stmt_items = $conn->prepare("
    SELECT oi.*, p.product_name AS product_name, p.images
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt_items->bind_param("s", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$order_items = [];
$subtotal = 0;

while ($item = $result_items->fetch_assoc()) {
    $item_total = $item['quantity'] * $item['price'];
    $subtotal += $item_total;

    $order_items[] = [
        'name'     => $item['product_name'] ?? "Product (ID: " . $item['product_id'] . ")",
        'quantity' => $item['quantity'],
        'price'    => $item['price'],
        'total'    => $item_total
    ];
}

$stmt_items->close();

generateInvoiceHTML($order, $order_items, $subtotal);

function generateInvoiceHTML($order, $order_items, $subtotal) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #<?php echo htmlspecialchars($order['order_id']); ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
        }
        .invoice-container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0px 5px 25px rgba(0,0,0,0.1);
        }
        .invoice-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #845848;
        }
        .invoice-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #845848;
        }
        .order-id {
            font-size: 1.1rem;
            color: #555;
            margin-top: 5px;
        }
        .company-info {
            text-align: center;
            margin: 20px 0;
        }
        .company-info h2 {
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .invoice-details {
            margin-bottom: 25px;
        }
        .invoice-details h3 {
            font-size: 1.2rem;
            border-bottom: 2px solid #845848;
            padding-bottom: 5px;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .detail-row {
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .items-table th {
            background: #2c3e50;
            color: #fff;
            padding: 12px;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .total-section {
            margin-top: 20px;
            border-top: 2px solid #845848;
            padding-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        .grand-total {
            font-size: 1.3rem;
            font-weight: bold;
            border-top: 2px solid #845848;
            padding-top: 10px;
            color: #2c3e50;
        }
        .grand-total .amount {
            color: #845848;
        }
        .footer {
            text-align: center;
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 0.9rem;
            color: #666;
        }
        .print-button {
            position: fixed;
            top: 15px;
            right: 15px;
            background: #845848;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            z-index: 999;
        }
        .print-button:hover {
            background: #6a403f;
        }
        @media print {
            .print-button { display: none; }
            body { background: #fff; }
            .invoice-container {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="fa fa-print"></i> Print / Save as PDF
    </button>

    <div class="invoice-container">
        <div class="invoice-header">
            <h1>INVOICE</h1>
            <div class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
        </div>

        <div class="company-info">
            <h2>BALAJI</h2>
            <p class="fs-5">We Sell Comfort Not Just Chair</p>
        </div>

        <div class="row invoice-details">
            <div class="col-md-6">
                <h3>Bill To</h3>
                <p><span class="detail-label">Address:</span> <?php echo htmlspecialchars($order['address_line']); ?></p>
                <p><span class="detail-label">City/State/Pincode:</span> <?php echo htmlspecialchars($order['city'] . ', ' . $order['state'] . ' - ' . $order['zipcode']); ?></p>
                <p><span class="detail-label">Contact:</span> <?php echo htmlspecialchars($order['contact_no']); ?></p>
            </div>
            <div class="col-md-6">
                <h3>Order Information</h3>
                <p><span class="detail-label">Order Date:</span> <?php echo date('F j, Y, g:i A', strtotime($order['created_at'])); ?></p>
                <p><span class="detail-label">Order Status:</span> <?php echo htmlspecialchars(ucfirst($order['status'])); ?></p>
                <p><span class="detail-label">Payment Status:</span> <?php echo htmlspecialchars(ucfirst($order['payment_status'])); ?></p>
                <p><span class="detail-label">Payment Method:</span> <?php echo htmlspecialchars($order['payment_method']); ?></p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td class="text-end">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td class="text-end">₹<?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₹<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="total-row">
                <span>Discount:</span>
                <span>- ₹<?php echo number_format($order['discount'], 2); ?></span>
            </div>
            <div class="total-row grand-total">
                <span>Grand Total:</span>
                <span class="amount">₹<?php echo number_format($order['amount'], 2); ?></span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>This is a computer generated invoice. No signature required.</p>
        </div>
    </div>

    <script src="js/bootstrap.min.js"></script>
</body>
</html>
<?php
}
?>
