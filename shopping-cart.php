<?php
session_start();
require_once 'connect.php';
require_once 'includes/coin_system.php';
require_once 'includes/session_timeout.php';

// Initialize coin system
$coinSystem = new CoinSystem($conn);

// Get user's available coins
$available_coins = 0;
if (isset($_SESSION['user_id'])) {
    $available_coins = $coinSystem->getCoinBalance($_SESSION['user_id']);
}


// Initialize session timeout handler
$sessionHandler = new SessionTimeoutHandler($conn);
$sessionHandler->handleSessionTimeout();

// Check login
if (!isset($_SESSION['user_id'])) {
	header("Location: loginSignUp/login.php");
	exit;
}

$user_id = (int)$_SESSION['user_id'];

// Handle Add to Cart
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'add') {
	$product_id = (int)$_GET['id'];
	$quantity = max(1, (int)($_GET['qty'] ?? 1));

	// Check product stock first
	$stock_check = $conn->prepare("SELECT stock FROM products WHERE id = ?");
	$stock_check->bind_param("i", $product_id);
	$stock_check->execute();
	$stock_result = $stock_check->get_result();
	$product_stock = 0;

	if ($stock_result->num_rows > 0) {
		$product_stock = (int)$stock_result->fetch_assoc()['stock'];
	} else {
		// Try home_daily_deal if not found in products
		$deal_check = $conn->prepare("SELECT stock FROM home_daily_deal WHERE id = ?");
		$deal_check->bind_param("i", $product_id);
		$deal_check->execute();
		$deal_result = $deal_check->get_result();

		if ($deal_result->num_rows > 0) {
			$product_stock = (int)$deal_result->fetch_assoc()['stock'];
		}
	}

	// Limit quantity to available stock
	$quantity = min($quantity, $product_stock);

	if ($quantity <= 0) {
		// Redirect with error if no stock
		header("Location: shopping-cart.php?error=outofstock");
		exit;
	}

	// Check if already in cart
	$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
	$stmt->bind_param("ii", $user_id, $product_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$current_qty = (int)$result->fetch_assoc()['quantity'];
		$new_qty = min($current_qty + $quantity, $product_stock);

		$update = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
		$update->bind_param("iii", $new_qty, $user_id, $product_id);
		$update->execute();
	} else {
		$insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
		$insert->bind_param("iii", $user_id, $product_id, $quantity);
		$insert->execute();
	}

	header("Location: shopping-cart.php?added=1");
	exit;
}
// Handle Remove from Cart
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'remove') {
	$product_id = (int)$_GET['id'];
	$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
	$stmt->bind_param("ii", $user_id, $product_id);
	$stmt->execute();

	header("Location: shopping-cart.php");
	exit;
}

// Handle Clear Discounts
if (isset($_GET['clear_discounts']) && (int)$_GET['clear_discounts'] === 1) {
	$coins_to_restore = $_SESSION['coins_applied'] ?? 0;
	if ($coins_to_restore > 0) {
		$coinSystem->addCoins($user_id, $coins_to_restore, "Cleared discounts from cart");
	}

	unset(
		$_SESSION['coupon_code'],
		$_SESSION['coupon_discount'],
		$_SESSION['coins_applied'],
		$_SESSION['coupon_error'],
		$_SESSION['coins_error'],
		$_SESSION['apply_success']
	);

	$sessionHandler->clearSessionTimeout();
	header("Location: shopping-cart.php");
	exit;
}

