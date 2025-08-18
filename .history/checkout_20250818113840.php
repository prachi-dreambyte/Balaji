<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access.");
}

// Create tables if they don't exist
$table = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    zipcode VARCHAR(20) NOT NULL,
    contact_no VARCHAR(15) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    discount INT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    payment_status VARCHAR(255) DEFAULT 'Un paid',
    payment_method VARCHAR(255),
    payment_id VARCHAR(100) DEFAULT NULL,
    razor_pay_orderId VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES signup(id) ON DELETE CASCADE
)";
$table2 = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";
mysqli_query($conn, $table);
mysqli_query($conn, $table2);

// Handle form submission for placing order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $user_id = intval($_POST['user_id'] ?? 0);
    
    // Get address details
    if (isset($_POST['selected_address_id']) && !empty($_POST['selected_address_id'])) {
        $address_id = intval($_POST['selected_address_id']);
        $address_stmt = $conn->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
        $address_stmt->bind_param("ii", $address_id, $user_id);
        $address_stmt->execute();
        $address_result = $address_stmt->get_result();
        
        if ($address_result->num_rows > 0) {
            $address = $address_result->fetch_assoc();
            $address_line = $address['address_line'];
            $city = $address['city'];
            $state = $address['state'];
            $zipcode = $address['zipcode'];
            $contact_no = $address['contact_no'];
        } else {
            die("Invalid address selected.");
        }
    } else {
        $address_line = mysqli_real_escape_string($conn, $_POST['address_line'] ?? '');
        $city = mysqli_real_escape_string($conn, $_POST['city'] ?? '');
        $state = mysqli_real_escape_string($conn, $_POST['state'] ?? '');
        $zipcode = mysqli_real_escape_string($conn, $_POST['zipcode'] ?? '');
        $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no'] ?? '');
        
        if (empty($address_line) || empty($city) || empty($state) || empty($zipcode) || empty($contact_no)) {
            die("Please fill all address fields.");
        }
    }
    
    $payment_method = 'razorpay';
    
    // Get cart total
    $cart_total = 0;
    $cart_stmt = $conn->prepare("SELECT SUM(p.price * c.quantity) as total 
                                FROM cart c 
                                JOIN products p ON c.product_id = p.id 
                                WHERE c.user_id = ?");
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    if ($cart_row = $cart_result->fetch_assoc()) {
        $cart_total = floatval($cart_row['total'] ?? 0) * 100; // Convert to paise for Razorpay
    }
    
    if ($cart_total <= 0 || $user_id <= 0) {
        die("Invalid cart data.");
    }
    
    // Generate a unique order ID
    $order_id = "ORD_" . uniqid();
    
    // Start transaction
    $conn->begin_transaction();
    $amount = $cart_total / 100;
    try {
        // Insert into orders table
        $order_stmt = $conn->prepare("INSERT INTO orders 
                                    (order_id, user_id, address_line, city, state, zipcode, contact_no, amount, status, payment_method) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
        $order_stmt->bind_param("sisssssds", $order_id, $user_id, $address_line, $city, $state, $zipcode, $contact_no,$amount, $payment_method);
        $order_stmt->execute();
        
        // Get cart items and insert into order_items
        $item_stmt = $conn->prepare("INSERT INTO order_items 
                                    (order_id, product_id, quantity, price) 
                                    SELECT ?, c.product_id, c.quantity, p.price 
                                    FROM cart c 
                                    JOIN products p ON c.product_id = p.id 
                                    WHERE c.user_id = ?");
        $item_stmt->bind_param("si", $order_id, $user_id);
        $item_stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Store order details in session for verification
        $_SESSION['razorpay_order_id'] = $order_id;
        $_SESSION['razorpay_amount'] = $cart_total;
        
        // Redirect based on payment method
        // if ($payment_method === 'razorpay') {
            header("Location: razorpay_payment.php?order_id=" . $order_id);
            exit();
        // } else { // COD or other methods
            // header("Location: order_success.php?order_id=" . $order_id);
            // exit();
    //   
        
    } catch (Exception $e) {
        $conn->rollback();
        die("Error processing order: " . $e->getMessage());
    }
}
// Normal page load (not form submission)
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    header("Location: login.php");
    exit();
}

// Get cart items for display
$cart_stmt = $conn->prepare("SELECT c.quantity, p.id, p.product_name, p.price, p.images 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

$cart_itemss = [];
$order_total = 0;

while ($cart_row = $cart_result->fetch_assoc()) {
    $subtotal = floatval($cart_row['quantity']) * floatval($cart_row['price']);
    $order_total += $subtotal;

    // Handle product images
    $image = 'default.jpg';
    if (!empty($cart_row['images'])) {
        $images = json_decode($cart_row['images'], true);
        $image =  !empty($images[0]) ? $images[0] : 'default.jpg';
    }

    $cart_itemss[] = [
        'id' => $cart_row['id'],
        'name' => $cart_row['product_name'] ?? 'Unknown Product',
        'quantity' => $cart_row['quantity'],
        'price' => $cart_row['price'],
        'subtotal' => $subtotal,
        'image' => $image
    ];
}

// Get user addresses
$addresses = [];
$error = '';

try {
    $address_stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
    $address_stmt->bind_param("i", $user_id);
    $address_stmt->execute();
    $address_result = $address_stmt->get_result();
    while ($address_row = $address_result->fetch_assoc()) {
        $addresses[] = $address_row;
    }
} catch (Exception $e) {
    $error = "Error fetching addresses: " . $e->getMessage();
}
?>

<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Checkout || Balaji/title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <!-- Place favicon.ico in the root directory -->
    <!-- google font -->
    <link href='https://fonts.googleapis.com/css?family=Lato:400,100,300,700,900' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>
    <!-- all css here -->
    <!-- bootstrap v3.3.6 css -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- animate css -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- jquery-ui.min css -->
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <!-- meanmenu css -->
    <link rel="stylesheet" href="css/meanmenu.min.css">
    <!-- owl.carousel css -->
    <link rel="stylesheet" href="css/owl.carousel.css">
    <!-- font-awesome css -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- nivo-slider css -->
    <link rel="stylesheet" href="css/nivo-slider.css">
    <!-- style css -->
    <link rel="stylesheet" href="style.css">
    <!-- responsive css -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- modernizr css -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    
    <!-- ... (keep existing head content) ... -->
    <style>
        .address-card {
            cursor: pointer;
            border: 2px solid #ccc;
            border-radius: 8px;
            transition: 0.3s;
            margin-bottom: 15px;
        }
        .address-card.selected {
            border-color: #007bff;
            background-color: #f0f8ff;
        }
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <!-- header-start -->
    <?php include 'header.php'; ?>
    <!-- header-end -->

    <!--checkout-start-->
    <div class="checkout-top-area">
        <div class="container">
            <div class="breadcrumb-area">
                <div class="breadcrumb">
                    <a href="index.php" title="Return to Home">
                        <i class="icon-home"></i>
                    </a>
                    <span class="navigation-pipe">></span>
                    <span class="navigation-page">Checkout</span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="entry-header">
                        <h1 class="entry-title">Checkout</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- checkout-area start -->
   <div class="checkout-area py-5">
    <div class="container">
        <form method="POST" id="checkout-form">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <div class="row">
                <!-- Billing Details -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fa fa-map-marker"></i> 1. Billing Details</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <?php if (!empty($addresses)): ?>
                                <h5 class="mb-3">Select a Saved Address:</h5>
                                <div class="row">
                                    <?php foreach ($addresses as $address): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="address-card p-3 h-100" onclick="selectAddress(this, <?php echo htmlspecialchars(json_encode($address)); ?>)">
                                                <input type="radio" name="selected_address_id" value="<?php echo $address['id']; ?>" hidden>
                                                <p class="mb-1"><strong><?php echo htmlspecialchars($address['address_line']); ?></strong></p>
                                                <p class="mb-1"><?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> - <?php echo htmlspecialchars($address['zipcode']); ?></p>
                                                <small class="text-muted">Phone: <?php echo htmlspecialchars($address['contact_no']); ?></small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="ship-box" onclick="toggleNewAddress()">
                                <label class="form-check-label" for="ship-box">
                                    Ship to a different address?
                                </label>
                            </div>

                            <div id="new-address-fields" class="hidden mt-3">
                                <div class="mb-3">
                                    <label class="form-label">Address Line</label>
                                    <input type="text" name="address_line" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" name="state" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Zipcode</label>
                                        <input type="text" name="zipcode" class="form-control">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="contact_no" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Order Notes</label>
                                <textarea name="order_notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0"><i class="fa fa-shopping-cart"></i> 2. Your Order</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_itemss as $item): ?>
                                        <tr>
                                            <td>
                                                <img src="admin/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="me-2" style="width:50px;">
                                                <?= htmlspecialchars($item['name']) ?> × <?= intval($item['quantity']) ?>
                                            </td>
                                            <td>₹<?= number_format($item['subtotal'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Order Total</th>
                                        <th>₹<?= number_format($order_total, 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning">
                            <h4 class="mb-0"><i class="fa fa-credit-card"></i> 3. Payment Method</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" value="bank_transfer" checked>
                                <label class="form-check-label">Direct Bank Transfer</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" value="cod">
                                <label class="form-check-label">Cash On Delivery</label>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="radio" name="payment_method" value="razorpay">
                                <label class="form-check-label">Razorpay</label>
                            </div>
                            <button type="submit" name="place_order" class="btn btn-lg btn-primary w-100">Place Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.address-card {
    border: 2px solid #ddd;
    border-radius: 6px;
    transition: all 0.3s ease;
}
.address-card:hover {
    border-color: #007bff;
    background: #f8f9fa;
}
.address-card.selected {
    border-color: #007bff;
    background: #e7f1ff;
    box-shadow: 0 0 8px rgba(0,0,0,0.1);
}
.hidden { display: none; }
</style> 
    <!-- checkout-area end -->

    <!-- ... (keep existing footer and scripts) ... -->

    <?php include 'footer.php'; ?>
    <!-- footer-end -->

    <!-- all js here -->
    <!-- jquery latest version -->
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <!-- bootstrap js -->
    <script src="js/bootstrap.min.js"></script>
    <!--jquery scrollUp js -->
    <script src="js/jquery.scrollUp.js"></script>
    <!-- owl.carousel js -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- meanmenu js -->
    <script src="js/jquery.meanmenu.js"></script>
    <!-- jquery-ui js -->
    <script src="js/jquery-ui.min.js"></script>
    <!-- wow js -->
    <script src="js/wow.min.js"></script>
    <!-- nivo slider js -->
    <script src="js/jquery.nivo.slider.pack.js"></script>
    <!-- countdown js -->
    <script src="js/countdown.js"></script>
    <!-- plugins js -->
    <script src="js/plugins.js"></script>
    <!-- main js -->
    <script src="js/main.js"></script>

    <script>
        // Function to select address and auto-fill form
        function selectAddress(cardElement, address) {
            // Deselect all cards
            document.querySelectorAll('.address-card').forEach(c => c.classList.remove('selected'));
            
            // Select current card
            cardElement.classList.add('selected');
            
            // Auto-fill form fields
            document.querySelector('input[name="address_line"]').value = address.address_line;
            document.querySelector('input[name="city"]').value = address.city;
            document.querySelector('input[name="state"]').value = address.state;
            document.querySelector('input[name="zipcode"]').value = address.zipcode;
            document.querySelector('input[name="contact_no"]').value = address.contact_no;
            
            // Hide new address fields if they were shown
            document.getElementById('new-address-fields').classList.add('hidden');
            document.getElementById('ship-box').checked = false;
        }
        
        // Function to toggle new address fields
        function toggleNewAddress() {
            const newAddressFields = document.getElementById('new-address-fields');
            if (document.getElementById('ship-box').checked) {
                newAddressFields.classList.remove('hidden');
                // Clear any selected address
                document.querySelectorAll('.address-card').forEach(c => c.classList.remove('selected'));
            } else {
                newAddressFields.classList.add('hidden');
            }
        }
        
        // Form validation before submission
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const selectedAddress = document.querySelector('.address-card.selected');
            const newAddressChecked = document.getElementById('ship-box').checked;
            
            if (!selectedAddress && !newAddressChecked) {
                e.preventDefault();
                alert('Please select an address or enter a new one');
                return false;
            }
            
            if (newAddressChecked) {
                const requiredFields = [
                    'address_line', 'city', 'state', 'zipcode', 'contact_no'
                ];
                
                for (const field of requiredFields) {
                    if (!document.querySelector(input[name="${field}"]).value.trim()) {
                        e.preventDefault();
                        alert('Please fill all required address fields');
                        return false;
                    }
                }
            }
            
            return true;
        });
    </script>
</body>
</html>