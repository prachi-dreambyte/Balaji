<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connect.php';

$user_id = $_SESSION['user_id'];

// Get order ID from URL
$order_id = filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_STRING);

if (!$order_id) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - Balaji</title>
        <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <div class="container" style="margin-top: 100px; margin-bottom: 100px;">
            <div class="alert alert-danger">
                <h4><i class="fa fa-exclamation-triangle"></i> Error</h4>
                <p>A valid order ID is required.</p>
                <a href="my-account.php#my-orders" class="btn btn-primary">Back to My Orders</a>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

// Prepare query using the public order_id from orders table
$stmt_order = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
if ($stmt_order === false) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - Balaji</title>
        <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <div class="container" style="margin-top: 100px; margin-bottom: 100px;">
            <div class="alert alert-danger">
                <h4><i class="fa fa-exclamation-triangle"></i> Database Error</h4>
                <p>Failed to prepare order details query: <?php echo htmlspecialchars($conn->error); ?></p>
                <a href="my-account.php#my-orders" class="btn btn-primary">Back to My Orders</a>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}
$stmt_order->bind_param("si", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows === 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Not Found - Balaji</title>
        <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <div class="container" style="margin-top: 100px; margin-bottom: 100px;">
            <div class="alert alert-warning">
                <h4><i class="fa fa-exclamation-triangle"></i> Order Not Found</h4>
                <p>Order not found or you do not have permission to view this order.</p>
                <a href="my-account.php#my-orders" class="btn btn-primary">Back to My Orders</a>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

$order = $result_order->fetch_assoc();
$stmt_order->close();

// Fetch order items with product data
$stmt_items = $conn->prepare("
    SELECT oi.*, p.product_name AS product_name, p.images
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");

if ($stmt_items === false) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - Balaji</title>
        <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <div class="container" style="margin-top: 100px; margin-bottom: 100px;">
            <div class="alert alert-danger">
                <h4><i class="fa fa-exclamation-triangle"></i> Database Error</h4>
                <p>Failed to prepare order items query: <?php echo htmlspecialchars($conn->error); ?></p>
                <a href="my-account.php#my-orders" class="btn btn-primary">Back to My Orders</a>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

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
        'total'    => $item_total,
        'images'   => $item['images']
    ];
}

$stmt_items->close();




// It's good practice to close the main database connection when done
// $conn->close(); // Uncomment if you want to close it here, though it's often done in a common footer or script end
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Balaji</title>
    <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link href='https://fonts.googleapis.com/css?family=Lato:400,100,300,700,900' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/responsive.css">
    
    <style>body {
    font-family: 'Open Sans', sans-serif;
    background-color: #f4f7f6;
    color: #333;
    line-height: 1.6;
}

/* Main Container */
.order-details-container {
    max-width: 1100px;
    margin: 30px auto;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

/* Header */
.order-header {
    background: linear-gradient(135deg, #845848, #a67c68);
    color: white;
    padding: 30px 20px;
    text-align: center;
}
.order-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
}
.order-id {
    font-size: 1rem;
    opacity: 0.9;
}

/* Content */
.order-content {
    padding: 30px;
}

/* Section */
.info-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
    border: 1px solid #eee;
}
.info-section h3 {
    color: #2c3e50;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 15px;
    border-left: 4px solid #845848;
    padding-left: 10px;
}
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 15px;
}
.info-item {
    display: flex;
    justify-content: space-between;
    font-size: 0.95rem;
}
.info-label {
    font-weight: 600;
    color: #2c3e50;
}
.info-value {
    color: #555;
}

/* Table */
.order-items-table {
    margin-bottom: 25px;
    border: 1px solid #eee;
    border-radius: 10px;
    overflow: hidden;
}
.table-header {
    background: #c3aaa1;
    color: white;
    padding: 15px 20px;
}
.table-header h3 {
    margin: 0;
    font-size: 1.1rem;
}
.items-table {
    width: 100%;
    border-collapse: collapse;
}
.items-table th, 
.items-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #f1f1f1;
}
.items-table th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 0.95rem;
    color: #2c3e50;
}
.product-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.product-image {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ddd;
}
.product-name {
    font-weight: 600;
    color: #2c3e50;
}
.price {
    font-weight: 600;
    color: #845848;
}
.text-right {
    text-align: right;
}