// ✅ Fetch cart items
$stmt = $conn->prepare("SELECT c.id, c.quantity, c.product_id FROM cart c WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

$cart_itemss = [];
$total = 0;



while ($cart_row = $cart_result->fetch_assoc()) {
	$cart_id = $cart_row['id'];
	$product_id = $cart_row['product_id'];
	$quantity = $cart_row['quantity'];
	$product = null; // Reset the product at the start of each loop

	// Try fetching from 'products' table
	$product_stmt = $conn->prepare("SELECT id, product_name, price, images, stock FROM products WHERE id = ?");
	$product_stmt->bind_param("i", $product_id);
	$product_stmt->execute();
	$product_result = $product_stmt->get_result();
	$product = $product_result->fetch_assoc();

	// If not found in 'products', try 'home_daily_deal'
	if (empty($product)) {
		$deal_stmt = $conn->prepare("SELECT id, product_name, price, images, stock FROM home_daily_deal WHERE id = ?");
		$deal_stmt->bind_param("i", $product_id);
		$deal_stmt->execute();
		$deal_result = $deal_stmt->get_result();
		$product = $deal_result->fetch_assoc();
	}

	// If product found in either table
	if (!empty($product)) {
		$images = json_decode($product['images'], true);
		$image = is_array($images) && !empty($images) ? $images[0] : 'default.jpg';
		$subtotal = $product['price'] * $quantity;
		$total += $subtotal;

		$cart_itemss[] = [
			'id' => $product_id,  // Use product_id for consistency
			'cart_id' => $cart_id,
			'product_name' => $product['product_name'],
			'price' => $product['price'],
			'quantity' => $quantity,
			'image' => $image,
			'subtotal' => $subtotal,
			'stock' => $product['stock']
		];
	}
}




// Calculate dynamic flat rate (18% of subtotal)
$flat_rate = $total * 0.18;

// Get applied coupon and coins from session
$coupon_discount = $_SESSION['coupon_discount'] ?? 0;
$coins_applied = $_SESSION['coins_applied'] ?? 0;

// Calculate grand total with discounts
$grand_total = $total + $flat_rate - $coupon_discount - $coins_applied;

// Ensure grand total doesn't go below 0
if ($grand_total < 0) {
	$grand_total = 0;
}
?>


<?php

if (isset($_POST['apply_coupon'])) {
	// Get coupon code and coins from form
	$coupon_code = trim($_POST['coupon_code'] ?? '');
	$coins_to_use = intval($_POST['coins_to_use'] ?? 10);

	// Reset previous session messages
	$_SESSION['coupon_error'] = '';
	$_SESSION['coins_error'] = '';
	$_SESSION['apply_success'] = false;

	$coupon_applied = false;
	$coins_applied = false;

	// -------------------- COUPON VALIDATION --------------------
	if (!empty($coupon_code)) {
		$stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active'");
		$stmt->bind_param("s", $coupon_code);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$coupon = $result->fetch_assoc();

			// Store applied coupon in session
			$_SESSION['coupon_code'] = $coupon_code;
			$_SESSION['coupon_discount'] = $coupon['discount_amount'] ?? 0;

			$coupon_applied = true;
		} else {
			$_SESSION['coupon_error'] = "Invalid or expired coupon.";
		}
	}

	// -------------------- COINS VALIDATION --------------------
	if ($coins_to_use > 0) {
		// Get REAL available balance
		$real_available = $coinSystem->getRealTimeBalance($user_id);

		// Validate against the real available balance
		if ($coins_to_use > $real_available) {
			$_SESSION['coins_error'] = "You don't have enough coins. Available: $real_available";
		} else {
			// Proceed with coin application
			$validation = $coinSystem->validateCoinUsageRealTime($user_id, $coins_to_use);

			if ($validation['valid']) {
				$previous_coins = $_SESSION['coins_applied'] ?? 0;
				if ($previous_coins > 0) {
					$coinSystem->addCoins($user_id, $previous_coins, "Restore previous application");
				}
				$deduct_success = $coinSystem->deductCoins($user_id, $coins_to_use, "Shopping cart discount");
				if ($deduct_success) {
					$_SESSION['coins_applied'] = $coins_to_use;
					$sessionHandler->updateCoinApplicationTime();
					$coins_applied = true;
				} else {
					$_SESSION['coins_error'] = "Failed to apply coins. Please try again.";
				}
			} else {
				$_SESSION['coins_error'] = $validation['message'];
			}
		}
	} else {
		// If no coins to use, restore any previously applied coins
		$previous_coins = $_SESSION['coins_applied'] ?? 0;
		if ($previous_coins > 0) {
			$coinSystem->addCoins($user_id, $previous_coins, "Restore previous application");
			unset($_SESSION['coins_applied']);
		}
	}

	// If at least one was applied successfully
	if ($coupon_applied || $coins_applied) {
		$_SESSION['apply_success'] = true;
	}

	// Force page refresh to update coin balance display
	header("Location: shopping-cart.php?refresh=" . time());
	exit;
}


?>





<!doctype html>
<html class="no-js" lang="">


