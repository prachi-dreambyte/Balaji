<?php

session_start();
$compare_count = isset($_SESSION['compare_list']) ? count($_SESSION['compare_list']) : 0;

include 'connect.php';

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
	$_SESSION['cart'] = [];
}

// Handle Add to Cart
if (isset($_GET['action']) && $_GET['action'] == 'add') {
	$product_id = intval($_GET['id']);

	$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
	$stmt->bind_param("i", $product_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$product = $result->fetch_assoc();

	if ($product) {
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = [];
		}

		if (isset($_SESSION['cart'][$product_id])) {
			$_SESSION['cart'][$product_id]['quantity'] += 1;
		} else {
			// Extract image first
			$image_array = json_decode($product['images'], true);
			$image = isset($image_array[0]) ? $image_array[0] : 'default.jpg';

			// Add to session cart
			$_SESSION['cart'][$product_id] = [
				'id' => $product['id'],
				'name' => $product['product_name'],
				'price' => $product['price'],
				'quantity' => 1,
				'image' => $image
			];
		}

		header("Location: shop.php?added=1#product-list");
		exit;
	} else {
		echo "Product not found.";
	}
}

// Get category_name from URL parameter
$category_raw = isset($_GET['category']) ? trim($_GET['category']) : '';
$category_raw = urldecode($category_raw); // decode %20 to space
$category_names = array_map('trim', explode(',', $category_raw));
$category_names = array_filter($category_names, function ($c) {
	return $c !== '';
});
// $category_name = isset($_GET['category']) ? trim($_GET['category']) : '';

// Default limit: 12 products per page
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;

$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';


// Get sort_by from URL
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';

// Build ORDER BY condition
$order_by = '';
switch ($sort_by) {
	case 'price_asc':
		$order_by = ' ORDER BY price ASC';
		break;
	case 'price_desc':
		$order_by = ' ORDER BY price DESC';
		break;
	case 'name_asc':
		$order_by = ' ORDER BY product_name ASC';
		break;
	case 'name_desc':
		$order_by = ' ORDER BY product_name DESC';
		break;
	default:
		$order_by = ''; // no sorting
}

$like = "%$search_term%";
$params = [];
$types = '';
$conditions = [];

$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$enable_price_filter = $min_price > 0 && $max_price > 0;

if (!empty($category_names)) {
	$placeholders = implode(',', array_fill(0, count($category_names), '?'));
	$conditions[] = "category IN ($placeholders)";
	$params = array_merge($params, $category_names);
	$types .= str_repeat('s', count($category_names));
}

if (!empty($search_term)) {
	$conditions[] = "(product_name LIKE ? OR category LIKE ?)";
	$params[] = $like;
	$params[] = $like;
	$types .= 'ss';
}

if ($enable_price_filter) {
	$conditions[] = "price BETWEEN ? AND ?";
	$params[] = $min_price;
	$params[] = $max_price;
	$types .= 'dd';
}

$where = !empty($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '';

$sql = "SELECT * FROM products" . $where . $order_by . " LIMIT ?";
$params[] = $limit;
$types .= 'i';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);



$debug_info['sql'] = $sql;


$stmt->execute();
$result = $stmt->get_result();
$allRows = $result->fetch_all(MYSQLI_ASSOC);

// Debug: Get all unique categories from products table
$debug_sql = "SELECT DISTINCT category FROM products ORDER BY category";
$debug_stmt = $conn->prepare($debug_sql);
$debug_stmt->execute();
$debug_result = $debug_stmt->get_result();
$debug_categories = $debug_result->fetch_all(MYSQLI_ASSOC);
$debug_info['available_categories'] = array_column($debug_categories, 'category');
$debug_stmt->close();

// Get all categories for sidebar
$cat_sidebar_sql = "SELECT * FROM categories ORDER BY created_at DESC";
$cat_sidebar_stmt = $conn->prepare($cat_sidebar_sql);
$cat_sidebar_stmt->execute();
$cat_sidebar_result = $cat_sidebar_stmt->get_result();
$categories = $cat_sidebar_result->fetch_all(MYSQLI_ASSOC);
$cat_sidebar_stmt->close();
?>



<!doctype html>
<html class="no-js" lang="">


