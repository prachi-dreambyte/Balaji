<?php
include 'connect.php';

$product = [
	'product_name' => '',
	'tag_number' => '',
	'price' => '',
	'discount' => '',
	'description' => '',
	'stock' => '',
	'brand' => '',
	'weight' => '',
	'size' => '',
	'category' => '',
	'tags' => '',
	'images' => ''
];
$images = [];
$related_products = [];
if (isset($_GET['id'])) {
	$product_id = intval($_GET['id']);
	$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
	$stmt->bind_param("i", $product_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	if ($row) {
		$product = $row;
		$images = json_decode($product['images'], true);
		if (!is_array($images)) $images = [];
		// Fetch related products (same category, not this product)
		$rel_stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 8");
		$rel_stmt->bind_param("si", $product['category'], $product_id);
		$rel_stmt->execute();
		$rel_result = $rel_stmt->get_result();
		while ($rel = $rel_result->fetch_assoc()) {
			$related_products[] = $rel;
		}
		$rel_stmt->close();
	}
}
// Parse sizes
$sizes = array_filter(array_map('trim', explode(',', $product['size'])));
// Parse tags
$tags = array_filter(array_map('trim', explode(',', $product['tags'] ?? '')));
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
	<!-- <!doctype html>
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

		<!-- mobile-menu-area-end -->
		</header>
		<!-- header-end -->
		<!-- product-details-area-start -->
		<div class="shop-1-area">
			<div class="container">
				<div class="breadcrumb">
					<a href="index.php" title="Return to Home">
						<i class="icon-home"></i>
					</a>
					<span class="navigation-pipe">></span>
					<span class="navigation-page">
						<a href="#" title="CATEGORY ">
							<span>CATEGORY </span>
						</a>
						<span class="navigation-pipe nav-pipe-2">></span>
						Faded Short Sleeves T-shirt
					</span>
				</div>
				<div class="row">
					<div class="col-md-5 col-sm-6 col-xs-12">
						<!-- Primary Product Image -->
						<!-- Remove the primary-product-image block entirely so only the gallery remains -->
						<div class="picture-tab">
							<!-- Nav tabs -->
							<!-- Dynamic Product Image Gallery -->
							<ul class="pic-tabs nav" role="tablist">
								<?php if (!empty($images)) {
									foreach ($images as $idx => $img): ?>
										<li><a class="<?php echo $idx === 0 ? 'active' : ''; ?>" href="#picture-<?php echo $idx + 1; ?>" aria-controls="picture-<?php echo $idx + 1; ?>" role="tab" data-bs-toggle="tab"><img src="admin/<?php echo htmlspecialchars($img); ?>" alt="" /></a></li>
									<?php endforeach;
								} else { ?>
									<li><a class="active" href="#picture-1" aria-controls="picture-1" role="tab" data-bs-toggle="tab"><img src="img/no-image.png" alt="No image" /></a></li>
								<?php } ?>
							</ul>
							<div class="tab-content">
								<?php if (!empty($images)) {
									foreach ($images as $idx => $img): ?>
										<div role="tabpanel" class="tab-pane fade<?php echo $idx === 0 ? ' active show' : ''; ?>" id="picture-<?php echo $idx + 1; ?>">
											<div class="single-product">
												<div class="product-img">
													<a href="#"><img src="admin/<?php echo htmlspecialchars($img); ?>" alt="" /></a>
												</div>
											</div>
										</div>
									<?php endforeach;
								} else { ?>
									<div role="tabpanel" class="tab-pane active fade show" id="picture-1">
										<div class="single-product">
											<div class="product-img">
												<a href="#"><img src="img/no-image.png" alt="No image" /></a>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="col-md-7 col-sm-6 col-xs-12 shop-content">
						<h1><?php echo $product['product_name'] !== '' ? htmlspecialchars($product['product_name']) : '---'; ?></h1>
						<p class="reference">
							<label>Reference: </label>
							<span><?php echo $product['tag_number'] !== '' ? htmlspecialchars($product['tag_number']) : '---'; ?></span>
						</p>
						<p class="condition">
							<label>Condition: </label>
							<span>New product</span>
						</p>
						<div class="content-price">
							<p class="price-new">
								<span class="price-box"><?php echo $product['price'] !== '' ? '₹ ' . htmlspecialchars($product['price']) : '---'; ?></span>
								<span class="price-tax"> tax incl.</span>
							</p>
							<?php if ($product['discount'] !== '' && $product['discount'] > 0): ?>
								<p class="reduction-percent">
									<span>-<?php echo htmlspecialchars($product['discount']); ?>%</span>
								</p>
							<?php endif; ?>
							<p class="old-price">
								<?php if ($product['discount'] !== '' && $product['discount'] > 0 && $product['price'] !== ''): ?>
									<span class="price">₹ <?php echo number_format($product['price'] + ($product['price'] * $product['discount'] / 100), 2); ?></span>
									tax incl.
								<?php endif; ?>
							</p>
						</div>
						<div class="short-description">
							<p><?php echo $product['description'] !== '' ? nl2br(htmlspecialchars($product['description'])) : '---'; ?></p>
						</div>
						<form action="#">
							<div class="shop-product-add">
								<div class="add-cart">
									<p class="quantity cart-plus-minus">
										<label>Quantity</label>
										<input id="quantity_wanted" class="text" type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock'] !== '' ? (int)$product['stock'] : 1; ?>" <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>

									</p>
									<style>
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
											vertical-align: middle;
										}

										.cart-btn:hover {
											transform: translateY(-2px);
											background-color: #111;
										}

										.cart-btn.disabled {
											background-color: #888;
											color: #eee;
											cursor: not-allowed;
											pointer-events: none;
										}

										.cart-btn i {
											font-size: 15px;
										}
									</style>

									<div class="shop-add-cart" style="margin-top: 15px;">
										<?php if ($product['stock'] <= 0): ?>
											<button class="cart-btn disabled">
												<i class="fa fa-times-circle"></i> Out of Stock
											</button>
										<?php else: ?>
											<a href="shopping-cart.php?action=add&id=<?php echo $product['id']; ?>" class="cart-btn">
												<i class="fa fa-shopping-cart"></i> Add to Cart
											</a>
										<?php endif; ?>
									</div>

									<ul class="usefull-links">
										<!-- <li class="sendtofriend">
											<a class="send-friend-button" href="#"> Send to a friend </a>
										</li>
										<li class="print">
											<a class="#" href="#"> Print </a>
										</li> -->
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
												<?php if (!empty($sizes)) {
													foreach ($sizes as $size): ?>
														<option value="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($size); ?></option>
													<?php endforeach;
												} else { ?>
													<option value="">---</option>
												<?php } ?>
											</select>
										</div>
									</fieldset>
									<!-- Color: not implemented, left as static -->
									<!-- <fieldset class="color">
										<label>Color</label>
										<div class="color-selector">
											<ul>
												<li><a class="color-1" href="#"></a></li>
												<li><a class="color-2" href="#"></a></li>
											</ul>
										</div>
									</fieldset> -->
								</div>
							</div>
						</form>
						<div class="clearfix"></div>
						<p class="quantity-available">
							<span><?php echo $product['stock'] !== '' ? htmlspecialchars($product['stock']) : '---'; ?></span>
							<span>Items</span>
						</p>
						<p class="availability-status">
							<span><?php echo ($product['stock'] > 0) ? 'In stock' : 'Out of stock'; ?></span>
						</p>
						<?php if (!empty($tags)): ?>
							<p class="product-tags"><strong>Tags:</strong> <?php echo implode(', ', array_map('htmlspecialchars', $tags)); ?></p>
						<?php endif; ?>
						<?php if ($product['brand']): ?>
							<p class="product-brand"><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand']); ?></p>
						<?php endif; ?>
						<?php if ($product['weight']): ?>
							<p class="product-weight"><strong>Weight:</strong> <?php echo htmlspecialchars($product['weight']); ?></p>
						<?php endif; ?>
						<!-- <p class="social-sharing">
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
						</p> -->
						<!-- <div class="product-comment">
	<div class="comment-note clearfix">
		<span>Rating</span>
		<div class="star-content clearfix">
			<?php
			$avgQuery = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = " . intval($product['id']);
			$avgResult = mysqli_query($conn, $avgQuery);
			$avgData = mysqli_fetch_assoc($avgResult);
			$avgRating = round($avgData['avg_rating']);
			$totalReviews = $avgData['total_reviews'];

			for ($i = 1; $i <= 5; $i++) {
				echo '<span class="star ' . ($i <= $avgRating ? 'star-on' : '') . '"></span>';
			}
			?>
		</div>
	</div>

	<ul class="comments-advices">
		<li>
			<a class="reviews" href="#reviews"> Read reviews (<?php echo $totalReviews; ?>)</a>
		</li>
		<li>
			<a class="open-comment-form" href="#reviews"> Write a review </a>
		</li>
	</ul>
</div> -->

					</div>
				</div>
			</div>
		</div>
		<!-- product-details-area-end -->
		<!-- product-details-tab-start -->
		<div class="shop-1-product-tab">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="feature-tab-area">
							<!-- Nav tabs -->
							<ul class="tabs nav mb-4" role="tablist">
								<li><a class="active" href="#moreinfo" aria-controls="moreinfo" role="tab" data-bs-toggle="tab">more info</a></li>
								<li><a href="#datasheet" aria-controls="datasheet" role="tab" data-bs-toggle="tab">data sheet</a></li>
								<li class="mar1"><a href="#reviews" aria-controls="reviews" role="tab" data-bs-toggle="tab">reviews</a></li>
							</ul>
							<div class="tab-content">
								<!-- More Info Tab -->
								<div role="tabpanel" class="tab-pane active fade show" id="moreinfo">
									<div class="tab-box">
										<div class="more-info">
											<p><?php echo $product['description'] !== '' ? nl2br(htmlspecialchars($product['description'])) : '---'; ?></p>
										</div>
									</div>
								</div>
								<!-- Data Sheet Tab -->
								<div role="tabpanel" class="tab-pane fade" id="datasheet">
									<div class="tab-box">
										<table class="table-data-sheet">
											<tbody>
												<tr class="odd">
													<td>Brand</td>
													<td><?php echo $product['brand'] !== '' ? htmlspecialchars($product['brand']) : '---'; ?></td>
												</tr>
												<tr class="even">
													<td>Weight</td>
													<td><?php echo $product['weight'] !== '' ? htmlspecialchars($product['weight']) : '---'; ?></td>
												</tr>
												<tr class="odd">
													<td>Size</td>
													<td><?php echo $product['size'] !== '' ? htmlspecialchars($product['size']) : '---'; ?></td>
												</tr>
												<tr class="even">
													<td>Category</td>
													<td><?php echo $product['category'] !== '' ? htmlspecialchars($product['category']) : '---'; ?></td>
												</tr>
												<tr class="odd">
													<td>Stock</td>
													<td><?php echo $product['stock'] !== '' ? htmlspecialchars($product['stock']) : '---'; ?></td>
												</tr>
												<tr class="even">
													<td>Tag Number</td>
													<td><?php echo $product['tag_number'] !== '' ? htmlspecialchars($product['tag_number']) : '---'; ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<!-- ⭐ REVIEW SECTION START -->
								<div role="tabpanel" class="tab-pane fade in " id="reviews" style="padding: 25px;">

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

									<!-- Header and Toggle -->
									<div class="write-review-toggle">
										<h3>Product Reviews</h3>
										<button onclick="toggleReviewForm()" class="review-btn">Write a Review</button>
									</div>
									<hr style="margin-bottom: 18px;">
									<!-- Write Review Form -->
									<div id="writeReviewForm" style="display: none;">
										<form action="submit-review.php" method="POST" enctype="multipart/form-data">
											<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
											<label style="font-weight: 500;">Your Rating:</label>
											<div id="star-rating" style="font-size: 38px; margin-bottom: 10px;">
												<?php for ($i = 5; $i >= 1; $i--): ?>
													<input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
													<label for="star<?php echo $i; ?>">★</label>
												<?php endfor; ?>
											</div>
											<label style="font-weight: 500;">Your Review:</label>
											<textarea name="review_text" rows="4" required placeholder="Share your experience..."></textarea>
											<label style="font-weight: 500;">Upload Product Images (optional):</label>
											<input type="file" name="review_image[]" multiple accept="image/*" id="reviewImageInput" onchange="previewReviewImages(this)">
											<div id="reviewImagePreview" style="display: flex; gap: 8px; margin: 10px 0 18px 0;"></div>
											<button type="submit" class="review-btn">Submit Review</button>
										</form>
									</div>
									<!-- Display Reviews -->
									<div id="reviewList">
										<?php
										$productId = $product['id'];
										$query = "SELECT * FROM reviews WHERE product_id = $productId ORDER BY created_at DESC LIMIT 3";
										$result = mysqli_query($conn, $query);
										$totalReviews = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM reviews WHERE product_id = $productId"));
										$shownReviews = 0;
										if (mysqli_num_rows($result) > 0):
											while ($review = mysqli_fetch_assoc($result)):
												$shownReviews++;
												$images = json_decode($review['image_path'], true);
										?>
												<div class="review-block" style="border-bottom: 1.5px solid #e0e0e0; padding: 18px 0 12px 0;">
													<div class="review-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
														<div style="font-weight: 600; font-size: 17px; color: #222; letter-spacing: 0.2px;">
															<i class="fa fa-user-circle" style="font-size: 20px; color: #888; margin-right: 4px;"></i>
															<?php echo htmlspecialchars($review['user_name']); ?>
														</div>
														<div class="review-rating" style="font-size: 22px; color: #f39c12; margin-left: 10px;">
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
																<img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Review Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc;">
															<?php endforeach; ?>
														</div>
													<?php endif; ?>
													<div class="review-text" style="font-size: 15.5px; margin-top: 2px; color: #222;">
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
								<!-- ⭐ REVIEW SECTION END -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--product-details-tab-end -->
		<!-- product-details-accessories-start -->
		<div class="accessories-area">
			<div class="container">
				<div class="row ">
					<div class="col-md-12">
						<div class="product-title">
							<h2>
								<span>accessories</span>
							</h2>
						</div>
						<div class="owl-carousel-space">
							<div class="row">
								<div class="accessories">
									<div class="accessories-carousel">
										<!-- single-product start -->
										<div class="col-md-12">
											<div class="single-product">
												<div class="product-img">
													<a href="#">
														<img src="img/tab-pro/printed-chiffon-dress.jpg" alt="" />
													</a>
													<span class="new">new</span>
													<span class="sale">sale!</span>
													<div class="product-action">
														<div class="add-to-links">
															<ul>
																<li>
																	<a href="#" title="Add to cart">
																		<i class="fa fa-shopping-cart"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to wishlist">
																		<i class="fa fa-heart" aria-hidden="true"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to compare">
																		<i class="fa fa-bar-chart" aria-hidden="true"></i>
																	</a>
																</li>
															</ul>
															<div class="quick-view">
																<a href="#" data-bs-toggle="modal" data-target="#myModal" title="Quick view">
																	<span>Quick view</span>
																</a>
															</div>
														</div>
													</div>
												</div>
												<div class="product-content">
													<h5 class="product-name">
														<a href="#" title="Printed Chiffon Dress">Printed Chiffon Dress</a>
													</h5>
													<div class="reviews">
														<div class="star-content clearfix">
															<span class="star star-on"></span>
															<span class="star star-on"></span>
															<span class="star star-on"></span>
															<span class="star star-on"></span>
															<span class="star star-on"></span>
														</div>
														<div class="comment">
															<span class="reviewcount">1</span>
															Review(s)
														</div>
													</div>
													<div class="price-box">
														<span class="price"> £ 19.68 </span>
														<span class="old-price"> £ 24.60 </span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="single-product">
												<div class="product-img">
													<a href="#">
														<img src="img/tab-pro/lamp.jpg" alt="" />
													</a>
													<span class="new">new</span>
													<div class="product-action">
														<div class="add-to-links">
															<ul>
																<li>
																	<a href="#" title="Add to cart">
																		<i class="fa fa-shopping-cart"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to wishlist">
																		<i class="fa fa-heart" aria-hidden="true"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to compare">
																		<i class="fa fa-bar-chart" aria-hidden="true"></i>
																	</a>
																</li>
															</ul>
															<div class="quick-view">
																<a href="#" data-bs-toggle="modal" data-target="#myModal" title="Quick view">
																	<span>Quick view</span>
																</a>
															</div>
														</div>
													</div>
												</div>
												<div class="product-content">
													<h5 class="product-name">
														<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
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
														<span class="price"> £ 36.60 </span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="single-product">
												<div class="product-img">
													<a href="#">
														<img src="img/tab-pro/printed-summer-dress.jpg" alt="" />
													</a>
													<span class="new">new</span>
													<span class="sale">sale!</span>
													<div class="product-action">
														<div class="add-to-links">
															<ul>
																<li>
																	<a href="#" title="Add to cart">
																		<i class="fa fa-shopping-cart"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to wishlist">
																		<i class="fa fa-heart" aria-hidden="true"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to compare">
																		<i class="fa fa-bar-chart" aria-hidden="true"></i>
																	</a>
																</li>
															</ul>
															<div class="quick-view">
																<a href="#" data-bs-toggle="modal" data-target="#myModal" title="Quick view">
																	<span>Quick view</span>
																</a>
															</div>
														</div>
													</div>
												</div>
												<div class="product-content">
													<h5 class="product-name">
														<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
													</h5>
													<div class="reviews">
														<div class="star-content clearfix">
															<span class="star star-on"></span>
															<span class="star star-on"></span>
															<span class="star star-on"></span>
															<span class="star star-on"></span>
															<span class="star star-on"></span>
														</div>
														<div class="comment">
															<span class="reviewcount">1</span>
															Review(s)
														</div>
													</div>
													<div class="price-box">
														<span class="price"> £ 34.78 </span>
														<span class="old-price"> £ 36.61 </span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="single-product">
												<div class="product-img">
													<a href="#">
														<img src="img/tab-pro/printed-dress.jpg" alt="" />
													</a>
													<span class="new">new</span>
													<div class="product-action">
														<div class="add-to-links">
															<ul>
																<li>
																	<a href="#" title="Add to cart">
																		<i class="fa fa-shopping-cart"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to wishlist">
																		<i class="fa fa-heart" aria-hidden="true"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to compare">
																		<i class="fa fa-bar-chart" aria-hidden="true"></i>
																	</a>
																</li>
															</ul>
															<div class="quick-view">
																<a href="#" title="Quick view" data-bs-toggle="modal" data-target="#myModal">
																	<span>Quick view</span>
																</a>
															</div>
														</div>
													</div>
												</div>
												<div class="product-content">
													<h5 class="product-name">
														<a href="#" title="Printed Dress">Printed Dress</a>
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
														<span class="price"> £ 55.07 </span>
														<span class="old-price"> £ 61.19 </span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="single-product">
												<div class="product-img">
													<a href="#">
														<img src="img/tab-pro/cup.jpg" alt="" />
													</a>
													<span class="new">new</span>
													<span class="sale">sale</span>
													<div class="product-action">
														<div class="add-to-links">
															<ul>
																<li>
																	<a href="#" title="Add to cart">
																		<i class="fa fa-shopping-cart"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to wishlist">
																		<i class="fa fa-heart" aria-hidden="true"></i>
																	</a>
																</li>
																<li>
																	<a href="#" title="Add to compare">
																		<i class="fa fa-bar-chart" aria-hidden="true"></i>
																	</a>
																</li>
															</ul>
															<div class="quick-view">
																<a href="#" title="Quick view" data-bs-toggle="modal" data-target="#myModal">
																	<span>Quick view</span>
																</a>
															</div>
														</div>
													</div>
												</div>
												<div class="product-content">
													<h5 class="product-name">
														<a href="#" title="Printed Dress">Printed Dress</a>
													</h5>
													<div class="reviews">
														<div class="star-content clearfix">
															<span class="star star-on"></span>
															<span class="star star-on"></span>
															<span class="star star-on"></span>
															<span class="star star-on"></span>
															<span class="star star-on"></span>
														</div>
														<div class="comment">
															<span class="reviewcount">1</span>
															Review(s)
														</div>
													</div>
													<div class="price-box">
														<span class="price"> £ 28.08 </span>
														<span class="old-price"> £ 31.20 </span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
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
									<div class="accessories-carousel">
										<?php if (!empty($related_products)): ?>
											<?php foreach ($related_products as $rel):
												$rel_images = json_decode($rel['images'], true);
												$rel_img = (!empty($rel_images) && !empty($rel_images[0])) ? 'admin/' . $rel_images[0] : 'img/no-image.png';
											?>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="product-details.php?id=<?php echo $rel['id']; ?>">
																<img src="<?php echo $rel_img; ?>" alt="<?php echo htmlspecialchars($rel['product_name']); ?>" />
															</a>
														</div>
														<div class="product-content">
															<h5 class="product-name">
																<a href="product-details.php?id=<?php echo $rel['id']; ?>" title="<?php echo htmlspecialchars($rel['product_name']); ?>">
																	<?php echo htmlspecialchars($rel['product_name']); ?>
																</a>
															</h5>
															<div class="price-box">
																<span class="price">₹ <?php echo $rel['price']; ?></span>
															</div>
														</div>
													</div>
												</div>
											<?php endforeach; ?>
										<?php else: ?>
											<p>No related products found.</p>
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
		<!-- brand-area-end -->
		<!-- footer-start -->
		<footer>
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
							<div class="col-md-6 col-sm-6 col-xs-12 address">
								<p class="copyright">&copy; 2021 <strong>Vonia</strong> Made with <i class="fa fa-heart text-danger" aria-hidden="true"></i> by <a href="https://hasthemes.com/"><strong>HasThemes</strong></a>.</p>
							</div>
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
		</footer>
		<!-- footer-end -->
		<!-- modal start -->
		<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
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
</body>


</html>