<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Shopping cart || Vonia</title>
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
	<style>
		.buttons-cart {
			margin-bottom: 30px !important;
			overflow: hidden !important;
			padding-top: 25px !important;
		}

		.cart_totals {
			padding-top: 25px !important;
		}

		.coupon h3 {
			font-size: 20px !important;
			font-weight: 450 !important;
		}

		buttons-cart input,
		.coupon input[type="submit"],
		.buttons-cart a,
		.coupon-info p.form-row input[type="submit"] {
			background: #c06b81 none repeat scroll 0 0 !important;
		}

		.wc-proceed-to-checkout a {
			background: #c06b81 none repeat scroll 0 0 !important;
		}

		.wc-proceed-to-checkout button {
			background: #c06b81 none repeat scroll 0 0 !important;
			border: none;
			color: #fff;
			padding: 10px 10px;
			font-weight: 600;
		}

		.buttons-cart input,
		.coupon input[type="submit"],
		.buttons-cart a,
		.coupon-info p.form-row input[type="submit"] {
			background: #c06b81 none repeat scroll 0 0 !important;
		}

		.product-quantity input {
			text-align: center;
			width: 20%;
			padding: 5px;
		}
	</style>


</head>

<body>

	<!-- <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">✅ Cart updated successfully!</div>
<?php endif; ?> -->

	<!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
	<!-- header-start -->

	<?php include 'header.php'; ?>



	<!-- mainmenu-area-end -->
	<!-- mobile-menu-area-start -->

	<!-- mobile-menu-area-end -->
	
	<!-- header-end -->
	<!-- shopping-cart-start -->
	<div class="breadcrumb-area bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none"><i class="fas fa-home me-1"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
            </ol>
        </nav>
    </div>
</div>

