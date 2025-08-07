<?php
session_start();
require_once 'connect.php';
require_once 'includes/coin_system.php';
require_once 'includes/session_timeout.php';

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
	</header>
	<!-- header-end -->
	<!-- shopping-cart-start -->
	<div class="breadcrumb-area">
		<div class="container">
			<div class="breadcrumb">
				<a href="index.php" title="Return to Home">
					<i class="icon-home"></i>
				</a>
				<span class="navigation-pipe">></span>
				<span class="navigation-page">
					Shopping cart
				</span>
			</div>
		</div>
	</div>
	<div class="cart-main-area">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="entry-header">
						<h1 class="entry-title">Shopping cart</h1>
					</div>
					<div class="table-content">
						<form action="update-cart.php" method="post" id="cartUpdateForm">

							<div class="table-content table-responsive">
								<table>
									<thead>
										<tr>
											<th class="product-thumbnail">Image</th>
											<th class="product-name">Product</th>
											<th class="product-price">Price</th>
											<th class="product-quantity">Quantity</th>
											<th class="product-subtotal">Total</th>
											<th class="product-remove">Remove</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if (!empty($cart_itemss)):
											foreach ($cart_itemss as $cart_item): ?>


												<tr>
													<td class="product-thumbnail">
														<a href="#">
															<img src="./admin/<?php echo $cart_item['image']; ?>" alt="<?php echo htmlspecialchars($cart_item['product_name']); ?>" width="80">
														</a>
													</td>
													<td class="product-name">
														<a href="#"><?php echo htmlspecialchars($cart_item['product_name']); ?></a>
													</td>
													<td class="product-price">
														<span class="amount">₹<?php echo number_format($cart_item['price'], 2); ?></span>
													</td>
													<td class="product-quantity text-center align-middle">
														<div class="d-inline-flex align-items-center justify-content-center">
															<button type="button"
																class="btn btn-danger btn-sm minus-btn px-3 py-2 fw-bold"
																aria-label="Decrease quantity"
																style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
																−
															</button>

															<input type="hidden" name="id[]" value="<?php echo $cart_item['id']; ?>">
															<input type="number"
																name="quantity[]"
																class="form-control form-control-sm text-center quantity-input fw-bold"
																value="<?php echo $cart_item['quantity']; ?>"
																min="1"
																max="<?php echo $cart_item['stock']; ?>"
																aria-label="Quantity"
																style="width: 70px; border-radius: 0; border-color: #ddd; background-color: #f8f9fa;" />

															<button type="button"
																class="btn btn-success btn-sm plus-btn px-3 py-2 fw-bold"
																aria-label="Increase quantity"
																style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
																+
															</button>
														</div>
													</td>



													<td class="product-subtotal">
														₹<?php echo number_format($cart_item['price'] * $cart_item['quantity'], 2); ?>
													</td>
													<td class="product-remove">
														<a href="shopping-cart.php?action=remove&id=<?= $cart_item['id']; ?>">
															<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="black" class="bi bi-trash3-fill" viewBox="0 0 16 16">
																<path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
															</svg>
														</a>
													</td>

												</tr>
											<?php
											endforeach;
										else:
											?>
											<tr>
												<td colspan="6">🛒 Your cart is empty.</td>
											</tr>
										<?php endif; ?>
									</tbody>

								</table>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="buttons-cart">
										<input type="submit" value="Update Cart" />
										<a href="shop.php">Continue Shopping</a>
									</div>
						</form>
						<?php
						// Get user's available coins using real-time balance
						$user_coins = $coinSystem->getRealTimeBalance($user_id);

						// Remove or modify this block to only initialize for truly new users
						// if ($user_coins == 0) {
						//     $coinSystem->initializeWallet($user_id, 50);
						//     $user_coins = 50;
						// }

						// Check for abandoned coins and restore them
						$coinSystem->checkAbandonedCoins($user_id);

						// Calculate available coins (show actual balance, do not subtract applied coins)
						$available_coins = $user_coins;
						?>
						<form method="POST" id="applyForm">
							<div class="coupon">
								<h3>Apply Coupon / Coins</h3>
								<p>Enter your coupon code and number of coins to apply:</p>
								<p><strong id="available-coins"><?php echo $available_coins; ?></strong> Available Coins</p>
								<input type="text" name="coupon_code" placeholder="Coupon code" value="<?php echo $_SESSION['coupon_code'] ?? ''; ?>" />
								<input type="number" name="coins_to_use" placeholder="Coins to apply" min="0" max="<?php echo $available_coins; ?>" value="<?php echo $_SESSION['coins_applied'] ?? ''; ?>" />
								<input type="submit" name="apply_coupon" value="Apply" />
								<?php if (!empty($_SESSION['coupon_code']) || !empty($_SESSION['coins_applied'])): ?>
									<a href="shopping-cart.php?clear_discounts=1" class="btn btn-danger" style="margin-left: 10px;">Clear Discounts</a>
								<?php endif; ?>
							</div>
						</form>

						<?php
						// Embed toast info in a hidden div for JS to pick up
						if (!empty($_SESSION['apply_success'])) {
							echo '<div id="toast-data" data-type="success" data-message="Coupon and/or coins applied successfully!" style="display:none;"></div>';
							unset($_SESSION['apply_success']);
						} elseif (!empty($_SESSION['coupon_error'])) {
							echo '<div id="toast-data" data-type="error" data-message="' . addslashes($_SESSION['coupon_error']) . '" style="display:none;"></div>';
							unset($_SESSION['coupon_error']);
						} elseif (!empty($_SESSION['coins_error'])) {
							echo '<div id="toast-data" data-type="error" data-message="' . addslashes($_SESSION['coins_error']) . '" style="display:none;"></div>';
							unset($_SESSION['coins_error']);
						}
						?>


					</div>

					<div class="col-md-4">
						<div class="cart_totals">
							<h2>Cart Totals</h2>
							<table>
								<tbody>
									<tr class="cart-subtotal">
										<th>Subtotal</th>
										<td>
											<span class="amount">₹<?php echo number_format($total, 2); ?></span>
										</td>
									</tr>

									<tr class="shipping">
										<th>Shipping</th>
										<td>
											<ul id="shipping_method">
												<li>
													<input type="radio" name="shipping_method" value="flat_rate" checked />
													<label>
														Flat Rate (18%):
														<span class="amount">₹<?php echo number_format($flat_rate, 2); ?></span>
													</label>
												</li>
												<li>
													<input type="radio" name="shipping_method" value="free_shipping" />
													<label>
														Free Shipping
													</label>
												</li>
											</ul>
											<p>
												<a class="shipping-calculator-button" href="#">Calculate Shipping</a>
											</p>
										</td>
									</tr>
									<?php if ($coupon_discount > 0): ?>
										<tr class="coupon-discount">
											<th>Coupon Discount</th>
											<td>
												<span class="amount">-₹<?php echo number_format($coupon_discount, 2); ?></span>
											</td>
										</tr>
									<?php endif; ?>
									<?php if ($coins_applied > 0): ?>
										<tr class="coins-discount">
											<th>Coins Applied</th>
											<td>
												<span class="amount">-₹<?php echo number_format($coins_applied, 2); ?></span>
											</td>
										</tr>
									<?php endif; ?>
									<tr class="order-total">
										<th>Total</th>
										<td>
											<strong>
												<span class="amount">₹<?php echo number_format($grand_total, 2); ?></span>
											</strong>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="wc-proceed-to-checkout">
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

									<button class="wc-proceed-to-checkout" type="submit" name="checkout">Proceed to Checkout</button>

								</form>

							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
	</div>
	</div>
	<!-- shopping-cart-end -->
	<!-- brand-area-start -->
	<div class="brand-area owl-carousel-space">
		<div class="container">
			<div class="row">
				<div class="brands">
					<div class="brand-carousel">
						<div class="col-md-12">
							<div class="single-brand">
								<a href="#">
									<img src="img/brand/1.jpg" alt="" />
								</a>
							</div>
						</div>
						<div class="col-md-12">
							<div class="single-brand">
								<a href="#">
									<img src="img/brand/2.jpg" alt="" />
								</a>
							</div>
						</div>
						<div class="col-md-12">
							<div class="single-brand">
								<a href="#">
									<img src="img/brand/3.jpg" alt="" />
								</a>
							</div>
						</div>
						<div class="col-md-12">
							<div class="single-brand">
								<a href="#">
									<img src="img/brand/4.jpg" alt="" />
								</a>
							</div>
						</div>
						<div class="col-md-12">
							<div class="single-brand">
								<a href="#">
									<img src="img/brand/5.jpg" alt="" />
								</a>
							</div>
						</div>
						<div class="col-md-12">
							<div class="single-brand">
								<a href="#">
									<img src="img/brand/6.jpg" alt="" />
								</a>
							</div>
						</div>
						<div class="col-md-12">
							<div class="single-brand">
								<a href="#">
									<img src="img/brand/7.jpg" alt="" />
								</a>
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






	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Show toast messages if they exist
			const toastData = document.getElementById('toast-data');
			if (toastData) {
				const type = toastData.dataset.type;
				const message = toastData.dataset.message;

				Swal.fire({
					toast: true,
					position: 'top-end',
					icon: type,
					title: message,
					showConfirmButton: false,
					timer: 3000
				});
			}

			// Update balance in real-time
			updateBalance();

			// Update balance every 30 seconds
			setInterval(updateBalance, 30000);
		});

		function updateBalance() {
			fetch('update_balance.php?t=' + Date.now())
				.then(response => response.json())
				.then(data => {
					if (data.error) {
						console.error('Error updating balance:', data.error);
						return;
					}

					console.log('Balance update:', data);

					// Update the available coins display
					const availableCoinsElement = document.getElementById('available-coins');
					if (availableCoinsElement) {
						availableCoinsElement.textContent = data.available_balance;
					}

					// Update the max value of coins input
					const coinsInput = document.querySelector('input[name="coins_to_use"]');
					if (coinsInput) {
						coinsInput.max = data.available_balance;
					}
				})
				.catch(error => {
					console.error('Error updating balance:', error);
				});
		}
	</script>







</body>


</html>