<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Shop || BALAJI FURNITURE</title>
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
	<link rel="stylesheet" href="style1.css">
	<link rel="stylesheet" href="header.css">
	<!-- responsive css -->
	<link rel="stylesheet" href="css/responsive.css">
	<!-- modernizr js -->
	<script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
	<!-- header-start -->
	<?php include 'header.php'; ?>
	</header>

	<!-- header-end -->
	<!-- shop-2-area-start -->
	<!-- <div class="shop-2-area" id="product-list"> -->
	<section class="AboutSection">
		<div class="image-wrapper">
			<img src="img\slider\5.jpg" class="AboutwrapperImage" />
			<h1 class="aboutUs-Heading">CATEGORY</h1>
			<div class="AboutDivWrapper">
				<a class="AboutHome"> HOME </a> &nbsp/ &nbsp <a class="AboutHome">CATEGORY</a>
			</div>
		</div>
	</section>
	<!-- <div class="breadcrumb">
					<a href="index.php" title="Return to Home">
						<i class="icon-home"></i>
					</a>
					<span class="navigation-pipe">></span>
					<span class="navigation-page">
						<?php if (!empty($category_name)): ?>
							<a href="shop.php" title="All Products">FURNITURE</a>
							<span class="navigation-pipe">></span>
							<?php echo htmlspecialchars($category_name); ?>
						<?php else: ?>
							FURNITURE
						<?php endif; ?>
					</span>
				</div> -->
				<section class="shopSection">
	<div class="container">
		<div class="row">
			<div class="left-column col-sm-3 pt-5">
				<div class="left-column-block">
					<h1>Catalog</h1>
					<div class="block-content">
						<div class="content-box">
							<h3 class="content-box-heading">
								Categories
							</h3>

							<form id="categoryFilterForm" method="GET" action="shop.php#product-list">
								<ul>
									<?php foreach ($categories as $cat): ?>
										<li>
											<label>
												<input type="checkbox"
													class="category-filter"
													value="<?php echo $cat['category_name']; ?>"
													<?php echo in_array($cat['category_name'], $category_names) ? 'checked' : ''; ?>>
												<?php echo htmlspecialchars($cat['category_name']); ?>
											</label>
										</li>
									<?php endforeach; ?>
								</ul>
							</form>
							</ul>
						</div>
						<div class="content-box">
							<h3 class="content-box-heading">price</h3>
							<div class="info_widget">
								<div class="price_filter">
									<div id="slider-range"></div>
									<div class="price_slider_amount">
										<input type="text" id="amount" name="price" placeholder="Add Your Price" />
										<input type="submit" value="" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-9 pt-5">
				<div class="shop-heading">
					<h2><?php echo !empty($category_name) ? htmlspecialchars($category_name) : "All Products"; ?></h2>
					<span>There are <?php echo count($allRows); ?> products.</span>
				</div>
				<div class="category-products">
					<div class="topbar-category">
						<div class="pager-area">
							<div>
								<!-- Nav tabs -->
								<ul class="shop-tab nav">
									<li><a class="active" href="#gried_view" role="tab" data-bs-toggle="tab">
											<i class="fa fa-th-large"></i></a>
									</li>
									<li><a href="#list_view" role="tab" data-bs-toggle="tab">
											<i class="fa fa-th-list"></i></a>
									</li>
								</ul>
							</div>
						</div>

						<div class="sort-by">
							<form method="GET" action="shop.php#product-list" id="sortForm">

								<input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
								<label for="sort_by">Sort By</label>
								<select name="sort_by" id="sort_by" onchange="document.getElementById('sortForm').submit()">
									<option value="">Default</option>
									<option value="price_asc" <?php if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
									<option value="price_desc" <?php if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
									<option value="name_asc" <?php if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'name_asc') echo 'selected'; ?>>Name: A to Z</option>
									<option value="name_desc" <?php if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'name_desc') echo 'selected'; ?>>Name: Z to A</option>

								</select>
							</form>
						</div>


						<div class="show">
							<form method="GET" action="shop.php#product-list" id="limitForm">

								<!-- hidden fields to preserve filters -->
								<input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
								<input type="hidden" name="sort_by" value="<?php echo $sort_by; ?>">

								<label for="limit">Show</label>
								<select name="limit" id="limit" onchange="document.getElementById('limitForm').submit()">
									<option value="12" <?php if (isset($_GET['limit']) && $_GET['limit'] == '12') echo 'selected'; ?>>12</option>
									<option value="24" <?php if (isset($_GET['limit']) && $_GET['limit'] == '24') echo 'selected'; ?>>24</option>
								</select>
								<span>per page</span>
							</form>
						</div>


						<!-- <div class="compare">
							<a href="compare.php "> compare (<span class="compare-count"><?php echo $compare_count; ?></span>) </a>
							<i class="fa fa-angle-right"></i>
						</div> -->

					</div>
					<div class="shop-category-product">

						<div class="category-product">
							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active fade show" id="gried_view">
									<div class="row">

										<?php foreach ($allRows as $row) {

											// Get image
											$images = json_decode($row['images']);
											$firstImage = $images[0];
										?>
											<div class="col-md-4 col-sm-6 col-xs-12">
												<div class="single-product">
													<div class="product-img">
														<a href="product-details.php?id=<?php echo $row['id']; ?>">
															<img src="./admin/<?php echo $firstImage ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" />
														</a>
														<span class="new"><a href="wishlist.php?action=add&id=<?php echo $row['id']; ?>" title="Add to wishlist">
															<i class="fa fa-heart" aria-hidden="true"></i>
														</a></span>

														<div class="product-action">
															<div class="add-to-links">
																<ul>
																	<!-- <li>
																		<a href="wishlist.php?action=add&id=<?php echo $row['id']; ?>" title="Add to wishlist">
																			<i class="fa fa-heart" aria-hidden="true"></i>
																		</a>
																	</li> -->
																	<!-- <li>
									<a href="#" title="Add to compare">
										<i class="fa fa-bar-chart" aria-hidden="true"></i>
									</a>
								</li> -->
																	<!-- <li>
																		<a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
																			<i class="fa fa-bar-chart" aria-hidden="true"></i>
																		</a>
																	</li> -->

																</ul>
																<div class="AddCart">
																	<a href="shopping-cart.php?action=add&id=<?php echo $row['id']; ?>" title="Add to cart">
																			<span>add to cart</span>																
																</div>
															</div>
														</div>
													</div>
													<div class="product-content">
														<h5 class="product-name">
															<a href="#" title="<?php echo htmlspecialchars($row['product_name']); ?>">
																<?php echo htmlspecialchars($row['product_name']); ?>
															</a>
														</h5>
														<div class="reviews">
															<div class="star-content clearfix">
																<?php for ($i = 0; $i < 5; $i++) : ?>
																	<span class="star star-on"></span>
																<?php endfor; ?>
															</div>
															<div class="comment">
																<span class="reviewcount">1</span> Review(s)
															</div>
														</div>
														<div class="price-box">
															<span class="price">₹ <?php echo $row['price']; ?></span>
															<?php if (!empty($row['old_price']) && $row['old_price'] > $row['price']) { ?>
																<span class="old-price">₹ <?php echo $row['old_price']; ?></span>
															<?php } ?>
														</div>
                                                        
														<div class="stock-info">
                                                         <small class="text-muted">Stock: <?php echo $row['stock']; ?> available</small>
                                                         </div>


													</div>
												</div>
											</div>
										<?php } ?>

									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="list_view">
									<div class="list-view">
										<?php foreach ($allRows as $row):
											// Get image
											$images = json_decode($row['images'], true);
											$firstImage = is_array($images) && !empty($images) ? $images[0] : 'default.jpg';

											// Calculate price display
											$price = isset($row['price']) ? floatval($row['price']) : 0;
											$discount = isset($row['discount']) ? floatval($row['discount']) : 0;
											$old_price = $price + $discount;
										?>
											<div class="list-view-single row list-view-mar">
												<div class="col-md-4 col-sm-5">
													<div class="single-product">
														<div class="product-img">
															<a href="product-details.php?id=<?php echo $row['id']; ?>">
																<img src="./admin/<?php echo $firstImage; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" />
															</a>
															<?php if (strtotime($row['created_at']) > strtotime('-30 days')): ?>
																<span class="new">new</span>
															<?php endif; ?>
															<?php if ($discount > 0): ?>
																<span class="sale">sale</span>
															<?php endif; ?>
															<div class="product-action">
																<div class="add-to-links">
																	<div class="quick-view">
																		<a href="product-details.php?id=<?php echo $row['id']; ?>" title="Quick view">
																			<span>Quick view</span>
																		</a>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-8 col-sm-7">
													<div class="product-content">
														<h5 class="product-name">
															<a href="product-details.php?id=<?php echo $row['id']; ?>" title="<?php echo htmlspecialchars($row['product_name']); ?>">
																<?php echo htmlspecialchars($row['product_name']); ?>
															</a>
														</h5>
														<div class="reviews">
															<div class="star-content clearfix">
																<?php for ($i = 0; $i < 5; $i++) : ?>
																	<span class="star star-on"></span>
																<?php endfor; ?>
															</div>
															<div class="comment">
																<span class="reviewcount">1</span>
																Review(s)
															</div>
														</div>
														<div class="price-box">
															<span class="price">₹ <?php echo number_format($price, 2); ?></span>
															<?php if ($discount > 0): ?>
																<span class="old-price">₹ <?php echo number_format($old_price, 2); ?></span>
															<?php endif; ?>
														</div>

														<div class="stock-info">
                                                         <small class="text-muted">Stock: <?php echo $row['stock']; ?> available</small>
                                                         </div>


														<p class="product-desc">
															<?php echo htmlspecialchars(substr($row['description'] ?? 'Product description not available.', 0, 150)) . '...'; ?>
														</p>
														<div class="action">
															<ul>
																<li class="cart">
																	<a href="shopping-cart.php?action=add&id=<?php echo $row['id']; ?>" title="Add to cart">
																		<!-- <i class="fa fa-shopping-cart"></i> -->
																		<span>add to cart</span>
																	</a>
																</li>

																<li class="wishlist">
																	<a href="wishlist.php?action=add&id=<?php echo $row['id']; ?>" title="Add to wishlist">
																		<i class="fa fa-heart" aria-hidden="true"></i>
																	</a>
																</li>
																<!-- <li>
																	<a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
																		<i class="fa fa-bar-chart" aria-hidden="true"></i>
																	</a>
																</li> -->

															</ul>
														</div>
														<div class="stock-info mt-2">
    <small class="text-muted">Stock: <?php echo $row['stock']; ?> available</small>