<div class="cart-main-area py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <form action="update-cart.php" method="post" id="cartUpdateForm">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-primary bg-gradient text-white p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0 fw-bold"><i class="fas fa-shopping-cart me-2"></i>Your Shopping Cart</h4>
                                <span class="badge bg-white text-primary fs-6"><?= count($cart_itemss) ?> Items</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-0">
                            <?php if (!empty($cart_itemss)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" style="width: 100px;">Image</th>
                                                <th scope="col">Product</th>
                                                <th scope="col" class="text-end">Price</th>
                                                <th scope="col" style="width: 180px;">Quantity</th>
                                                <th scope="col" class="text-end">Total</th>
                                                <th scope="col" style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cart_itemss as $cart_item): ?>
                                                <tr>
                                                    <td>
                                                        <a href="#" class="d-block">
                                                            <img src="./admin/<?= htmlspecialchars($cart_item['image']) ?>" 
                                                                 alt="<?= htmlspecialchars($cart_item['product_name']) ?>" 
                                                                 class="img-fluid rounded-2 border"
                                                                 onerror="this.src='img/default.jpg';">
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <h6 class="mb-1"><?= htmlspecialchars($cart_item['product_name']) ?></h6>
                                                        <!-- <small class="text-muted">SKU: <?= htmlspecialchars($cart_item['sku'] ?? 'N/A') ?></small> -->
                                                    </td>
                                                    <td class="text-end">₹<?= number_format($cart_item['price'], 2) ?></td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <button type="button" class="btn btn-outline-secondary minus-btn px-3" <?= $cart_item['quantity'] <= 1 ? 'disabled' : '' ?>>
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            <input type="number" name="quantity[]" 
                                                                   class="form-control text-center quantity-input" 
                                                                   value="<?= $cart_item['quantity'] ?>" 
                                                                   min="1" 
                                                                   max="<?= $cart_item['stock'] ?>"
                                                                   readonly>
                                                            <button type="button" class="btn btn-outline-secondary plus-btn px-3" <?= $cart_item['quantity'] >= $cart_item['stock'] ? 'disabled' : '' ?>>
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                            <input type="hidden" name="id[]" value="<?= $cart_item['id'] ?>">
                                                        </div>
                                                        <small class="text-muted d-block mt-1">Available: <?= $cart_item['stock'] ?></small>
                                                    </td>
                                                    <td class="text-end fw-bold">₹<?= number_format($cart_item['subtotal'], 2) ?></td>
                                                    <td class="text-center">
                                                        <a href="shopping-cart.php?action=remove&id=<?= $cart_item['id'] ?>" 
                                                           class="text-danger" 
                                                           title="Remove item"
                                                           onclick="return confirm('Are you sure you want to remove this item?');">
                                                            <i class="far fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center p-5">
                                    <div class="mb-4">
                                        <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                                    </div>
                                    <h5 class="mb-3">Your cart is empty</h5>
                                    <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet</p>
                                    <a href="shop.php" class="btn btn-primary px-4">
                                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($cart_itemss)): ?>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-sync-alt me-2"></i>Update Cart
                                </button>
                                <a href="shop.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Discount Section -->
                <div class="card border-0 shadow-sm rounded-3 mt-4">
                    <div class="card-header bg-light p-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-tag me-2"></i>Apply Discount</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($_SESSION['coupon_error']) || !empty($_SESSION['coins_error']) || !empty($_SESSION['apply_success'])): ?>
                            <div class="alert alert-<?= !empty($_SESSION['apply_success']) ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_SESSION['coupon_error'] ?? $_SESSION['coins_error'] ?? 'Discount applied successfully!') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php 
                                unset($_SESSION['coupon_error'], $_SESSION['coins_error'], $_SESSION['apply_success']);
                            endif; 
                            ?>
                        
                        <form method="POST" id="applyForm">
                            <div class="mb-3">
                                <label for="coupon_code" class="form-label">Coupon Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="coupon_code" name="coupon_code" 
                                           placeholder="Enter coupon code" 
                                           value="<?= htmlspecialchars($_SESSION['coupon_code'] ?? '') ?>">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="tooltip" 
                                            title="Check for available coupons">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="coins_to_use" class="form-label">
                                    Reward Coins (Available: <span class="text-success fw-bold" id="available-coins"><?= $available_coins ?></span>)
                                </label>
                                <input type="range" class="form-range mb-2" id="coins_range" min="0" 
                                       max="<?= min($available_coins, $total) ?>" 
                                       value="<?= htmlspecialchars($_SESSION['coins_applied'] ?? 0) ?>">
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="coins_to_use" name="coins_to_use" 
                                           min="0" max="<?= $available_coins ?>" 
                                           value="<?= htmlspecialchars($_SESSION['coins_applied'] ?? '') ?>">
                                </div>
                                <small class="text-muted">1 coin = ₹1 discount</small>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" name="apply_coupon" class="btn btn-success" <?= empty($cart_itemss) ? 'disabled' : '' ?>>
                                    <i class="fas fa-check-circle me-2"></i>Apply Discounts
                                </button>
                                
                                <?php if (!empty($_SESSION['coupon_code']) || !empty($_SESSION['coins_applied'])): ?>
                                    <a href="shopping-cart.php?clear_discounts=1" class="btn btn-outline-danger">
                                        <i class="fas fa-times-circle me-2"></i>Clear Discounts
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary bg-gradient text-white p-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                    </div>
                    
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between py-3">
                                <span>Subtotal (<?= count($cart_itemss) ?> items)</span>
                                <strong>₹<?= number_format($total, 2) ?></strong>
                            </li>
                            
                            <li class="list-group-item d-flex justify-content-between py-3">
                                <span>Shipping</span>
                                <strong>₹<?= number_format($flat_rate, 2) ?></strong>
                            </li>
                            
                            <?php if ($coupon_discount > 0): ?>
                                <li class="list-group-item d-flex justify-content-between py-3 text-success">
                                    <span>Coupon Discount</span>
                                    <strong>-₹<?= number_format($coupon_discount, 2) ?></strong>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($coins_applied > 0): ?>
                                <li class="list-group-item d-flex justify-content-between py-3 text-success">
                                    <span>Reward Coins Used</span>
                                    <strong>-₹<?= number_format($coins_applied, 2) ?></strong>
                                </li>
                            <?php endif; ?>
                            
                            <li class="list-group-item d-flex justify-content-between py-3 border-top-2 border-dark">
                                <span class="fw-bold fs-5">Total Amount</span>
                                <strong class="fw-bold fs-5">₹<?= number_format($grand_total, 2) ?></strong>
                            </li>
                        </ul>
                        
                        <div class="d-grid gap-2 mt-4">
                            <form action="checkout.php" method="POST">
                                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                                <input type="hidden" name="order_total" value="<?= $grand_total ?>">
                                <input type="hidden" name="coupon_discount" value="<?= $coupon_discount ?>">
                                <input type="hidden" name="coins_applied" value="<?= $coins_applied ?>">
                                <input type="hidden" name="coupon_code" value="<?= $_SESSION['coupon_code'] ?? '' ?>">
                                <?php foreach ($cart_itemss as $item): ?>
                                    <input type="hidden" name="products[]" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>">
                                <?php endforeach; ?>
                                <button type="submit" class="btn btn-success btn-lg w-100 py-3" <?= empty($cart_itemss) ? 'disabled' : '' ?>>
                                    <i class="fas fa-lock me-2"></i>Proceed to Checkout
                                </button>
                            </form>
                            
                            <!-- <div class="text-center mt-3">
                                <img src="img/payment-methods.png" alt="Accepted payment methods" class="img-fluid" style="max-width: 250px;">
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
	<!-- brand-area-end -->
	<!-- footer-start -->

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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// PLUS button
			document.querySelectorAll('.plus-btn').forEach(function(btn) {
				btn.addEventListener('click', function() {
					const input = this.parentElement.querySelector('.quantity-input');
					let currentVal = parseInt(input.value);
					const max = parseInt(input.getAttribute('max'));

					if (!isNaN(currentVal) && currentVal < max) {
						input.value = currentVal + 1;
					}
				});
			});

			// MINUS button
			document.querySelectorAll('.minus-btn').forEach(function(btn) {
				btn.addEventListener('click', function() {
					const input = this.parentElement.querySelector('.quantity-input');
					let currentVal = parseInt(input.value);
					const min = parseInt(input.getAttribute('min'));

					if (!isNaN(currentVal) && currentVal > min) {
						input.value = currentVal - 1;
					}
				});
			});
		});
	</script>





	<script>
		$(document).ready(function() {
			$('#cartUpdateForm').on('submit', function(e) {
				e.preventDefault();

				$.ajax({
					url: 'update-cart.php',
					type: 'POST',
					data: $(this).serialize(),
					dataType: 'json',
					success: function(response) {
						if (response.status === 'success') {
							// Update each item in the table
							response.items.forEach(function(item) {
								// Find the row with the matching product ID
								var row = $('input[name="id[]"][value="' + item.id + '"]').closest('tr');
								// Update the subtotal display
								row.find('.product-subtotal').html('₹' + item.subtotal.toFixed(2));
							});

							// Update the totals
							$('.cart-subtotal .amount').text('₹' + response.total.toFixed(2));
							$('.order-total .amount').text('₹' + response.total.toFixed(2));

							// Show success message
							Swal.fire({
								toast: true,
								position: 'top-end',
								icon: 'success',
								title: 'Cart updated successfully!',
								showConfirmButton: false,
								timer: 3000
							});

							setTimeout(function() {
								window.location.reload(true);
							}, 1000);
						} else {
							Swal.fire({
								toast: true,
								position: 'top-end',
								icon: 'error',
								title: 'Error: ' + response.message,
								showConfirmButton: false,
								timer: 3000
							});
						}
					},
					error: function(xhr, status, error) {
						console.error('AJAX Error:', status, error);
						Swal.fire({
							toast: true,
							position: 'top-end',
							icon: 'error',
							title: 'An error occurred while updating the cart.',
							showConfirmButton: false,
							timer: 3000
						});
					}
				});
			});
		});
	</script>

