<?php
session_start();
require_once 'connect.php';
require_once 'includes/coin_system.php';
require_once 'includes/session_timeout.php';

// ---------- AUTO UPDATE HANDLER ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['quantity'])) {
    $user_id = (int)$_SESSION['user_id'];
    $product_id = (int)$_POST['id'];
    $quantity   = max(1, (int)$_POST['quantity']);

    // Update cart quantity
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    $stmt->execute();

    // Determine account type for corporate discount logic
    $user_account_type = isset($_SESSION['account_type']) ? strtolower(trim($_SESSION['account_type'])) : null;
    $is_commercial = ($user_account_type === 'commercial');

    // Get product pricing with discounts
    $stmt = $conn->prepare("SELECT price, COALESCE(discount, 0) AS discount, COALESCE(corporate_discount, 0) AS corporate_discount FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    $base_price = (float)($product['price'] ?? 0);
    $discount = (float)($product['discount'] ?? 0);
    $corporate_discount = (float)($product['corporate_discount'] ?? 0);
    $final_price = $base_price - $discount;
    if ($is_commercial && $corporate_discount > 0) {
        $final_price -= $corporate_discount;
    }
    if ($final_price < 0) { $final_price = 0; }

    $subtotal = $final_price * $quantity;

    // Recompute totals across the cart using discounted prices
    $stmt = $conn->prepare("SELECT c.quantity, p.price, COALESCE(p.discount,0) AS discount, COALESCE(p.corporate_discount,0) AS corporate_discount
                            FROM cart c
                            JOIN products p ON c.product_id = p.id
                            WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $total = 0.0;
    while ($row = $result->fetch_assoc()) {
        $row_price = (float)$row['price'];
        $row_discount = (float)$row['discount'];
        $row_corp = (float)$row['corporate_discount'];
        $row_final = $row_price - $row_discount;
        if ($is_commercial && $row_corp > 0) {
            $row_final -= $row_corp;
        }
        if ($row_final < 0) { $row_final = 0; }
        $total += $row_final * (int)$row['quantity'];
    }

    $coupon_discount = (float)($_SESSION['coupon_discount'] ?? 0);
    $coins_applied   = (float)($_SESSION['coins_applied'] ?? 0);

    $flat_rate = $total * 0.18; // 18% shipping/tax
    $grand_total = $total + $flat_rate - $coupon_discount - $coins_applied;
    if ($grand_total < 0) { $grand_total = 0; }

    echo json_encode([
        "success"         => true,
        "subtotal"        => $subtotal,
        "subtotal_all"    => $total,
        "flat_rate"       => $flat_rate,
        "grand_total"     => $grand_total,
        "coupon_discount" => $coupon_discount,
        "coins_applied"   => $coins_applied
    ]);
    exit;
}



 

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
	

	// Limit quantity to available stock
	
     // Redirect with error if no stock
	// if ($quantity <= 0) {
		
	// 	header("Location: shopping-cart.php?error=outofstock");
	// 	exit;
	// }

	// Check if already in cart
	$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
	$stmt->bind_param("ii", $user_id, $product_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$current_qty = (int)$result->fetch_assoc()['quantity'];
		$new_qty = $current_qty + $quantity;

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
$items_count = 0;

while ($cart_row = $cart_result->fetch_assoc()) {
	$cart_id = $cart_row['id'];
	$product_id = $cart_row['product_id'];
	$quantity = $cart_row['quantity'];
	$product = null; // Reset the product at the start of each loop

	// Try fetching from 'products' table (include discount fields)
	$product_stmt = $conn->prepare("SELECT id, product_name, price, discount, corporate_discount, images, stock FROM products WHERE id = ?");
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
		// compute discounted final unit price
		$unit_price = (float)$product['price'];
		$discount = isset($product['discount']) ? (float)$product['discount'] : 0.0;
		$corporate_discount = isset($product['corporate_discount']) ? (float)$product['corporate_discount'] : 0.0;
		$user_account_type = isset($_SESSION['account_type']) ? strtolower(trim($_SESSION['account_type'])) : null;
		$is_commercial = ($user_account_type === 'commercial');
		$final_unit_price = $unit_price - $discount;
		if ($is_commercial && $corporate_discount > 0) {
			$final_unit_price -= $corporate_discount;
		}
		if ($final_unit_price < 0) { $final_unit_price = 0; }

		$subtotal = $final_unit_price * $quantity;
		$total += $subtotal;
		$items_count += (int)$quantity;

		$cart_itemss[] = [
			'id' => $product_id,  // Use product_id for consistency
			'cart_id' => $cart_id,
			'product_name' => $product['product_name'],
			'price' => $unit_price,
			'display_price' => $final_unit_price,
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

// COUPON & COINS handler (applies discounts)
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
	<title>Shopping cart balaji</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap 5 CSS (CDN) -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- FontAwesome (CDN) -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

	<!-- Optional: keep your existing CSS if you need (kept minimal) -->
	<link rel="stylesheet" href="style.css">

	<style>
		.shoppingHead{
			font-size:30px;
			font-weight:350;
			color: #fff;
		}
		.shoppingTr{
              font-size: 18px;
    color: #363636 !important;
    padding: 20px 10px !important;
	font-weight:350;
}  
.couponShopping{
	padding: 20px 10px !important;
}     
/* Improved Quantity Controls */
.quantity-control {
    display: flex;
    align-items: center;
    gap: 5px;
}

.quantity-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #495057;
}

.quantity-btn:hover {
    background: #e9ecef;
    color: #212529;
    transform: scale(1.05);
}

.quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.quantity-input {
    width: 50px;
    text-align: center;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 5px;
    font-weight: 500;
    transition: all 0.2s ease;
}
 
.quantity-input:focus {
    border-color: #845848;
    box-shadow: 0 0 0 0.25rem rgba(192, 107, 129, 0.25);
    outline: none;
}

/* Mobile responsive adjustments */
@media (max-width: 576px) {
    .quantity-control {
        gap: 3px;
    }
    
    .quantity-btn {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
    
    .quantity-input {
        width: 40px;
        font-size: 14px;
    }
}




		/* Page background and spacing */
		body {
			background: linear-gradient(180deg, #f7f9fc 0%, #ffffff 100%);
			font-family: "Open Sans", Arial, sans-serif;
			color: #222;
		}

		/* Breadcrumb area */
		.breadcrumb-area {
			background: linear-gradient(90deg, #d7bbf4ff, #e08debff);
			padding: 18px 0;
			margin-bottom: 28px;
		}
		.breadcrumb-area .breadcrumb a,
		.breadcrumb-area .breadcrumb-item {
			color: rgba(255,255,255,0.95) !important;
		}
		.breadcrumb-area .breadcrumb-item.active {
			color: rgba(255,255,255,0.9) !important;
			font-weight: 600;
		}

		/* Cards */
		.card.rounded-3 {
			border-radius: 12px !important;
		}
		.card .card-header.bg-primary {
			background: #845848 !important;
			color: #fff;
			border-bottom: none;
			border-top-left-radius: 12px;
			border-top-right-radius: 12px;
		}

		/* Cart table */
		.table td, .table th {
			vertical-align: middle;
			border-top: 0;
		}
		.table tbody tr {
			transition: all .18s ease;
			border-bottom: 1px solid #f1f3f5;
		}
		.table tbody tr:hover {
			background: #fbfbff;
			box-shadow: 0 6px 18px rgba(101, 84, 233, 0.04);
			transform: translateY(-1px);
		}
		.product-thumb {
			width: 84px;
			height: 84px;
			object-fit: cover;
			border-radius: 10px;
			border: 1px solid #eef1f6;
		}

		/* Quantity controls */
		.input-group .btn {
			border-radius: 8px;
		}
		.input-group .quantity-input {
			max-width: 86px;
			border-radius: 8px;
		}
		.quantity-available {
			font-size: 13px;
			color: #6c757d;
		}

		/* Discounts card */
		.card .card-header.bg-light {
			background: #f8f9fb;
			border-bottom: none;
			font-weight: 600;
		}
		.form-range {
			margin-bottom: 6px;
		}

		/* Order summary */
		.list-group-item {
			border: 0;
			padding-left: 0;
			padding-right: 0;
		}
		.summary-total {
			border-top: 1px dashed #e9ecef;
			margin-top: 10px;
			padding-top: 12px;
		}
		.btn-apply {
			background: linear-gradient(90deg,#ff6b6b,#ff8a6b);
			color: #fff;
			border: none;
		}

		/* Responsive tweaks */
		@media (max-width: 991px) {
			.card .card-header .badge {
				display: none;
			}
		}
		@media (max-width: 576px) {
			.product-thumb { width: 64px; height: 64px; }
			.input-group .quantity-input { max-width: 72px; }
		}
	</style>
</head>
<body>

	<?php include 'header.php'; ?>

	<!-- Breadcrumb -->
	<!-- <div class="breadcrumb-area">
	    <div class="container-fluid">
	        <nav aria-label="breadcrumb">
	            <ol class="breadcrumb mb-0">
	                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none"><i class="fas fa-home me-1"></i> Home</a></li>
	                <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
	            </ol>
	        </nav>
	    </div>
	</div> -->

	<!-- Main content -->
	<div class="cart-main-area py-5 px-5">
	    <div class="container-fluid">
	        <div class="row g-4">
	            <!-- Cart Items -->
	            <div class="col-lg-8">
	                <form action="update-cart.php" method="post" id="cartUpdateForm">
	                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
	                        <div class="card-header bg-primary bg-gradient text-white p-3">
	                            <div class="d-flex justify-content-between align-items-center">
	                                <h4 class="mb-0 shoppingHead"><i class="fas fa-shopping-cart me-2"></i>Your Shopping Cart</h4>
	                                <span class="badge bg-white text-black fs-6"><?php echo ($items_count ?? 0) . ' Item' . (($items_count ?? 0) > 1 ? 's' : ''); ?>
	                            </div>
	                        </div>
	                        
	                        <div class="card-body p-0">
	                            <?php if (!empty($cart_itemss)): ?>
	                                <div class="table-responsive">
	                                    <table class="table table-hover align-middle mb-0">
	                                        <thead class="table-light">
	                                            <tr>
	                                                <th scope="col" class="shoppingTr" style="width: 140px;">Image</th>
	                                                <th scope="col" class="shoppingTr">Product</th>
	                                                <th scope="col" class="shoppingTr ">Price</th>
	                                                <th scope="col" class="shoppingTr" style="width: 200px; text-align: center;">Quantity</th>
	                                                <th scope="col" class="shoppingTr text-end">Total</th>
	                                                <th scope="col" class="shoppingTr" style="width: 60px;">Remove</th>
	                                            </tr>
	                                        </thead>
	                                        <tbody>
	                                            <?php foreach ($cart_itemss as $index => $cart_item): ?>
	                                                <tr data-index="<?= $index ?>">
	                                                    <td>
	                                                        <a href="#" class="d-block">
	                                                            <img src="./admin/<?= htmlspecialchars($cart_item['image']) ?>" 
	                                                                 alt="<?= htmlspecialchars($cart_item['product_name']) ?>" 
	                                                                 class="product-thumb "
	                                                                 onerror="this.src='img/default.jpg';">
	                                                        </a>
	                                                    </td>
	                                                    <td>
	                                                        <h6 class="mb-1"><?= htmlspecialchars($cart_item['product_name']) ?></h6>
	                                                    </td>
	                                                    <td>₹<?= number_format($cart_item['price'], 2) ?></td>
	                                                    <td>
	                                                        <div class="input-group input-group-sm align-items-center">
	                                                            <button type="button" class="btn btn-outline-secondary minus-btn" data-index="<?= $index ?>" <?= $cart_item['quantity'] <= 1 ? 'disabled' : '' ?>>
	                                                                <i class="fas fa-minus"></i>
	                                                            </button>
	                                                            <input type="number" name="quantity[]" 
	                                                                   class="form-control text-center quantity-input" 
																	style="padding: 14px;"
	                                                                   value="<?= $cart_item['quantity'] ?>" 
	                                                                   min="1" 
	                                                                   data-index="<?= $index ?>"
	                                                                   max="<?= $cart_item['stock'] ?>">
	                                                            <button type="button" class="btn btn-outline-secondary plus-btn" data-index="<?= $index ?>" <?= $cart_item['quantity'] >= $cart_item['stock'] ? 'disabled' : '' ?>>
	                                                                <i class="fas fa-plus"></i>
	                                                            </button>
	                                                            <input type="hidden" name="id[]" value="<?= $cart_item['id'] ?>">
	                                                        </div>
	                                                        
	                                                    </td>
	                                                    <td class="text-end fw-bold">₹<?= number_format($cart_item['subtotal'], 2) ?></td>
	                                                    <td class="text-center">
	                                                        <a href="shopping-cart.php?action=remove&id=<?= $cart_item['id'] ?>" 
	                                                           class="text-black" 
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
	                                    <a href="shop.php" class="btn btn-outline-secondary px-4">
	                                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
	                                    </a>
	                                </div>
	                            <?php endif; ?>
	                        </div>
	                        
	                        <?php if (!empty($cart_itemss)): ?>
	                        <div class="card-footer bg-light">
	                            <div class="d-flex justify-content-between py-4">
	                                <div>
	                                    <button type="submit" class="btn btn-outline-secondary me-2">
	                                        <i class="fas fa-sync-alt me-2"></i>Update Cart
	                                    </button>
	                                    <!-- <a href="checkout.php" class="btn btn-success me-2">
	                                        <i class="fas fa-shopping-basket me-2"></i>Buy Now
	                                    </a> -->
	                                </div>
	                                <a href="shop.php" class="btn btn-outline-secondary">
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
	                            <div class="row">
	                                <div class="col-md-6">
	                                    <label for="coupon_code" class="form-label">Coupon Code</label>
	                                    <div class="input-group mb-3">
	                                        <input type="text" class="form-control couponShopping" id="coupon_code" name="coupon_code" 
	                                               placeholder="Enter coupon code" 
	                                               value="<?= htmlspecialchars($_SESSION['coupon_code'] ?? '') ?>">
	                                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="tooltip" 
	                                                title="Enter coupon code and click Apply">
	                                            <i class="fas fa-info-circle"></i>
	                                        </button>
	                                    </div>
	                                </div>

	                                <div class="col-md-6">
	                                    <label class="form-label">Reward Coins <small class="text-muted">(1 coin = ₹1)</small></label>
	                                    <div class="mb-2">
	                                        <input type="range" class="form-range" id="coins_range" min="0" 
	                                               max="<?= max(0, min($available_coins, (int)$total)) ?>" 
	                                               value="<?= htmlspecialchars($_SESSION['coins_applied'] ?? 0) ?>">
	                                    </div>
	                                    <div class="input-group">
	                                        <span class="input-group-text">₹</span>
	                                        <input type="number" class="form-control couponShopping" id="coins_to_use" name="coins_to_use" 
	                                               min="0" max="<?= $available_coins ?>" 
	                                               value="<?= htmlspecialchars($_SESSION['coins_applied'] ?? 0) ?>">
	                                        <button class="btn btn-outline-secondary" type="button" id="maxCoinsBtn">Max</button>
	                                    </div>
	                                    <small class="text-muted">Available: <span id="available-coins"><?= $available_coins ?></span></small>
	                                </div>
	                            </div>
	                            
	                            <div class="d-flex justify-content-between mt-3">
	                                <button type="submit" name="apply_coupon" class="btn btn-outline-secondary" <?= empty($cart_itemss) ? 'disabled' : '' ?>>
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
	                        <h5 class="mb-0 shoppingHead"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
	                    </div>
	                    
	                    <div class="card-body">
	                        <ul class="list-group list-group-flush">
	                            <li class="list-group-item d-flex justify-content-between py-2">
	                                <span>Subtotal (<?php echo ($items_count ?? 0) . ' item' . (($items_count ?? 0) > 1 ? 's' : ''); ?>)</span>
                                    <strong>₹<span id="summary-subtotal"><?= number_format($total, 2) ?></span></strong>
                                </li>
	                            
	                            <li class="list-group-item d-flex justify-content-between py-2">
                                <span>Shipping (18%)</span>
                                  <strong>₹<span id="summary-shipping"><?= number_format($flat_rate, 2) ?></span></strong>
                                 </li>
								 <li class="list-group-item summary-total d-flex justify-content-between py-2">
                                    <span>Grand Total</span>
                                     <strong>₹<span id="summary-grand"><?= number_format($grand_total, 2) ?></span></strong>
                                  </li>
	                            
	                            <?php if ($coupon_discount > 0): ?>
	                                <li class="list-group-item d-flex justify-content-between py-2 text-success">
	                                    <span>Coupon Discount</span>
	                                    <strong>-₹<?= number_format($coupon_discount, 2) ?></strong>
	                                </li>
	                            <?php endif; ?>
	                            
	                            <?php if ($coins_applied > 0): ?>
	                                <li class="list-group-item d-flex justify-content-between py-2 text-success">
	                                    <span>Reward Coins Used</span>
	                                    <strong>-₹<?= number_format($coins_applied, 2) ?></strong>
	                                </li>
	                            <?php endif; ?>
	                            
	                           
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
	                                <button type="submit" class="btn btn-dark btn-lg w-100 py-3" <?= empty($cart_itemss) ? 'disabled' : '' ?>>
	                                    <i class="fas fa-lock me-2"></i>Proceed to Checkout
	                                </button>
	                            </form>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	<!-- brand-area-end -->
	<!-- footer-start -->

	

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

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<script>
document.addEventListener('DOMContentLoaded', function() {

    // ================================
    // TOOLTIP INIT
    // ================================
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ================================
    // QUANTITY BUTTONS (plus/minus)
    // ================================
    document.querySelectorAll('.plus-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            let val = parseInt(input.value) || 1;
            const max = parseInt(input.max) || 9999;
            if (val < max) {
                input.value = val + 1;
                updateButtonStates(input);
            }
        });
    });

    document.querySelectorAll('.minus-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            let val = parseInt(input.value) || 1;
            const min = parseInt(input.min) || 1;
            if (val > min) {
                input.value = val - 1;
                updateButtonStates(input);
            }
        });
    });

    function updateButtonStates(input) {
        const minusBtn = input.parentElement.querySelector('.minus-btn');
        const plusBtn = input.parentElement.querySelector('.plus-btn');
        const max = parseInt(input.max) || 9999;
        const min = parseInt(input.min) || 1;
        const val = parseInt(input.value) || 1;

        minusBtn.disabled = val <= min;
        plusBtn.disabled = val >= max;
    }
	document.querySelectorAll(".plus-btn, .minus-btn, .quantity-input").forEach(el => {
    if (el.classList.contains("plus-btn") || el.classList.contains("minus-btn")) {
        el.addEventListener("click", handleUpdate);
    }
    if (el.classList.contains("quantity-input")) {
        el.addEventListener("change", handleUpdate);
    }
});


    // ================================
    // COINS RANGE SYNC
    // ================================
    const coinsRange = document.getElementById('coins_range');
    const coinsInput = document.getElementById('coins_to_use');
    const maxCoinsBtn = document.getElementById('maxCoinsBtn');

    if (coinsRange && coinsInput) {
        coinsRange.addEventListener('input', function() {
            coinsInput.value = this.value;
        });

        coinsInput.addEventListener('input', function() {
            let v = parseInt(this.value) || 0;
            if (v < parseInt(this.min || 0)) v = parseInt(this.min || 0);
            if (v > parseInt(this.max || 0)) v = parseInt(this.max || 0);
            this.value = v;
            coinsRange.value = v;
        });
    }

    if (maxCoinsBtn && coinsRange && coinsInput) {
        maxCoinsBtn.addEventListener('click', function() {
            const maxv = parseInt(coinsRange.max || 0);
            coinsRange.value = maxv;
            coinsInput.value = maxv;
        });
    }

    // ================================
    // AJAX CART UPDATE
    // ================================
    $('#cartUpdateForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'update-cart.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    response.items.forEach(function(item) {
                        var row = $('input[name="id[]"][value="' + item.id + '"]').closest('tr');
                        row.find('.product-subtotal').html('₹' + item.subtotal.toFixed(2));
                    });

                    $('.cart-subtotal .amount').text('₹' + response.total.toFixed(2));
                    $('.order-total .amount').text('₹' + response.total.toFixed(2));

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

function handleUpdate(e) {
    let index = this.dataset.index;
    let row   = document.querySelector(`tr[data-index="${index}"]`);
    let input = row.querySelector(".quantity-input");
    let id    = row.querySelector("input[name='id[]']").value;
    let qty   = parseInt(input.value) || 1;

    if (qty < 1) qty = 1;

    // AJAX with fetch
    fetch("shopping-cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&quantity=${qty}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // update row subtotal
            row.querySelector("td.text-end").innerHTML = "₹" + (data.subtotal || 0).toFixed(2);

            // update order summary values
            document.getElementById("summary-subtotal").innerText = (data.subtotal_all || 0).toFixed(2);
            document.getElementById("summary-shipping").innerText = (data.flat_rate || 0).toFixed(2);
            document.getElementById("summary-grand").innerText    = (data.grand_total || 0).toFixed(2);

            // update hidden checkout inputs
            document.querySelector("input[name='order_total']").value      = data.grand_total || 0;
            document.querySelector("input[name='coupon_discount']").value  = data.coupon_discount || 0;
            document.querySelector("input[name='coins_applied']").value    = data.coins_applied || 0;
        }
    })
    .catch(err => console.error("Cart update failed:", err));
}
</script>






 <?php include 'footer.php'; ?>




</body>
</html>