/* Summary */
.order-summary {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-top: 25px;
    border: 1px solid #eee;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    font-size: 0.95rem;
}
.summary-row.grand-total {
    font-weight: 700;
    font-size: 1.1rem;
    border-top: 2px solid #ddd;
    margin-top: 10px;
    padding-top: 15px;
}
.total-amount {
    color: #845848;
    font-size: 1.4rem;
}

/* Buttons */
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 15px;
    margin-top: 30px;
}
.back-button, 
.download-invoice-btn {
    flex: 1;
    min-width: 160px;
    text-align: center;
    padding: 12px 20px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
}
.back-button {
    background: #845848;
    color: white;
}
.back-button:hover {
    background: #a67c68;
    color: white;
}
.download-invoice-btn {
    background: #6c757d;
    color: white;
}
.download-invoice-btn:hover {
    background: #5a6268;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .order-header h1 { font-size: 1.6rem; }
    .order-content { padding: 20px; }
    .items-table th, .items-table td { font-size: 0.9rem; padding: 10px; }
    .product-info { flex-direction: column; align-items: flex-start; }
    .product-image { width: 50px; height: 50px; }
}

    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="order-details-container">
        <div class="order-header">
            <h1>Order Details</h1>
            <div class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
        </div>

        <div class="order-content">
            <div class="info-section">
                <h3>Order Information</h3>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Order Date:</span><span class="info-value"><?php echo date('F j, Y, g:i A', strtotime($order['created_at'])); ?></span></div>
                    <div class="info-item"><span class="info-label">Order Status:</span><span class="info-value"><?php echo htmlspecialchars(ucfirst($order['status'])); ?></span></div>
                    <div class="info-item"><span class="info-label">Payment Status:</span><span class="info-value"><?php echo htmlspecialchars(ucfirst($order['payment_status'])); ?></span></div>
                    <div class="info-item"><span class="info-label">Payment Method:</span><span class="info-value"><?php echo htmlspecialchars($order['payment_method']); ?></span></div>
                </div>
            </div>

            <div class="info-section">
                <h3>Shipping Information</h3>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Address:</span><span class="info-value"><?php echo htmlspecialchars($order['address_line']); ?></span></div>
                    <div class="info-item"><span class="info-label">City/State/Pincode:</span><span class="info-value"><?php echo htmlspecialchars($order['city'] . ', ' . $order['state'] . ' - ' . $order['zipcode']); ?></span></div>
                    <div class="info-item"><span class="info-label">Contact:</span><span class="info-value"><?php echo htmlspecialchars($order['contact_no']); ?></span></div>
                </div>
            </div>

            <div class="order-items-table">
                <div class="table-header"><h3>Order Items</h3></div>
                <?php if (empty($order_items)): ?>
                    <div style="padding: 40px; text-align: center; color: #666;">
                        <i class="fa fa-shopping-bag" style="font-size: 3rem; margin-bottom: 20px; color: #ccc;"></i>
                        <p>No items found for this order.</p>
                    </div>
                <?php else: ?>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <?php 
                                            $images = json_decode($item['images'], true);
                                            $product_image = $images[0];
                                            
                                            ?>
                                            <img src="./admin/<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image">
                                            <span class="product-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-right"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td class="text-right price">₹<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="text-right price">₹<?php echo number_format($item['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="order-summary">
                <div class="summary-row"><span>Subtotal:</span><span>₹<?php echo number_format($subtotal, 2); ?></span></div>
                <div class="summary-row"><span>Discount:</span><span>- ₹<?php echo number_format($order['discount'], 2); ?></span></div>
                <div class="summary-row grand-total"><span>Grand Total:</span><span class="total-amount">₹<?php echo number_format($order['amount'], 2); ?></span></div>
            </div>

           <div class="action-buttons">
    <a href="my-account.php#my-orders" class="back-button">⬅ Back to My Orders</a>
    <a href="generate_invoice.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" 
       class="download-invoice-btn" target="_blank">
        <i class="fa fa-file-pdf-o"></i> View Invoice
    </a>
</div>

        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>