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

// Generate and display the invoice HTML
generateInvoiceHTML($order, $order_items, $subtotal);

function generateInvoiceHTML($order, $order_items, $subtotal) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invoice - Order #<?php echo htmlspecialchars($order['order_id']); ?></title>
        <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f4f7f6;
                color: #333;
            }
            .invoice-container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
            .invoice-header {
                text-align: center;
                border-bottom: 3px solid #845848;
                padding-bottom: 20px;
                margin-bottom: 30px;
            }
            .invoice-header h1 {
                color: #845848;
                margin: 0;
                font-size: 2.5em;
            }
            .invoice-header .order-id {
                font-size: 1.2em;
                color: #666;
                margin-top: 5px;
            }
            .company-info {
                text-align: center;
                margin-bottom: 30px;
            }
            .company-info h2 {
                color: #2c3e50;
                margin: 0;
                font-size: 1.5em;
            }
            .invoice-details {
                display: flex;
                justify-content: space-between;
                margin-bottom: 30px;
            }
            .invoice-details > div {
                flex: 1;
            }
            .invoice-details h3 {
                color: #2c3e50;
                border-bottom: 2px solid #845848;
                padding-bottom: 5px;
                margin-bottom: 15px;
            }
            .detail-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
            }
            .detail-label {
                font-weight: bold;
                color: #2c3e50;
            }
            .detail-value {
                color: #555;
            }
            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }
            .items-table th {
                background-color: #2c3e50;
                color: white;
                padding: 15px;
                text-align: left;
                font-weight: bold;
            }
            .items-table td {
                padding: 15px;
                border-bottom: 1px solid #ddd;
            }
            .items-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .text-right {
                text-align: right;
            }
            .text-center {
                text-align: center;
            }
            .total-section {
                border-top: 2px solid #845848;
                padding-top: 20px;
                margin-top: 20px;
            }
            .total-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
                font-size: 1.1em;
            }
            .grand-total {
                font-size: 1.3em;
                font-weight: bold;
                color: #2c3e50;
                border-top: 2px solid #845848;
                padding-top: 10px;
                margin-top: 10px;
            }
            .grand-total .amount {
                color: #845848;
            }
            .footer {
                text-align: center;
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #ddd;
                color: #666;
                font-size: 0.9em;
            }
            .print-button {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #845848;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: bold;
                z-index: 1000;
            }
            .print-button:hover {
                background: #a85d71;
            }
            @media print {
                body {
                    background-color: white;
                }
                .invoice-container {
                    box-shadow: none;
                    border: 1px solid #ddd;
                }
                .print-button {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <button class="print-button" onclick="window.print()">
            <i class="fa fa-print"></i> Print/Save as PDF
        </button>
        
        <div class="invoice-container">
            <div class="invoice-header">
                <h1>INVOICE</h1>
                <div class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
            </div>
            
            <div class="company-info">
                <h2>VONIA</h2>
                <p>Your trusted shopping destination</p>
            </div>
            
            <div class="invoice-details">
                <div>
                    <h3>Bill To:</h3>
                    <div class="detail-row">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['address_line']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">City/State/Pincode:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['city'] . ', ' . $order['state'] . ' - ' . $order['zipcode']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Contact:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['contact_no']); ?></span>
                    </div>
                </div>
                <div>
                    <h3>Order Information:</h3>
                    <div class="detail-row">
                        <span class="detail-label">Order Date:</span>
                        <span class="detail-value"><?php echo date('F j, Y, g:i A', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Order Status:</span>
                        <span class="detail-value"><?php echo htmlspecialchars(ucfirst($order['status'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Payment Status:</span>
                        <span class="detail-value"><?php echo htmlspecialchars(ucfirst($order['payment_status'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Payment Method:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                    </div>
                </div>
            </div>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td class="text-right">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td class="text-right">₹<?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
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
        
        <script src="js/vendor/jquery-1.12.4.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
}
?> 