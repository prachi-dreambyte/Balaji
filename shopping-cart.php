<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
	die("Please login to access your cart.");
}

$user_id = $_SESSION['user_id'];

// Handle Add to Cart
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
	$product_id = intval($_GET['id']);

	// Check if already in cart
	$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
	$stmt->bind_param("ii", $user_id, $product_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
		$update->bind_param("ii", $user_id, $product_id);
		$update->execute();
	} else {
		$insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
		$insert->bind_param("ii", $user_id, $product_id);
		$insert->execute();
	}

	header("Location: shopping-cart.php?added=1");
	exit;
}

// Handle Remove from Cart
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
	$product_id = intval($_GET['id']);
	$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
	$stmt->bind_param("ii", $user_id, $product_id);
	$stmt->execute();
	header("Location: shopping-cart.php");
	exit;
}

// Fetch cart items from cart table
$stmt = $conn->prepare("SELECT c.quantity, p.id, p.product_name, p.price, p.images FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

$cart_items = [];
$total = 0;

while ($row = $cart_result->fetch_assoc()) {
	$image_array = json_decode($row['images'], true);
	$image = isset($image_array[0]) ? $image_array[0] : 'default.jpg';
	$subtotal = $row['price'] * $row['quantity'];
	$total += $subtotal;

	$cart_items[] = [
		'id' => $row['id'],
		'name' => $row['product_name'],
		'price' => $row['price'],
		'quantity' => $row['quantity'],
		'image' => $image,
		'subtotal' => $subtotal
	];
}

// Calculate dynamic flat rate (18% of subtotal)
$flat_rate = $total * 0.18;
$grand_total = $total + $flat_rate;
?>


<?php
 
if ( isset($_POST['apply_coupon'])) {
	
	$coupon_code = trim($_POST['coupon_code'] ?? '');
	$coins_to_use = intval($_POST['coins_to_use'] ?? 0);
	
	$_SESSION['coupon_error'] = '';
	$_SESSION['coins_error'] = '';
	$_SESSION['apply_success'] = `false`;
	

	$coupon_applied = false;
	$coins_applied = false;

	if (!empty($coupon_code)) {
		$stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active'");
		$stmt->bind_param("s", $coupon_code);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$coupon = $result->fetch_assoc();
			$_SESSION['coupon_code'] = $coupon_code;
			$_SESSION['coupon_discount'] = $coupon['discount_amount'] ?? 0;
			$coupon_applied = true;
		} else {
			$_SESSION['coupon_error'] = "Invalid or expired coupon.";
		}
	}

	if ($coins_to_use > 0) {
		$coin_stmt = $conn->prepare("SELECT coins FROM wallet WHERE id = ?");
		$coin_stmt->bind_param("i", $user_id);
		$coin_stmt->execute();
		$coin_result = $coin_stmt->get_result();
	$coin_row = $coin_result->fetch_assoc(); // ✅ fetch_assoc() returns associative array

// if ($coin_row) {
    echo $coin_row['coins']; // ✅ access like array
// } else {
//     echo "No record found.";
// }
		
			
		if ($coin_row = $coin_result->fetch_assoc()) {
			echo "dffghgytjgyjgu";
		
			$user_coins = intval($coin_row['coins']);
			if ($coins_to_use <= $user_coins) {
				echo $coins_to_use;
				$_SESSION['coins_applied'] = $coins_to_use;
				$coins_applied = true;
			} else {
				$_SESSION['coins_error'] = "You don't have enough coins.";
			}
		}
	}

	if ($coupon_applied || $coins_applied) {
		$_SESSION['apply_success'] = true;
		$apply_coin = 'true';
	}
	
	echo $_SESSION['apply_success'];
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
	<div class="mobile-menu-area d-lg-none d-block">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="mobile_menu">
						<nav id="mobile_menu_active">
							<ul>
								<li><a href="index.php">Home</a>

								</li>
								<li>
									<a href="shop.php">CATEGORY</a>
									<div class="mega-menu">
										<span style="display: grid; grid-template-columns: 200px 200px; gap: 10px;">

											<a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Executive Chair</a>
											<a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Plastic Chair</a>

											<a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Mesh Chair</a>
											<a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Plastic Table</a>

											<a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Staff Chairs</a>
											<a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Plastic Baby Chairs</a>

											<a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Visitor Chair</a>
											<a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Plastic Stools</a>
										</span>
									</div>
								</li>

								<li>
									<a href="shop.php">OFFER</a>

								</li>
								<li>
									<a href="contact.php">CONTACT</a>
									< /li>
								<li>
									<a href="#">ABOUT</a>
									<div class="version pages">
										<span>
											<a href="blog.php">Blog</a>
											<!-- <a href="contact-us.php">Contact Us</a> -->
											<a class="link-checkout" title="Checkout" href="http://localhost/vonia/checkout.php">Checkout</a>
											<a href="my-account.php">My account</a>
											<a href="product-details.php">Product details</a>
											<a href="shop.php">Shop Page</a>
											<a href="shopping-cart.php">Shoping Cart</a>
											<a href="wishlist.php">Wishlist</a>
											<!-- <a href="404.php">404 Error</a> -->
										</span>
								</li>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
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
										if (!empty($cart_items)):
											foreach ($cart_items as $item):

												// Decode JSON string to PHP array
												$images = json_decode($item['images'], true);

												// Check if it's an array and get the first image
												$firstImage = is_array($images) ? $images[0] : '';

										?>
												<tr>
													<td class="product-thumbnail">
														<a href="#">
															<img src="./admin/<?php echo $firstImage; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" width="80">
														</a>
													</td>
													<td class="product-name">
														<a href="#"><?php echo htmlspecialchars($item['product_name']); ?></a>
													</td>
													<td class="product-price">
														<span class="amount">₹<?php echo number_format($item['price'], 2); ?></span>
													</td>
													<td class="product-quantity">
														<!-- cart id ko as a product id use kiya gya hai -->
														<!-- <?php echo $item['id']; ?> -->
														<input type="hidden" name="id[]" value="<?php echo $item['id']; ?>">
														<input type="number" name="quantity[]" value="<?php echo $item['quantity']; ?>" min="1" />

														<!-- <form method="post" action="update-cart.php">
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" />
            <input type="submit" value="Update" />
        </form> -->
													</td>
													<td class="product-subtotal">
														₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
													</td>
													<td class="product-remove">
														<a href="shopping-cart.php?action=remove&id=<?php echo $item['id']; ?>">
															<i class="fa fa-times"></i>
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
						<form method="POST" >
							<div class="coupon">
								<h3>Apply Coupon / Coins</h3>
								<p>Enter your coupon code and number of coins to apply:</p>
								<input type="text" name="coupon_code" placeholder="Coupon code" />
								<input type="number" name="coins_to_use" placeholder="Coins to apply" min="0" />
								<input type="submit" name="apply_coupon" value="Apply" />
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
													<input type="radio" checked />
													<label>
														Flat Rate (18%):
														<span class="amount">₹<?php echo number_format($flat_rate, 2); ?></span>
													</label>
												</li>
												<li>
													<input type="radio" />
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

									<?php foreach ($cart_items as $item): ?>
										<input type="hidden" name="products[]" value="<?= $item['id'] ?>">
										<input type="hidden" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>">
									<?php endforeach; ?>

									<button type="submit" name="checkout">Proceed to Checkout</button>
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
								$('input[name="quantity[]"][value="' + item.id + '"]').closest('tr').find('.product-subtotal .amount').text('₹' + item.subtotal.toFixed(2));
							});

							// Update the totals
							$('.cart-subtotal .amount').text('₹' + response.total.toFixed(2));
							$('.order-total .amount').text('₹' + response.total.toFixed(2));

							// Show success message
							alert('Cart updated successfully!');

							window.location.reload(true);


						} else {
							alert('Error: ' + response.message);
						}
					},
					error: function() {
						alert('An error occurred while updating the cart.');
					}
				});
			});
		});
	</script>






	<script>
		document.getElementById('applyForm').addEventListener('submit', function(e) {
			e.preventDefault(); // Prevent page reload

			const formData = new FormData(this);

			fetch("", { // empty string = same page
					method: "POST",
					body: formData
				})
				.then(res => res.text()) // Expecting normal PHP response
				.then(html => {
					// Optional: Reload part of the page dynamically if needed
					// You can parse and extract data if returning JSON instead

					// Check hidden div value for toast status (OR use echo in response)
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
				});
		});
	</script>







</body>


</html> 