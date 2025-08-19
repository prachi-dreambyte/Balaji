<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

include 'connect.php';

$category_name = 'CATEGORY';
$category_link = '#';

// 🔹 Use account_type from session if available, otherwise fetch from DB
$user_account_type = null;
if (!empty($_SESSION['account_type'])) {
	$user_account_type = strtolower(trim($_SESSION['account_type']));
} elseif (!empty($_SESSION['user_id'])) {
	$user_id = (int) $_SESSION['user_id']; // cast to int for safety
	$stmt = $conn->prepare("SELECT account_type FROM signup WHERE id = ? LIMIT 1");
	if ($stmt) {
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($row = $result->fetch_assoc()) {
			$_SESSION['account_type'] = $row['account_type'];
			$user_account_type = strtolower(trim($row['account_type']));
		}
		$stmt->close();
	}
}

if (!empty($product['category_id'])) {
	$cat_id = (int) $product['category_id'];

	// Get category name from DB
	$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ? LIMIT 1");
	$stmt->bind_param('i', $cat_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result && $row = $result->fetch_assoc()) {
		$category_name = $row['name'];
		$category_link = "shop.php?category_id=" . $cat_id; // adjust to your category page
	}
	$stmt->close();
}
// If product already contains category name (alternate schema)
elseif (!empty($product) && !empty($product['category_name'])) {
	$category_name = $product['category_name'];
	if (!empty($product['category_id'])) {
		$category_link = 'shop.php?category_id=' . (int) $product['category_id'];
	}
}


// -------------------------
// Fetch all variants for this product
// Initialize product and other arrays
$product = [
	'id' => 0,
	'product_name' => '',
	'tag_number' => '',
	'price' => 0.0,
	'discount' => 0.0,
	'short_description' => '',
	'stock' => 0,
	'brand' => '',
	'weight' => '',
	'size' => '',
	'category' => '',
	'tags' => '',
	'images' => '',
	'main_product_colour' => '',
];

$images = [];
$related_products = [];
$variants = [];

// Get product ID safely from GET
$product_id = intval($_GET['id'] ?? 0);


