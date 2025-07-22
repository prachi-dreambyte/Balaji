<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
include '../connect.php';


// Fetch categories for menu
$sql = "SELECT * FROM categories";
$result = mysqli_query($conn, $sql);

// Cart item count by user
$cart_count = 0;
$total_price = 0;

if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];

	$stmt = $conn->prepare("SELECT SUM(quantity) as total_items, SUM(quantity * p.price) as total_price
                            FROM cart c
                            JOIN products p ON c.product_id = p.id
                            WHERE user_id = ?");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$cart_result = $stmt->get_result();
	$row = $cart_result->fetch_assoc();

	$cart_count = $row['total_items'] ?? 0;
	$total_price = $row['total_price'] ?? 0;
}


if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>





<header class="header-4">
	<div class="header-top">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-sm-7 d-none d-sm-block">
					<div class="language">
						<div class="current">
							<span>English</span>
						</div>
						<ul class="lan-cur">
							<li class="selected">
								<img src="img/1/1.jpg" alt="en" />
								<span>English</span>
							</li>
							<li>
								<img src="img/1/2.jpg" alt="ar" />
								<a href="#" rel="alternate" title="اللغة العربية (Arabic)">
									<span>اللغة العربية</span>
								</a>
							</li>
						</ul>
					</div>
					<div class="currencies">
						<div class="current">
							<span class="cur-label">Currency :</span>
							<strong>GBP</strong>
						</div>
						<ul class="lan-cur">
							<li>
								<a href="#"> Dollar (USD) </a>
							</li>
							<li>
								<a href="#"> Pound (GBP) </a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-6 col-sm-5 col-xs-12">
					<div class="header-userinfo">
						<ul class="header-links">
							<li class="first">
								<a class="link-myaccount" title="My account" href="../my-account.php"> My account </a>
							</li>
							<li>
								<a class="link-wishlist wishlist_block" title="My wishlist" href="#">My wishlist</a>
							</li>
							<li>
								<a class="link-checkout" title="Checkout" href="http://localhost/vonia/checkout.php">Checkout</a>
							</li>

							<?php if (isset($_SESSION['user_id'])): ?>
								<li>
									<a class="logout" title="Log out of your customer account" rel="nofollow" href="?action=logout">Logout</a>
								</li>
							<?php else: ?>
								<li>
									<a class="login" title="Log in to your customer account" rel="nofollow" href="../loginSignUp/login.php">Log in</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="header-middle">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-4 col-sm-6 col-xs-12 d-none d-md-block">

					<form method="GET" action="../shop.php#product-list" class="search-block-top" id="searchForm">

						<input type="text" name="search" placeholder="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
						<input type="hidden" name="category" value="<?php echo isset($category_name) ? htmlspecialchars($category_name) : ''; ?>">


						<input type="hidden" name="sort_by" value="<?php echo isset($sort_by) ? htmlspecialchars($sort_by) : ''; ?>">
						<input type="hidden" name="limit" value="<?php echo isset($limit) ? (int)$limit : 12; ?>">


						<button class="btn btn-default" name="submit_search" type="submit"></button>

					</form>


				</div>
				<div class="col-md-4 col-sm-6 col-6">
					<div class="pos-logo">
						<a href="index.php">
							<img class="logo img-responsive" src="../img/balaji-logo-top.png" alt="" style="width: 200px; height: 200px;" />
						</a>
					</div>
				</div>
				<div class="col-md-4 col-sm-6 col-6">
					<div class="shopping-cart">
						<a href="shopping-cart.php" rel="nofollow" title="View my shopping cart">
							<b>cart
								<?php
								echo $cart_count . ' item' . ($cart_count > 1 ? 's' : '');
								?>

							</b>
						</a>

						<!-- Dropdown Cart -->
						<div class="top-cart-content">
							<?php
							$total_price = 0;
							if (isset($_SESSION['user_id'])) {
								$user_id = $_SESSION['user_id'];

								$stmt = $conn->prepare("SELECT c.quantity,c.id, p.product_name, p.price, p.images 
                            FROM cart c
                            JOIN products p ON c.product_id = p.id
                            WHERE c.user_id = ?");
								$stmt->bind_param("i", $user_id);
								$stmt->execute();
								$cart_items = $stmt->get_result();

								if ($cart_items->num_rows > 0) {
									while ($item = $cart_items->fetch_assoc()) {


										$product_name = $item['product_name'];
										$product_qty = $item['quantity'];
										$product_price = $item['price'];
										$product_image = "../admin/uploads/" . basename($item['images']);
										$product_total = $product_price * $product_qty;
										$total_price += $product_total;
							?>
										<div class="media header-middle-checkout">
											<div class="media-left check-img">
												<a href="#"><img src="<?php echo $product_image; ?>" alt="" style="width:50px;height:50px;" /></a>
											</div>
											<div class="media-body checkout-content">
												<h4 class="media-heading">
													<span class="cart-count"><?php echo $product_qty; ?>x</span>
													<a href="#"><?php echo $product_name; ?></a>
												</h4>
												<p>₹ <?php echo number_format($product_price, 2); ?></p>
											</div>
										</div>
									<?php
									}
									?>
									<div class="cart-total">
										<span>Total</span>
										<span><b>₹ <?php echo number_format($total_price, 2); ?></b></span>
									</div>
									<div class="checkout">
										<a href="../checkout.php"><span>Checkout <i class="fa fa-angle-right" aria-hidden="true"></i></span></a>
									</div>
							<?php
								} else {
									echo "<p style='padding:10px;'>Cart is empty.</p>";
								}
							}

							?>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
	<!-- mainmenu-area-start -->
	<div class="main-menu-area d-none d-lg-block">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="main-menu">
						<nav>
							<ul>
								<li>
									<a class="active" href="../index.php">home</a>

								</li>
								<li>
									<a href="../shop.php">CATEGORY</a>
									<div class="mega-menu">
										<span style="display: grid; grid-template-columns: 200px 200px; gap: 10px;">
											<?php
											if ($result) {
												while ($row = mysqli_fetch_assoc($result)) {
											?>

													<!-- <a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <?php echo $row['category_name']; ?>
        </a> -->


													<a href="../shop.php?category=<?php echo $row['category_name']; ?>#product-list"
														style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
														<?php echo $row['category_name']; ?>
													</a>




												<?php
												}
											} else {
												?>
												<span>Category not found.</span>
											<?php
											}
											?>
										</span>

									</div>
								</li>




								<li>
									<a href="index.php#deals">OFFER</a>

								</li>
								<li>
									<a href="../contact.php">CONTACT</a>
								</li>
								<li>
									<a href="../about-us.php">ABOUT US</a>

									<!-- <span>
													<a href="blog.php">Blog</a>
													
													<a class="link-checkout" title="Checkout" href="http://localhost/vonia/checkout.php">Checkout</a>
													<a href="my-account.php">My account</a>
													<a href="product-details.php">Product details</a>
													<a href="shop.php">Shop Page</a>
													<a href="shopping-cart.php">Shoping Cart</a>
													<a href="wishlist.php">Wishlist</a>
													
												</span> -->


								</li>
								<li>
									<a href="../blog.php">BLOG</a>
								</li>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- mainmenu-area-end -->
	<!-- mobile-menu-area-start -->
	<div class="mobile-menu-area d-lg-none d-block">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="mobile_menu">
						<nav id="mobile_menu_active">
							<ul>
								<li><a href="../index.php">Home</a>
									<!-- <ul>
													<li><a href="index11.php">Home 1</a></li>
													<li><a href="index-2.php">Home 2</a></li>
													<li><a href="index-3.php">Home 3</a></li>
													<li><a href="index.php">Home 4</a></li>
												</ul> -->
								</li>
								<li>
									<a href="../shop.php">CATEGORY</a>
									<div class="mega-menu">
										<span style="display: grid; grid-template-columns: 200px 200px; gap: 10px;">
											<?php
											if ($result) {
												while ($row = mysqli_fetch_assoc($result)) {
											?>
													<a href="../shop.php?category=<?php echo $row['category_name']; ?>#product-list"
														style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
														<?php echo $row['category_name']; ?>
													</a>

												<?php
												}
											} else {
												?>
												<span>Category not found.</span>
											<?php
											}
											?>
										</span>

									</div>
								</li>


								<li>
									<a href="index.php#deals">OFFER</a>

								</li>
								<li>
									<a href="../contact.php">CONTACT</a>
								</li>
								<li>
									<a href="../about-us.php">ABOUT US</a>
									<!-- <div class="version pages">
												<span>
													<a href="blog.php">Blog</a>
													
													<a class="link-checkout" title="Checkout" href="http://localhost/vonia/checkout.php">Checkout</a>
													<a href="my-account.php">My account</a>
													<a href="product-details.php">Product details</a>
													<a href="shop.php">Shop Page</a>
													<a href="shopping-cart.php">Shoping Cart</a>
													<a href="wishlist.php">Wishlist</a>
													<a href="404.php">404 Error</a>
												</span> -->

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