<!-- JavaScript for enhanced functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity adjustment buttons
    document.querySelectorAll('.plus-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const max = parseInt(input.getAttribute('max'));
            const currentValue = parseInt(input.value);
            
            if (currentValue < max) {
                input.value = currentValue + 1;
                updateButtonStates(input);
            }
        });
    });

    document.querySelectorAll('.minus-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const currentValue = parseInt(input.value);
            
            if (currentValue > 1) {
                input.value = currentValue - 1;
                updateButtonStates(input);
            }
        });
    });

    function updateButtonStates(input) {
        const parentDiv = input.parentElement;
        const minusBtn = parentDiv.querySelector('.minus-btn');
        const plusBtn = parentDiv.querySelector('.plus-btn');
        const max = parseInt(input.getAttribute('max'));
        const currentValue = parseInt(input.value);
        
        minusBtn.disabled = currentValue <= 1;
        plusBtn.disabled = currentValue >= max;
    }

    // Sync range and number inputs for coins
    const coinsRange = document.getElementById('coins_range');
    const coinsInput = document.getElementById('coins_to_use');
    
    if (coinsRange && coinsInput) {
        coinsRange.addEventListener('input', function() {
            coinsInput.value = this.value;
        });
        
        coinsInput.addEventListener('input', function() {
            if (parseInt(this.value) > parseInt(this.max)) {
                this.value = this.max;
            }
            coinsRange.value = this.value;
        });
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>











</body>


</html>