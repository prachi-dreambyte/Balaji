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
    <title>Checkout || Vonia</title>
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
    <div class="checkout-area">
        <div class="container">
            <form  method="POST" id="checkout-form">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="checkbox-form">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <h3>Billing Details</h3>
                            
                            <?php if (!empty($addresses)): ?>
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h4>Select a Saved Address:</h4>
                                    </div>
                                    <?php foreach ($addresses as $address): ?>
                                        <div class="col-md-6">
                                            <div class="card address-card" onclick="selectAddress(this, <?php echo htmlspecialchars(json_encode($address)); ?>)">
                                                <div class="card-body">
                                                    <input type="radio" name="selected_address_id" value="<?php echo $address['id']; ?>" class="address-radio" hidden>
                                                    <p>
                                                        <?php echo htmlspecialchars($address['address_line']); ?><br>
                                                        <?php echo htmlspecialchars($address['city']); ?>,
                                                        <?php echo htmlspecialchars($address['state']); ?> -
                                                        <?php echo htmlspecialchars($address['zipcode']); ?><br>
                                                        <strong>Phone:</strong> <?php echo htmlspecialchars($address['contact_no']); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="different-address">
                                <div class="ship-different-title">
                                    <h3>
                                        <label>Ship to a different address?</label>
                                        <input id="ship-box" type="checkbox" onclick="toggleNewAddress()" />
                                    </h3>
                                </div>
                                <div id="new-address-fields" class="row hidden">
                                    <div class="col-md-12">
                                        <div class="checkout-form-list">
                                            <label>Address Line <span class="required">*</span></label>
                                            <input type="text" name="address_line" placeholder="Street address" required />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="checkout-form-list">
                                            <label>Town / City <span class="required">*</span></label>
                                            <input type="text" name="city" placeholder="Town / City" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkout-form-list">
                                            <label>State <span class="required">*</span></label>
                                            <input type="text" name="state" placeholder="State" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkout-form-list">
                                            <label>Postcode / Zip <span class="required">*</span></label>
                                            <input type="text" name="zipcode" placeholder="Postcode / Zip" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkout-form-list">
                                            <label>Phone <span class="required">*</span></label>
                                            <input type="text" name="contact_no" placeholder="Phone number" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="order-notes">
                                    <div class="checkout-form-list">
                                        <label>Order Notes</label>
                                        <textarea name="order_notes" cols="30" rows="3" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="your-order">
                            <h3>Your order</h3>
                            <div class="your-order-table table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="product-name">Product</th>
                                            <th class="product-total">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($cart_items)): ?>
                                            <?php foreach ($cart_itemss as $item): ?>
                                                <tr class="cart_item">
                                                    <td class="product-name">
                                                        <img src="admin/<?= htmlspecialchars($item['image']) ?>"
                                                            alt="<?= htmlspecialchars($item['name']) ?>"
                                                            style="width: 60px; height: auto; margin-right: 10px;">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                        <strong class="product-quantity"> × <?= intval($item['quantity']) ?></strong>
                                                    </td>
                                                    <td class="product-total">
                                                        <span class="amount">₹<?= number_format(floatval($item['subtotal']), 2) ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2">No items in cart</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="order-total">
                                            <th>Order Total</th>
                                            <td><strong><span class="amount">₹<?= number_format($order_total, 2) ?></span></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="payment-method">
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingOne">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    Direct Bank Transfer
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                            <div class="panel-body">
                                                <p>Make your payment directly into our bank account. Please use your Order ID as the payment reference.</p>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="payment_method" value="bank_transfer" checked>
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingTwo">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    Cash On Delivery
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                            <div class="panel-body">
                                                <p>Pay with cash when your order is delivered.</p>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="payment_method" value="cod">
                                                        Cash On Delivery
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingThree">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                    PayPal
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                            <div class="panel-body">
                                                <p>Pay via PayPal; you can pay with your credit card if you don't have a PayPal account.</p>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="razor_pay" value="razor_pay">
                                                        Razor Pay
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="order-button-payment">
                                    <input type="submit" name="place_order" value="Place order" class="btn btn-primary" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
                    if (!document.querySelector(`input[name="${field}"]`).value.trim()) {
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