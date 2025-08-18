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
    
    <style>
        /* Your existing CSS styles are great and remain unchanged */
        body { font-family: 'Open Sans', sans-serif; background-color: #f4f7f6; color: #333; line-height: 1.6; }
        .order-details-container { max-width: 1200px; margin: 50px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08); overflow: hidden; }
        .order-header { background: linear-gradient(135deg, #c06b81 0%, #a85d71 100%); color: white; padding: 30px; text-align: center; }
        .order-header h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 10px; }
        .order-id { font-size: 1.2rem; opacity: 0.9; }
        .order-content { padding: 40px; }
        .info-section { background: #f8f9fa; border-radius: 10px; padding: 25px; margin-bottom: 30px; }
        .info-section h3 { color: #2c3e50; font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; border-bottom: 2px solid #c06b81; padding-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .info-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #e9ecef; }
        .info-item:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #2c3e50; }
        .info-value { color: #555; text-align: right; }
        .order-items-table { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 30px; }
        .table-header { background: #2c3e50; color: white; padding: 20px; }
        .table-header h3 { margin: 0; font-size: 1.3rem; font-weight: 600; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th, .items-table td { padding: 15px; text-align: left; border-bottom: 1px solid #e9ecef; vertical-align: middle; }
        .items-table th { background: #f8f9fa; font-weight: 600; color: #2c3e50; }
        .items-table tr:last-child td { border-bottom: none; }
        .product-info { display: flex; align-items: center; gap: 15px; }
        .product-image { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #e9ecef; }
        .product-name { font-weight: 600; color: #2c3e50; }
        .price { font-weight: 600; color: #c06b81; }
        .text-right { text-align: right; }
        .order-summary { background: #f8f9fa; border-radius: 10px; padding: 25px; margin-top: 30px; }
        .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e9ecef; }
        .summary-row.grand-total { border-bottom: none; font-weight: 700; font-size: 1.2rem; color: #2c3e50; padding-top: 15px; }
        .total-amount { color: #c06b81; font-size: 1.5rem; }
        .back-button { background: #c06b81; color: white; padding: 12px 30px; border: none; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; margin-top: 30px; transition: background-color 0.3s ease; }
        .back-button:hover { background: #a85d71; color: white; text-decoration: none; }
        
        .download-invoice-btn {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .download-invoice-btn:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
        .download-invoice-btn i {
            margin-right: 8px;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .order-details-container { margin: 20px auto; border-radius: 8px; }
            .order-header { padding: 20px; }
            .order-header h1 { font-size: 2rem; }
            .order-content { padding: 20px; }
            .info-grid { grid-template-columns: 1fr; }
            .product-info { flex-direction: column; align-items: flex-start; gap: 10px; }
            .product-image { width: 50px; height: 50px; }
            .items-table th, .items-table td { padding: 10px; font-size: 14px; }
        }
        
        /* Loading state */
        .loading { text-align: center; padding: 50px; }
        .loading i { font-size: 3rem; color: #c06b81; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
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

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px;">
                <a href="my-account.php#my-orders" class="back-button">Back to My Orders</a>
                <a href="generate_invoice.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" class="download-invoice-btn" target="_blank">
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