</div>

													</div>
												</div>
											</div>
										<?php endforeach; ?>

										<!-- <div class="list-view-single row list-view-mar">
											<div class="col-md-4">
												<div class="single-product">
													<div class="product-img">
														<a href="#">
															<img src="img/tab-pro/chair.jpg" alt="" />
														</a>
														<span class="new">new</span>
														<span class="sale">sale</span>
														<div class="product-action">
															<div class="add-to-links">
																<div class="quick-view">
																	<a href="#" title="Quick view" data-bs-toggle="modal" data-target="#myModal">
																		<span>Quick view</span>
																	</a>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div> -->
											<!-- <div class="col-md-8">
												<div class="product-content">
													<h5 class="product-name">
														<a href="#" title=" Faded Short Sleeves T-shirt "> Faded Short Sleeves T-shirt </a>
													</h5>
													<div class="reviews">
														<div class="star-content clearfix">
															<span class="star"></span>
															<span class="star"></span>
															<span class="star"></span>
															<span class="star"></span>
															<span class="star"></span>
														</div>
													</div>
													<div class="price-box">
														<span class="price"> £ 61.19 </span>
													</div>
													<p class="product-desc">Printed evening dress with straight
														sleeves with black thin waist belt and ruffled linings.
													</p>
													<div class="action">
														<ul>
															<li class="cart">
																<a href="shopping-cart.php?action=add&id=<?php echo $row['id']; ?>" title="Add to cart">
																	<i class="fa fa-shopping-cart"></i>
																	<span>add to cart</span>
																</a>
															</li>

															<li class="wishlist">
																<a href="#" title="Add to wishlist">
																	<i class="fa fa-heart" aria-hidden="true"></i>
																</a>
															</li>
															<!-- <li> -->
																<!-- <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
															</li> -->

														<!-- </ul>
													</div> -->
													<!-- <span class="availability">
														<span> In stock </span>
													</span>
												</div>
											</div> -->
										
				<div class="shop-pagination">
					<div class="row">
						<div class="col-md-6 col-xs-6">
							<div class="product-count">
								Showing 1 - 12 of 13 items
							</div>
							<ul class="pagination">
								<li class="pagination-previous-bottom">
									<a href="#">
										<i class="fa fa-angle-left"></i>
									</a>
								</li>
								<li class="active current">
									<a href="#">
										1
									</a>
								</li>
								<li>
									<a href="#">
										2
									</a>
								</li>
								<li class="pagination-next-bottom">
									<a href="#">
										<i class="fa fa-angle-right"></i>
									</a>
								</li>
							</ul>
						</div>
						<div class="col-md-6 col-xs-6">

							<!-- <div class="compare">
								<a href="compare.php"> compare (<span class="compare-count"><?php echo $compare_count; ?></span>) </a>
								<i class="fa fa-angle-right"></i>
							</div> -->

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
															</section>
	<!--=====shop-2-area-end=====-->

	<!--===== brand-area-start =====-->
	<div class="brand-area">
		<div class="container">
			<div class="row">
				<div class="brands">
					<div class="brand-carousel">
						<div class="row">
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

	<!-- <footer>
			<div class="footer-area">
				<div class="footer-top">
					<div class="container">
						<div class="footer-logo">
							<a href="#">
								<img src="img/logo-footer.png" alt="" />
							</a>
						</div>
					</div>
				</div>
				<div class="footer-middle">
					<div class="container">
						<div class="row">
							<div class="col-md-9 col-sm-9 foot-mar">
								<div class="row">
									<div class="col-md-4  col-sm-4 col-xs-12">
										<h4>Shop Location</h4>
										<div class="footer-contact">
											<p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
											Duis dignissim erat ut laoreet pharetra....
											</p>
											<p class="address add">
												<span>No. 96, Jecica City, NJ 07305, New York, USA</span>
											</p>
											<p class="phone add">
												<span> +0123456789</span>
											</p>
											<p class="email add">
												<a href="#">demo@example.com</a>
											</p>
										</div>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<h4>Information</h4>
										<ul class="toggle-footer">
											<li>
												<a title="Specials" href="#">Specials</a>
											</li>
											<li>
												<a title="New products" href="#">New products</a>
											</li>
											<li>
												<a title="Best sellers" href="#">Best sellers</a>
											</li>
											<li>
												<a title="Our stores" href="#">Our stores</a>
											</li>
											<li>
                                              <a href="contact.php">CONTACT</a>
                                            </li>
											<li>
												<a title="Sitemap" href="#">Sitemap</a>
											</li>
										</ul>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<h4>My account</h4>
										<ul class="toggle-footer">
											<li>
												<a title="My orders" href="#">My orders</a>
											</li>
											<li>
												<a title="My credit slips" href="#"> My credit slips</a>
											</li>
											<li>
												<a title="My addresses" href="#">My addresses</a>
											</li>
											<li>
												<a title="My personal info" href="#">My personal info</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-sm-3">
								<div class="newsletter">
									<h4>Newsletter</h4>
									<div class="newsletter-content">
										<form action="https://htmldemo.net/vonia/vonia/method">
											<input class="newsletter-input" type="text" placeholder="Enter your e-mail" size="18" name="email">
											<button class="btn btn-default newsletter-button" type="submit">
												<span class="subscribe">Subscribe</span>
											</button>
										</form>
									</div>
								</div>
								<div class="footer-social">
								 <h3>Follow Us</h3>
									<a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
									<a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
									<a href="#"><i class="fa fa-rss" aria-hidden="true"></i></a>
									<a href="#"><i class="fa fa-youtube" aria-hidden="true"></i></a>
									<a href="#"><i class="fa fa-google-plus" aria-hidden="true"></i></a>
								</div>
							</div>
						</div>
						<div class="payment">
							<a href="#">
								<img src="img/payment.png" alt="" />
							</a>
						</div>
					</div>
				</div>
				<div class="footer-bottom">
					<div class="container">
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12 address"><p class="copyright">&copy; 2021 <strong>Vonia</strong> Made with <i class="fa fa-heart text-danger" aria-hidden="true"></i> by <a href="https://hasthemes.com/"><strong>HasThemes</strong></a>.</p>					</div>
							<div class="col-md-6 col-sm-6 col-xs-12 footer-link">
								<ul>
									<li>
										<a href="#">Customer Service</a>
									</li>
									<li>
										<a href="#">Secure payment</a>
									</li>
									<li>
										<a href="#">Term of Use</a>
									</li>
									<li>
										<a href="#">About us</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer> -->
	<!-- footer-end -->
	<!-- modal start -->
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<!-- <div class="modal-content">
				<div class="row">
					<div class="col-md-5 col-sm-5 col-xs-6">
						<div class="modal-pic" title="Printed Chiffon Dress">
							<a href="#">
								<img src="img/modal/printed-chiffon-dress.jpg" alt="" />
							</a>
							<span class="new">new</span>
							<span class="sale">sale</span>
						</div>
					</div>
					<div class="col-md-7 col-sm-7 col-xs-6">
						<h1>Faded Short Sleeves T-shirt</h1>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<p class="reference">
							<label>Reference: </label>
							<span>demo_1</span>
						</p>
						<p class="condition">
							<label>Condition: </label>
							<span>New product</span>
						</p>
						<div class="content-price">
							<p class="price-new">
								<span class="price-box">£ 16.84</span>
								<span class="price-tax"> tax incl.</span>
							</p>
						</div>
						<div class="short-description">
							<p>Faded short sleeves t-shirt with high neckline. Soft and stretchy material for a comfortable fit.
								Accessorize with a straw hat and you're ready for summer!
							</p>
						</div>
						<form action="#">
							<div class="shop-product-add">
								<div class="add-cart">
									<p class="quantity cart-plus-minus">
										<label>Quantity</label>
										<input id="quantity_wanted" class="text" type="text" value="1">

									</p>
									<div class="shop-add-cart">
										<button class="exclusive">
											<span>Add to cart</span>
										</button>
									</div>
									<ul class="usefull-links">
										<li class="sendtofriend">
											<a class="send-friend-button" href="#"> Send to a friend </a>
										</li>
										<li class="print">
											<a class="#" href="#"> Print </a>
										</li>
									</ul>
									<p class="add-wishlist">
										<a class="add-wish" href="#">
											Add to wishlist
										</a>
									</p>
								</div>
								<div class="clearfix"></div>
								<div class="size-color">
									<fieldset class="size">
										<label>Size </label>
										<div class="selector">
											<select id="group_1" class="form-control" name="group_1">
												<option title="S" selected="selected" value="1">S</option>
												<option title="M" value="2">M</option>
												<option title="L" value="3">L</option>
											</select>
										</div>
									</fieldset>
									<fieldset class="color">
										<label>Color</label>
										<div class="color-selector">
											<ul>
												<li><a class="color-1" href="#"></a></li>
												<li><a class="color-2" href="#"></a></li>
											</ul>
										</div>
									</fieldset>
								</div>
							</div>
						</form>
						<div class="clearfix"></div>
						<p class="quantity-available">
							<span>299</span>
							<span>Items</span>
						</p>
						<p class="availability-status">
							<span>In stock</span>
						</p>
						<p class="social-sharing">
							<button class="btn btn-default btn-twitter">
								<i class="icon-twitter"></i>
								Tweet
							</button>
							<button class="btn btn-default btn-facebook">
								<i class="icon-facebook"></i>
								Share
							</button>
							<button class="btn btn-default btn-google-plus">
								<i class="icon-google-plus"></i>
								Google+
							</button>
							<button class="btn btn-default btn-pinterest">
								<i class="icon-pinterest"></i>
								Pinterest
							</button>
						</p>
					</div>
				</div>
			</div>

		</div>
	</div>
	</div> -->
	<!-- modal end -->
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

	<!-- for search erase then auto show all product -->
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			const searchInput = document.querySelector('input[name="search"]');

			searchInput.addEventListener('input', function() {
				if (this.value.trim() === '') {
					// Automatically submit the form when input is empty
					document.getElementById('searchForm').submit();
				}
			});
		});
	</script>

	<!-- for compare -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		document.querySelectorAll('.category-filter').forEach(function(checkbox) {
			checkbox.addEventListener('change', function() {
				const selected = Array.from(document.querySelectorAll('.category-filter:checked'))
					.map(cb => encodeURIComponent(cb.value)) // handle spaces, commas etc.
					.join(',');

				const url = new URL(window.location.href);
				url.searchParams.delete('search');
				url.searchParams.set('category', selected);
				url.hash = 'product-list';
				window.location.href = url.toString();
			});
		});
	</script>

	<script>
		$(document).ready(function() {
			$('.add-to-compare').click(function(e) {
				e.preventDefault();

				var productId = $(this).data('id'); // Get product ID from data-id
				if (!productId) {
					alert('Product ID missing!');
					return;
				}

				$.ajax({
					url: 'compare.php',
					method: 'POST',
					data: {
						product_id: productId
					},
					success: function(response) {
						try {
							var res = JSON.parse(response);
							if (res.status === 'success') {
								$('.compare-count').text(res.count); // Update count
							} else {
								alert(res.message);
							}
						} catch (err) {
							console.error('Invalid JSON:', response);
						}
					},
					error: function(xhr, status, error) {
						console.error('AJAX Error:', error);
					}
				});
			});
		});
	</script>


	<?php include 'footer.php'; ?>
</body>


</html>