if ($product_id > 0) {
	// Fetch main product details from products table
	$stmt = $conn->prepare("
        SELECT id, images, product_name, price, discount, stock, variants
        FROM products
        WHERE id = ?
    ");
	$stmt->bind_param("i", $product_id);
	$stmt->execute();
	$product_result = $stmt->get_result();

	if ($product_row = $product_result->fetch_assoc()) {
		$product['id'] = $product_row['id'];
		$product['images'] = $product_row['images'] ?? '';
		$product['product_name'] = $product_row['product_name'] ?? '';
		$product['price'] = floatval($product_row['price'] ?? 0);
		$product['discount'] = floatval($product_row['discount'] ?? 0);
		$product['stock'] = intval($product_row['stock'] ?? 0);
		$product['variants'] = intval($product_row['variants'] ?? 0);

		// Convert images string into an array (comma separated)
		$images = !empty($product['images']) ? array_map('trim', explode(',', $product['images'])) : [];
	}
	$stmt->close();

	// Fetch variants from variants table for this product
	$stmt = $conn->prepare("SELECT * FROM variants WHERE product_id = ?");
	$stmt->bind_param("i", $product_id);
	$stmt->execute();
	$result = $stmt->get_result();

	while ($row = $result->fetch_assoc()) {
		$variants[] = $row;
	}


	$stmt->close();

	// Set main product colour using first variant's Main_Product_Colour or fallback to color or empty string
	if (!empty($variants)) {
		$product['main_product_colour'] = $variants[0]['Main_Product_Colour'] ?? $variants[0]['color'] ?? '';
	} else {
		$product['main_product_colour'] = '';
	}
} else {
	error_log("No valid product ID found while fetching product and variants.");
}



if (isset($_GET['id'])) {

	$products = null;
	$product_source = '';
	$related_products = [];
	// 1. First, try to fetch from `products` table
	$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
	$stmt->bind_param("i", $product_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();


	if ($row) {
		$products = $row;
		$product_source = 'products';

		// Decode images
		$images = json_decode($products['images'], true);
		if (!is_array($images))
			$images = [];

		// Fetch related products from `products` table
		$rel_stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 8");
		$rel_stmt->bind_param("si", $products['category'], $product_id);
		$rel_stmt->execute();
		$rel_result = $rel_stmt->get_result();
		while ($rel = $rel_result->fetch_assoc()) {
			$related_products[] = $rel;
		}
		$rel_stmt->close();
	} else {

		// 2. If not found in products, check in `home_daily_deal` table
		$stmt = $conn->prepare("SELECT * FROM home_daily_deal WHERE id = ?");
		$stmt->bind_param("i", $product_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();

		if ($row) {

			$products = $row;
			$product_source = 'home_daily_deal';
			// Decode images
			$images = json_decode($products['images'], true);
			if (!is_array($images))
				$images = [];

			// Fetch related products from `home_daily_deal` table
			$rel_stmt = $conn->prepare("SELECT * FROM home_daily_deal WHERE category = ? AND id != ? LIMIT 8");
			$rel_stmt->bind_param("si", $products['category'], $product_id);
			$rel_stmt->execute();
			$rel_result = $rel_stmt->get_result();
			while ($rel = $rel_result->fetch_assoc()) {
				$related_products[] = $rel;
			}
			$rel_stmt->close();
		}
	}
	$stmt->close();
	// If product not found in either table, redirect to 404
	if (!$products) {
		header("Location: 404.php");
		exit;
	}
}

// Parse sizes
$sizes = array_filter(array_map('trim', explode(',', $product['size'])));
// Parse tags
$tags = array_filter(array_map('trim', explode(',', $product['tags'] ?? '')));
// Debug: Show product source (can be removed in production)
if (isset($_GET['debug']) && $_GET['debug'] == 1) {
	echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
	echo "<strong>Debug Info:</strong> Product loaded from: " . ($product_source ?: 'Not found') . " table";
	echo "</div>";
}

$varaints_id = $product['variants'];
if ($varaints_id > 0) {
	try {
		$variant_stmt = $conn->prepare("SELECT * FROM products WHERE variants = ?");
		$variant_stmt->bind_param("i", $varaints_id);
		$variant_stmt->execute();
		$variant_result = $variant_stmt->get_result();
	} catch (Exception $e) {
		error_log("Failed to fetch the product: " . $e->getMessage());
	}
}

?>
<!doctype html>
<html class="no-js" lang="">

<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Product Details || Vonia</title>
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
		#writeReviewForm {
			background-color: #f9f9f9;
			border: 1px solid #ddd;
			border-radius: 8px;
			padding: 20px;
			margin-bottom: 30px;
			box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
		}

		#writeReviewForm textarea,
		#writeReviewForm input[type="file"] {
			width: 100%;
			padding: 10px;
			border-radius: 4px;
			border: 1px solid #ccc;
			margin-top: 8px;
			margin-bottom: 16px;
		}

		#star-rating {
			direction: rtl;
			font-size: 26px;
			display: flex;
			justify-content: flex-start;
			gap: 4px;
		}

		#star-rating input[type="radio"] {
			display: none;
		}

		#star-rating label {
			color: #ccc;
			cursor: pointer;
			transition: 0.3s;
		}

		#star-rating input:checked~label,
		#star-rating label:hover,
		#star-rating label:hover~label {
			color: #ff9800;
		}

		.review-block {
			border-bottom: 1px solid #e0e0e0;
			padding: 15px 0;
		}

		.review-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			font-weight: bold;
		}

		.review-rating {
			color: #f39c12;
		}

		.review-text {
			font-size: 15px;
			margin-top: 10px;
		}

		.review-images {
			display: flex;
			gap: 8px;
			margin-top: 10px;
			flex-wrap: wrap;
		}

		.review-images img {
			width: 90px;
			height: 90px;
			object-fit: cover;
			border-radius: 4px;
			border: 1px solid #ccc;
		}

		.review-date {
			font-size: 13px;
			color: #777;
			margin-top: 6px;
		}

		.write-review-toggle {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 20px;
		}

		.write-review-toggle h3 {
			margin: 0;
			font-size: 20px;
		}

		.review-btn {
			background: #111;
			color: #fff;
			padding: 8px 14px;
			border: none;
			border-radius: 4px;
			font-size: 14px;
			cursor: pointer;
		}

		.review-btn:hover {
			background: #333;
		}

		@media screen and (max-width: 768px) {
			#star-rating {
				font-size: 20px;
			}

			.review-images img {
				width: 70px;
				height: 70px;
			}
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
	<!-- product-details-area-start -->
	<div class="shop-1-area">
		<div class="container-fluid">
			<div class="breadcrumb">
				<a href="index.php" title="Return to Home">
					<i class="icon-home"></i>
				</a>
				<span class="navigation-pipe">></span>
				<span class="navigation-page">
					<a href="<?php echo htmlspecialchars($category_link); ?>"
						title="<?php echo htmlspecialchars($product['category']); ?>">
						<span><?php echo htmlspecialchars($product['category']); ?></span>
					</a>
					<span class="navigation-pipe nav-pipe-2">></span>
					<?php echo htmlspecialchars($product['product_name'] ?? '---'); ?>
				</span>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-md-5 col-sm-6 col-12 ">
					<style>
						.zoom-container {
							width: 100%;
							padding-top: 100%;
							/* 1:1 Aspect Ratio */
							position: relative;
							overflow: hidden;
							border: 1px solid #ddd;
						}

						.zoom-image {
							position: absolute;
							top: 0;
							left: 0;
							right: 0;
							bottom: 0;
							background-size: cover;
							background-repeat: no-repeat;
							background-position: center;
							transition: background-size 0.3s ease;
							cursor: zoom-in;
						}

						/* General Page Styling */
						body {
							font-family: 'Open Sans', sans-serif;
							background-color: #f8f9fa;
						}

						/* Product Title */
						.shop-content h1 {
							font-size: 28px;
							font-weight: 700;
							margin-bottom: 10px;
							color: #222;
						}

						/* Breadcrumb */
						.breadcrumb {
							/* background: linear-gradient(45deg, #ff7e5f, #feb47b); */
							padding: 45px 30px;
							border-radius: 8px;
							font-size: 18px;
							font-weight: 600;
							display: flex;
							align-items: center;
							flex-wrap: wrap;
							gap: 8px;
							box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
						}

						.breadcrumb a {
							color: black;
							text-decoration: none;
							transition: all 0.3s ease;
						}

						.breadcrumb a:hover {
							text-decoration: underline;
						}

						.breadcrumb .navigation-pipe {
							color: rgba(24, 18, 18, 0.8);
							font-weight: 400;
						}


						.breadcrumb .navigation-page a span {
							color: black;
						}

						.breadcrumb .navigation-page {
							color: black;
						}

						.breadcrumb i.icon-home {
							font-size: 20px;
							margin-right: 4px;
						}

						/* Price Section */
						.content-price .price-new {
							font-size: 22px;
							font-weight: bold;
							color: #e63946;
						}

						.content-price .old-price {
							font-size: 16px;
							margin-top: -8px;
							display: block;
						}

						.reduction-percent {
							font-size: 14px;
							font-weight: 600;
						}

						/* Add to Cart Button */
						.cart-btn {
							border-radius: 6px !important;
							padding: 10px 18px !important;
							font-size: 16px;
							font-weight: 600;
							background: linear-gradient(45deg, #C06B81, #C06B81);
							transition: all 0.3s ease;
						}

						.cart-btn:hover {
							transform: translateY(-2px);
							box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
						}

						/* Wishlist Icon */
						.add-wishlist .fa-heart {
							font-size: 20px;
							color: #C06B81;
							transition: 0.3s;
						}

						.add-wishlist .fa-heart:hover {
							transform: scale(1.2);
							color: #C06B81;
						}

						/* Product Images */
						.zoom-container {
							border-radius: 8px;
							overflow: hidden;
							box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
						}

						/* Review Section */
						.review-block {
							background: #fff;
							padding: 15px;
							border-radius: 8px;
							box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
							margin-bottom: 15px;
						}

						.review-header {
							font-size: 16px;
						}

						.review-rating span {
							font-size: 18px;
						}

						/* Tabs */
						.feature-tab-area .tabs a {
							padding: 10px 20px;
							font-weight: 600;
							border-radius: 4px;
						}

						.feature-tab-area .tabs a.active {
							background: #007bff;
							color: white;
						}

						/* Mobile Adjustments */
						@media screen and (max-width: 768px) {
							.shop-content h1 {
								font-size: 22px;
							}

							.cart-btn {
								width: 100%;
								justify-content: center;
							}
						}
					</style>

					<script>
						document.addEventListener("DOMContentLoaded", function() {
							const zoomImages = document.querySelectorAll(".zoom-image");

							zoomImages.forEach(image => {
								image.addEventListener("mousemove", function(e) {
									const rect = image.getBoundingClientRect();
									const x = ((e.clientX - rect.left) / rect.width) * 100;
									const y = ((e.clientY - rect.top) / rect.height) * 100;
									image.style.backgroundPosition = `${x}% ${y}%`;
								});

								image.addEventListener("mouseenter", function() {
									image.style.backgroundSize = "200%";
								});

								image.addEventListener("mouseleave", function() {
									image.style.backgroundSize = "cover";
									image.style.backgroundPosition = "center";
								});
							});
						});
					</script>

					<div class="picture-tab">
						<ul class="pic-tabs nav" role="tablist">
							<?php if (!empty($images)) {

								foreach ($images as $idx => $img): ?>
											<li>
												<a class="<?php echo $idx === 0 ? 'active' : ''; ?>"
													href="#picture-<?php echo $idx + 1; ?>" data-bs-toggle="tab">
													<img src="admin/<?php echo htmlspecialchars($img); ?>" alt="" />
												</a>
											</li>
									<?php endforeach;
							} else { ?>
									<li>
										<a class="active" href="#picture-1" data-bs-toggle="tab">
											<img src="img/no-image.png" alt="No image" />
										</a>
									</li>
							<?php } ?>
						</ul>

						<div class="tab-content">
							<?php if (!empty($images)) {
								foreach ($images as $idx => $img): ?>
											<div class="tab-pane fade<?php echo $idx === 0 ? ' show active' : ''; ?>"
												id="picture-<?php echo $idx + 1; ?>">
												<div class="single-product">
													<div class="product-img">
														<div class="zoom-container">
															<div class="zoom-image"
																style="background-image: url('admin/<?php echo htmlspecialchars($img); ?>');">
															</div>
														</div>
													</div>
												</div>
											</div>
									<?php endforeach;
							} else { ?>
									<div class="tab-pane fade show active" id="picture-1">
										<div class="single-product">
											<div class="product-img">
												<div class="zoom-container">
													<div class="zoom-image"
														style="background-image: url('img/no-image.png');"></div>
												</div>
											</div>
										</div>
									</div>
							<?php } ?>
						</div>
					</div>

				</div>
				<div class="col-md-7 col-sm-6 col-12 shop-content">
					<div class="abc">
						<h1><?php echo $product['product_name'] !== '' ? htmlspecialchars($product['product_name']) : '---'; ?>
						</h1>
						<!-- <p class="reference"><label>Reference: </label>
						<span><?php echo $product['tag_number'] !== '' ? htmlspecialchars($product['tag_number']) : '---'; ?></span>
					</p> -->
						<p class="condition"><label>Condition: </label></p>
						<div class="content-price">
							<?php
							$price = isset($product['price']) ? floatval($product['price']) : 0;
							$discount = isset($product['discount']) ? floatval($product['discount']) : 0;
							$corporate_discount = isset($product['corporate_discount']) ? floatval($product['corporate_discount']) : 0;

							// Old static price before any discount
							$old_price = $price;

							// Apply normal discount
							$final_price = $price - $discount;

							// Apply corporate discount if user is commercial
							if (!empty($user_account_type) && $user_account_type === 'commercial' && $corporate_discount > 0) {
								$final_price -= $corporate_discount;
							}

							// Ensure price doesn't go below zero
							$final_price = max($final_price, 0);

							// Calculate total discount percentage
							$total_discount = $old_price - $final_price;
							$discount_percent = ($old_price > 0 && $total_discount > 0) ? ($total_discount / $old_price) * 100 : 0;
							?>

							<p class="price-new">
								<span class="price-box">₹<?php echo number_format($final_price, 2); ?></span>
								<span class="price-tax"> tax incl.</span>
							</p>

							<?php if ($total_discount > 0): ?>
									<p class="old-price" style="text-decoration:line-through;color:#999;">
										₹<?php echo number_format($old_price, 2); ?> <span class="price-tax"> tax incl.</span>
									</p>
							<?php endif; ?>

							<?php if ($discount_percent > 0): ?>
									<p class="reduction-percent" style="color:green;">
										-<?php echo round($discount_percent); ?>% OFF
									</p>
							<?php endif; ?>

							<?php if (!empty($user_account_type) && $user_account_type === 'commercial' && $corporate_discount > 0): ?>
									<p style="color:green; font-weight:bold;">Special Commercial Price Applied</p>
							<?php endif; ?>
						</div>


						// Show main product colour and variants if any

						<div class="product-variants">
							<h4>Select Colour:</h4>
							<div class="variant-options" style="display: flex; gap: 10px; flex-wrap: wrap;">



								<!-- Variants list -->
								<?php if (!empty($variant_result)): ?>
										<?php while ($variant = $variant_result->fetch_assoc()): ?>
												<a href="product-details.php?id=<?= urlencode($variant['id']); ?>" style="text-decoration:none;">
												<div class="variant-item"
											
													style="cursor:pointer; text-align:center; border:1px solid #ddd; padding:5px; border-radius:6px; width:70px;">

													<?php
													$firstImag = null;
													if (!empty($variant['images'])) {
														$decodedImags = json_decode($variant['images'], true);
														if (is_array($decodedImags) && !empty($decodedImags)) {
															$firstImag = $decodedImags[0];
														}
													}
													?>

													<?php if ($firstImag): ?>
															<img src="<?= (strpos($firstImag, 'admin/') === 0) ? htmlspecialchars($firstImag) : 'admin/' . htmlspecialchars($firstImag); ?>"
																alt="<?= htmlspecialchars($variant['colour'] ?? ''); ?>"
																style="width:60px; height:60px; object-fit:cover; border-radius:4px;">
													<?php else: ?>
															<div style="width:60px; height:60px; background:#eee; border-radius:4px; 
						display:flex; align-items:center; justify-content:center; 
						font-size:10px; color:#888;">
																No Image
															</div>
													<?php endif; ?>

													<div style="font-size:13px; margin-top:5px;">
														<?= htmlspecialchars($variant['colour'] ?? ''); ?>
													</div>
												</div>
												</a>
										<?php endwhile; ?>

								<?php endif; ?>

							</div>
						</div>






						<div class="short-description">
							<p><?php echo $product['short_description'] !== '' ? nl2br(htmlspecialchars($product['short_description'])) : '---'; ?>
							</p>
						</div>
						<form action="#">
							<div class="shop-product-add">
								<div class="add-cart">
									<p class="quantity cart-plus-minus">
										<label for="quantity_wanted">Quantity</label>
										<input id="quantity_wanted" class="text" type="number" name="quantity" value="1"
											min="1"
											max="<?php echo $product['stock'] > 0 ? (int) $product['stock'] : 1; ?>"
											<?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
									</p>

									<div class="cart-wishlist-container">
										<div class="shop-add-cart">
											<?php if ($product['stock'] <= 0): ?>
													<button type="button" class="cart-btn disabled"><i
															class="fa fa-times-circle"></i> Out of Stock</button>
											<?php else: ?>
													<a href="shopping-cart.php?action=add&id=<?php echo $product['id']; ?>&qty=1"
														class="cart-btn" onclick="return addToCart(<?php echo $product['id']; ?> , );">
														<i class="fa fa-shopping-cart"></i> Add to Cart
													</a>
											<?php endif; ?>
										</div>

										<div class="wishlist-btn">
											<a class="add-wish" href="wishlist.php?action=add&id=<?= $id ?>" title="Add to wishlist">
												<i class="fa fa-heart" aria-hidden="true"></i>
											</a>
										</div>
									</div>



								</div>
							</div>
						</form>
						<?php
						// Make sure $id is set like this before this block
						$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
						?>




						<div class="size-color">
							<!-- <fieldset class="size">
							<label>Size </label>
							<div class="selector">
								<select id="group_1" class="form-control" name="group_1">
									<?php if (!empty($sizes)) {
										foreach ($sizes as $size): ?>
											<option value="<?php echo htmlspecialchars($size); ?>">
												<?php echo htmlspecialchars($size); ?></option>
										<?php endforeach;
									} else { ?>
										<option value="">---</option>
									<?php } ?>
								</select>
							</div>
						</fieldset> -->
							<!-- </div> -->
							<p class="quantity-available"><span><?php echo htmlspecialchars($product['stock']); ?></span>
								<span>Items</span>
							</p>
							<p class="availability-status">
								<span><?php echo ($product['stock'] > 0) ? 'In stock' : 'Out of stock'; ?></span>
							</p>
							<?php if (!empty($tags)): ?>
									<p class="product-tags"><strong>Tags:</strong>
										<?php echo implode(', ', array_map('htmlspecialchars', $tags)); ?></p><?php endif; ?>
							<?php if ($product['brand']): ?>
									<p class="product-brand"><strong>Brand:</strong>
										<?php echo htmlspecialchars($product['brand']); ?></p><?php endif; ?>
							<?php if ($product['weight']): ?>
									<p class="product-weight"><strong>Weight:</strong>
										<?php echo htmlspecialchars($product['weight']); ?></p><?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<style>
			.abc {
				padding-left: 10px !important;
			}


			.cart-btn {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				padding: 10px 18px;
				background-color: #000;
				color: #fff;
				text-decoration: none;
				border: none;
				border-radius: 2px;
				font-size: 15px;
				font-weight: 500;
				transition: all 0.2s ease;
				box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
				cursor: pointer;
			}

			.cart-btn:hover {
				transform: translateY(-2px);
				color: #ffffff;
			}

			.cart-btn.disabled {
				background-color: #888;
				color: #eee;
				cursor: not-allowed;
				pointer-events: none;
			}

			@media screen and (max-width: 767px) {
				.shop-content {
					margin-top: 20px;
				}

				.picture-tab {
					text-align: center;
				}
			}
		</style>

		<!-- product-details-area-end -->


		<!-- product-details-tab-end -->
		<div class="shop-1-product-tab">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="feature-tab-area">
							<!-- Nav tabs -->
							<ul class="tabs nav mb-4" role="tablist" style="color: #000; text-align: center;">
								<li><a class="active" href="#moreinfo" aria-controls="moreinfo" role="tab"
										data-bs-toggle="tab">more info</a></li>
								<li><a href="#datasheet" aria-controls="datasheet" role="tab" data-bs-toggle="tab">data
										sheet</a></li>
								<li><a href="#reviews" aria-controls="reviews" role="tab"
										data-bs-toggle="tab">reviews</a></li>
							</ul>
							<div class="tab-content">
								<!-- More Info Tab -->
								<div role="tabpanel" class="tab-pane active fade show" id="moreinfo">
									<div class="tab-box">
										<div class="more-info">
											<p><?php echo $products['description'] !== '' ? nl2br(htmlspecialchars($products['description'])) : '---'; ?>
											</p>
										</div>
									</div>
								</div>
								<!-- Data Sheet Tab -->

								<style>
									.feature-tab-area .tabs {
	display: flex;
	justify-content: center;
	border-bottom: none;
	margin-bottom: 25px;
	gap: 12px;
	flex-wrap: wrap;
}

.feature-tab-area .tabs li {
	list-style: none;
}

.feature-tab-area .tabs a {
	display: block;
	padding: 14px 28px !important;
	font-size: 15px;
	font-weight: 600;
	color: #444;
	background: linear-gradient(145deg, #f8f9fa, #ffffff);
	border-radius: 30px;
	box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
	transition: all 0.3s ease;
	text-transform: capitalize;
}

.feature-tab-area .tabs a:hover {
	background: linear-gradient(145deg, #f0f1f2, #ffffff);
	color: #000;
	transform: translateY(-2px);
	box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
}

.feature-tab-area .tabs a.active {
	background: linear-gradient(145deg, #c06b81, #a75669);
	color: #fff;
	box-shadow: 0 4px 14px rgba(192, 107, 129, 0.4);
	transform: translateY(-2px);
}

/* ---------- Tab Content Box ---------- */
.tab-box {
	background: #fff;
	padding: 25px;
	border-radius: 12px;
	box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
	animation: fadeIn 0.4s ease;
}

/* ---------- Specs Grid ---------- */
.specs-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
	gap: 18px;
}

.spec-item {
	background: linear-gradient(145deg, #fdfdfd, #f7f8f9);
	border: 1px solid #eee;
	border-radius: 10px;
	padding: 14px 18px;
	box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
	transition: all 0.3s ease;
}

.spec-item:hover {
	background: linear-gradient(145deg, #ffffff, #f5f5f5);
	box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
	transform: translateY(-3px);
}

.spec-label {
	font-weight: 600;
	font-size: 14px;
	color: #555;
	margin-bottom: 5px;
	text-transform: capitalize;
}

.spec-value {
	font-size: 15px;
	color: #222;
}

/* ---------- Animation ---------- */
@keyframes fadeIn {
	from { opacity: 0; transform: translateY(10px); }
	to { opacity: 1; transform: translateY(0); }
}
								</style>
								<div role="tabpanel" class="tab-pane fade" id="datasheet">
									<div class="tab-box">
									<div class="specs-grid">
										<?php if ($products['total_height']): ?>
													<div class="spec-item">
														<div class="spec-label">Total Height</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['total_height']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['total_width']): ?>
													<div class="spec-item">
														<div class="spec-label">Total Width</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['total_width']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['material']): ?>
													<div class="spec-item">
														<div class="spec-label">Material</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['material']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['seat_height']): ?>
													<div class="spec-item">
														<div class="spec-label">Seat Height</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['seat_height']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['seat_thickness']): ?>
													<div class="spec-item">
														<div class="spec-label">Seat Thickness</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['seat_thickness']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['seat_depth']): ?>
													<div class="spec-item">
														<div class="spec-label">Seat Depth</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['seat_depth']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['seat_material_type']): ?>
													<div class="spec-item">
														<div class="spec-label">Seat Material</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['seat_material_type']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['backrest_height_from_seat']): ?>
													<div class="spec-item">
														<div class="spec-label">Backrest Height</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['backrest_height_from_seat']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['backrest_material_type']): ?>
													<div class="spec-item">
														<div class="spec-label">Backrest Material</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['backrest_material_type']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['pedestal_base']): ?>
													<div class="spec-item">
														<div class="spec-label">Pedestal Base</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['pedestal_base']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['seat_height_adjusting_range']): ?>
													<div class="spec-item">
														<div class="spec-label">Height Adjustment Range</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['seat_height_adjusting_range']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['handle_type']): ?>
													<div class="spec-item">
														<div class="spec-label">Handle Type</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['handle_type']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['wheel_type']): ?>
													<div class="spec-item">
														<div class="spec-label">Wheel Type</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['wheel_type']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['mechanical_system_type']): ?>
													<div class="spec-item">
														<div class="spec-label">Mechanical System</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['mechanical_system_type']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['color_available']): ?>
													<div class="spec-item">
														<div class="spec-label">Available Colors</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['color_available']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['product_weight']): ?>
													<div class="spec-item">
														<div class="spec-label">Products Weight</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['product_weight']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['backrest_size']): ?>
													<div class="spec-item">
														<div class="spec-label">Backrest Size</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['backrest_size']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['adjuster_size']): ?>
													<div class="spec-item">
														<div class="spec-label">Adjuster Size</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['adjuster_size']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['guarantee']): ?>
													<div class="spec-item">
														<div class="spec-label">Guarantee</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['guarantee']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['chair_arms']): ?>
													<div class="spec-item">
														<div class="spec-label">Chair Arms</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['chair_arms']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['table_top_size']): ?>
													<div class="spec-item">
														<div class="spec-label">Table Top Size</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['table_top_size']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['sitting_capacity']): ?>
													<div class="spec-item">
														<div class="spec-label">Sitting Capacity</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['sitting_capacity']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['no_of_top']): ?>
													<div class="spec-item">
														<div class="spec-label">Number of Tops</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['no_of_top']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['table_type']): ?>
													<div class="spec-item">
														<div class="spec-label">Table Type</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['table_type']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['shape']): ?>
													<div class="spec-item">
														<div class="spec-label">Shape</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['shape']); ?></div>
													</div>
										<?php endif; ?>
										
										<?php if ($products['wheels']): ?>
													<div class="spec-item">
														<div class="spec-label">Wheels</div>
														<div class="spec-value"><?php echo htmlspecialchars($products['wheels']); ?></div>
													</div>
										<?php endif; ?>
									</div>
								</div>
								</div>
								<!-- ⭐ REVIEW SECTION START -->
								<div role="tabpanel" class="tab-pane fade in " id="reviews">
									<div class="tab-box">
										<!-- Header and Toggle -->
										<div class="write-review-toggle">
											<h3>Product Reviews</h3>
											<button onclick="toggleReviewForm()" class="review-btn">Write a Review</button>
										</div>
										<hr style="margin-bottom: 18px;">
										<!-- Write Review Form -->
										<div id="writeReviewForm" style="display: none;">
											<form action="submit-review.php" method="POST" enctype="multipart/form-data">
												<input type="hidden" name="product_id" value="<?php echo $products['id']; ?>">
												<label style="font-weight: 500;">Your Rating:</label>
												<div id="star-rating" style="font-size: 38px; margin-bottom: 10px;">
													<?php for ($i = 5; $i >= 1; $i--): ?>
															<input type="radio" id="star<?php echo $i; ?>" name="rating"
																value="<?php echo $i; ?>" required>
															<label for="star<?php echo $i; ?>">★</label>
													<?php endfor; ?>
												</div>
												<label style="font-weight: 500;">Your Review:</label>
												<textarea name="review_text" rows="4" required
													placeholder="Share your experience..."></textarea>
												<label style="font-weight: 500;">Upload Product Images (optional):</label>
												<input type="file" name="review_image[]" multiple accept="image/*"
													id="reviewImageInput" onchange="previewReviewImages(this)">
												<div id="reviewImagePreview"
													style="display: flex; gap: 8px; margin: 10px 0 18px 0;"></div>
												<button type="submit" class="review-btn">Submit Review</button>
											</form>
										</div>
										<!-- Display Reviews -->
										<div id="reviewList">
											<?php
											$productId = $products['id'];
											$query = "SELECT * FROM reviews WHERE product_id = $productId ORDER BY created_at DESC LIMIT 3";
											$result = mysqli_query($conn, $query);
											$totalReviews = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM reviews WHERE product_id = $productId"));
											$shownReviews = 0;
											if (mysqli_num_rows($result) > 0):
												while ($review = mysqli_fetch_assoc($result)):
													$shownReviews++;
													$images = json_decode($review['image_path'], true);
													?>
															<div class="review-block"
																style="border-bottom: 1.5px solid #e0e0e0; padding: 18px 0 12px 0;">
																<div class="review-header"
																	style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
																	<div
																		style="font-weight: 600; font-size: 17px; color: #222; letter-spacing: 0.2px;">
																		<i class="fa fa-user-circle"
																			style="font-size: 20px; color: #888; margin-right: 4px;"></i>
																		<?php echo htmlspecialchars($review['user_name']); ?>
																	</div>
																	<div class="review-rating"
																		style="font-size: 22px; color: #f39c12; margin-left: 10px;">
																		<?php
																		for ($i = 1; $i <= 5; $i++) {
																			echo '<span style="color:' . ($i <= $review['rating'] ? '#f39c12' : '#ddd') . ';">★</span>';
																		}
																		?>
																	</div>
																</div>
																<?php if (!empty($images) && is_array($images)): ?>
																		<div class="review-images" style="display: flex; gap: 8px; margin-bottom: 8px;">
																			<?php foreach ($images as $img):
																				$imgPath = (strpos($img, 'uploads/') === 0) ? $img : ('uploads/review-images/' . ltrim($img, '/'));
																				?>
																					<img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Review Image"
																						style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc;">
																			<?php endforeach; ?>
																		</div>
																<?php endif; ?>
																<div class="review-text"
																	style="font-size: 15.5px; margin-top: 2px; color: #222;">
																	<?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
																</div>
																<div class="review-date" style="font-size: 13px; color: #777; margin-top: 6px;">
																	<?php echo date("d M Y, h:i A", strtotime($review['created_at'])); ?>
																</div>
															</div>
													<?php endwhile;
											else: ?>
													<p>No reviews yet. Be the first to review this product!</p>
											<?php endif; ?>
										</div>
										<?php if ($totalReviews > 3): ?>
												<div style="text-align: center; margin-top: 20px;">
													<button class="review-btn" id="loadMoreReviewsBtn">View More</button>
												</div>
												<script>
													let reviewOffset = 3;
													document.getElementById('loadMoreReviewsBtn').addEventListener('click', function() {
														const btn = this;
														btn.disabled = true;
														btn.textContent = 'Loading...';
														const xhr = new XMLHttpRequest();
														xhr.open('GET', 'load-more-reviews.php?product_id=<?php echo $productId; ?>&offset=' + reviewOffset + '&limit=5', true);
														xhr.onload = function() {
															if (xhr.status === 200) {
																const newReviews = xhr.responseText;
																document.getElementById('reviewList').insertAdjacentHTML('beforeend', newReviews);
																reviewOffset += 5;
																if (reviewOffset >= <?php echo $totalReviews; ?>) {
																	btn.style.display = 'none';
																} else {
																	btn.disabled = false;
																	btn.textContent = 'View More';
																}
															}
														};
														xhr.send();
													});
												</script>
										<?php endif; ?>
										<script>
											function previewReviewImages(input) {
												const preview = document.getElementById('reviewImagePreview');
												preview.innerHTML = '';
												if (input.files) {
													Array.from(input.files).forEach(file => {
														if (file.type.startsWith('image/')) {
															const reader = new FileReader();
															reader.onload = function(e) {
																const img = document.createElement('img');
																img.src = e.target.result;
																img.style.width = '60px';
																img.style.height = '60px';
																img.style.objectFit = 'cover';
																img.style.borderRadius = '4px';
																img.style.border = '1px solid #ccc';
																img.style.marginRight = '4px';
																preview.appendChild(img);
															};
															reader.readAsDataURL(file);
														}
													});
												}
											}

											function toggleReviewForm() {
												const form = document.getElementById('writeReviewForm');
												form.style.display = (form.style.display === 'none') ? 'block' : 'none';
											}
										</script>
										<style>
											#star-rating input[type="radio"] {
												display: none;
											}

											#star-rating label {
												color: #ccc;
												cursor: pointer;
												font-size: 38px;
												transition: 0.3s;
											}

											#star-rating input:checked~label,
											#star-rating label:hover,
											#star-rating label:hover~label {
												color: #ff9800;
											}
										</style>

									</div>
								</div>
								<!-- ⭐ REVIEW SECTION END -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- product-details-accessories-start -->
		<!-- product-details-accessories-end -->
		<!-- product-details-other-product-start -->
		<div class="other-product-area">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="product-title">
							<h2>
								<span>Other products in the same category:</span>
							</h2>
						</div>
						<div class="owl-carousel-space">
							<div class="row">
								<div class="accessories">
									<div class="accessories-carousel owl-carousel owl-theme">
										<?php if (!empty($related_products)): ?>
												<?php foreach ($related_products as $rel):
													$rel_images = json_decode($rel['images'], true);
													$rel_img = (!empty($rel_images) && !empty($rel_images[0])) ? 'admin/' . $rel_images[0] : 'img/no-image.png';

													// Calculate price display
													$rel_price = isset($rel['price']) ? floatval($rel['price']) : 0;
													$rel_discount = isset($rel['discount']) ? floatval($rel['discount']) : 0;
													$rel_old_price = $rel_price + $rel_discount;
													?>
														<div class="item">
															<div class="single-product">
																<div class="product-img">
																	<a href="product-details.php?id=<?php echo $rel['id']; ?>">
																		<img src="<?php echo $rel_img; ?>"
																			alt="<?php echo htmlspecialchars($rel['product_name']); ?>" />
																	</a>
																	<!-- <?php if ($rel_discount > 0): ?>
															<span class="badge-sale">Sale</span>
														<?php endif; ?> -->
																</div>
																<div class="product-content">
																	<h5 class="product-name">
																		<a href="product-details.php?id=<?php echo $rel['id']; ?>"
																			title="<?php echo htmlspecialchars($rel['product_name']); ?>">
																			<?php echo htmlspecialchars($rel['product_name']); ?>
																		</a>
																	</h5>
																	<div class="price-box">
																		<span class="price">₹
																			<?php echo number_format($rel_price, 2); ?></span>
																		<?php if ($rel_discount > 0): ?>
																				<span class="old-price">₹
																					<?php echo number_format($rel_old_price, 2); ?></span>
																		<?php endif; ?>
																	</div>
																</div>
															</div>
														</div>
												<?php endforeach; ?>
										<?php else: ?>
												<div class="item">
													<p>No related products found.</p>
												</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- product-details-other-product-end -->
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
		<?php include 'footer.php'; ?>
		<!-- footer-end -->
		<!-- modal start -->
		<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="row">
						<div class="col-md-5 col-sm-5 col-xs-6 ">
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
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
									aria-hidden="true">&times;</span></button>
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
								<p>Faded short sleeves t-shirt with high neckline. Soft and stretchy material for a
									comfortable fit.
									Accessorize with a straw hat and you're ready for summer!
								</p>
							</div>
							<form action="#">
								<div class="shop-product-add">
									<div class="add-cart">
										<p class="quantity cart-plus-minus">
											<label>Quantity</label>
											<input class="text" type="text" value="1">

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
												<select class="form-control" name="group_1">
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
		<script>
			document.querySelectorAll('.variant-item').forEach(item => {
				item.addEventListener('click', function() {
					const id = this.dataset.variantId || 0; // Support main product color too
					const productId = this.dataset.mainProductId || 0;
					if (!id || !productId) {
						console.error('No variant or main product ID found on element.');
						return;
					}
					loadVariant(id, productId, this);
				});
			});

			function loadVariant(id, productId, el) {
				fetch(`get_variant.php?id=${encodeURIComponent(id)}&productId=${encodeURIComponent(productId)}`)
					.then(res => {
						if (!res.ok) throw new Error(`Network error: ${res.status}`);
						return res.json();
					})
					.then(data => {
						if (!data || data.error) {
							alert(data?.error || 'No variant data found');
							return;
						}

						// ---------------------------
						// 1. Update Zoom/Main Image
						// ---------------------------
						const zoomEl = document.querySelector('.zoom-image');
						if (zoomEl && Array.isArray(data.image) && data.image.length > 0) {
							zoomEl.style.backgroundImage = `url('admin/${data.image[0]}')`;
						}

						// ---------------------------
						// 2. Update Thumbnail Tabs
						// ---------------------------
						const picTabs = document.querySelector('.pic-tabs');
						if (picTabs) {
							picTabs.innerHTML = ''; // Clear existing tabs

							if (Array.isArray(data.image) && data.image.length > 0) {
								// Add thumbnails for each image
								data.image.forEach((img, idx) => {
									picTabs.innerHTML += `
							<li>
								<a class="${idx === 0 ? 'active' : ''}" 
								   href="#picture-${idx + 1}" data-bs-toggle="tab">
								   <img src="admin/${img}" alt="" />
								</a>
							</li>
						`;
								});
							} else if (window.defaultProductImages && defaultProductImages.length > 0) {
								// Fallback to default product images
								defaultProductImages.forEach((img, idx) => {
									picTabs.innerHTML += `
							<li>
								<a class="${idx === 0 ? 'active' : ''}" 
								   href="#picture-${idx + 1}" data-bs-toggle="tab">
								   <img src="admin/${img}" alt="" />
								</a>
							</li>
						`;
								});
							} else {
								// No images available
								picTabs.innerHTML = `
						<li>
							<a class="active" href="#picture-1" data-bs-toggle="tab">
								<img src="img/no-image.png" alt="No image" />
							</a>
						</li>
					`;
							}
						}

						// ---------------------------
						// 3. Update Prices
						// ---------------------------
						const priceEl = document.querySelector('.price-new .price-box');
						if (priceEl) {
							priceEl.textContent = '₹' + parseFloat(data.final_price || 0).toFixed(2);
						}

						const oldPriceEl = document.querySelector('.old-price');
						if (oldPriceEl) {
							if (data.old_price && parseFloat(data.old_price) > parseFloat(data.final_price)) {
								oldPriceEl.textContent = '₹' + parseFloat(data.old_price).toFixed(2) + ' tax incl.';
								oldPriceEl.style.display = '';
							} else {
								oldPriceEl.style.display = 'none';
							}
						}

						// ---------------------------
						// 4. Stock & Availability
						// ---------------------------
						const stockEl = document.querySelector('.quantity-available span:first-child');
						if (stockEl) stockEl.textContent = data.stock ?? '0';

						const availabilityEl = document.querySelector('.availability-status span');
						if (availabilityEl) {
							availabilityEl.textContent = (data.stock > 0) ? 'In stock' : 'Out of stock';
						}

						// ---------------------------
						// 5. Highlight Selected Variant
						// ---------------------------
						document.querySelectorAll('.variant-item').forEach(e => e.style.border = '1px solid #ddd');
						if (el) el.style.border = '2px solid #007bff';
					})
					.catch(err => {
						console.error('Error loading variant:', err);
						alert('Error loading variant details: ' + err.message);
					});
			}
		</script>



		<script>
			function addToCart(productId) {
				var quantity = document.getElementById('quantity_wanted').value;
				if (quantity < 1) quantity = 1;

				// Redirect to shopping cart with quantity
				window.location.href = 'shopping-cart.php?action=add&id=' + productId + '&qty=' + quantity;
				return false;
			}

			// Update add to cart link when quantity changes
			document.addEventListener('DOMContentLoaded', function() {
				var quantityInput = document.getElementById('quantity_wanted');
				var addToCartLink = document.querySelector('.cart-btn[onclick]');

				if (quantityInput && addToCartLink) {
					quantityInput.addEventListener('change', function() {
						var productId = addToCartLink.getAttribute('onclick').match(/\d+/)[0];
						var newQuantity = this.value;
						if (newQuantity < 1) newQuantity = 1;

						addToCartLink.href = 'shopping-cart.php?action=add&id=' + productId + '&qty=' + newQuantity;
					});
				}

				// Initialize Owl Carousel for related products
				if (typeof $ !== 'undefined' && $.fn.owlCarousel) {
					$('.accessories-carousel').owlCarousel({
						loop: true,
						margin: 20,
						nav: true,
						dots: true,
						autoplay: true,
						autoplayTimeout: 5000,
						autoplayHoverPause: true,
						responsive: {
							0: {
								items: 1
							},
							600: {
								items: 2
							},
							1000: {
								items: 4
							}
						}
					});
				}
			});
		</script>
</body>

</html>