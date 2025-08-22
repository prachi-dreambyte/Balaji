<?php
session_start();
$compare_count = isset($_SESSION['compare_list']) ? count($_SESSION['compare_list']) : 0;
include 'connect.php';

// Initialize the cart if needed
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


// Function to get category banner
function getCategoryBanner($category_name, $conn) {
    // Agar multiple categories aaye, to default banner return karo
    if (strpos($category_name, ',') !== false) {
        return 'admin/assets/images/coming-soon.png';
    }

    // Single category ke liye DB se banner fetch karo
    $stmt = $conn->prepare("SELECT banner_image FROM categories WHERE category_name = ?");
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return !empty($row['banner_image']) ? 'admin/'.$row['banner_image'] : 'admin/assets/images/coming-soon.png';
    }
    return 'admin/assets/images/coming-soon.png';
}

// Get current category from URL
$currentCategory = isset($_GET['category']) ? urldecode($_GET['category']) : '';

// Get banner image
$bannerImage = getCategoryBanner($currentCategory, $conn);


// Get user account type
$user_account_type = null;
if (!empty($_SESSION['account_type'])) {
    $user_account_type = strtolower(trim($_SESSION['account_type']));
} elseif (!empty($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT account_type FROM signup WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['account_type'] = $row['account_type'];
        $user_account_type = strtolower(trim($row['account_type']));
    }
    $stmt->close();
}

// ---------- Pagination Variables ----------
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// ---------- Filters & Search ----------
$category_raw = isset($_GET['category']) ? trim(urldecode($_GET['category'])) : '';
$category_names = array_filter(array_map('trim', explode(',', $category_raw)));
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';

$order_by = '';
switch ($sort_by) {
    case 'price_asc': $order_by = ' ORDER BY price ASC'; break;
    case 'price_desc': $order_by = ' ORDER BY price DESC'; break;
    case 'name_asc': $order_by = ' ORDER BY product_name ASC'; break;
    case 'name_desc': $order_by = ' ORDER BY product_name DESC'; break;
}

$like = "%$search_term%";
$params = [];
$types = [];
$conditions = [];

$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$enable_price_filter = $min_price > 0 && $max_price > 0;

if (!empty($category_names)) {
    $placeholders = implode(',', array_fill(0, count($category_names), '?'));
    $conditions[] = "category IN ($placeholders)";
    $params = array_merge($params, $category_names);
    $types[] = str_repeat('s', count($category_names));
}

$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search_term)) {
	// Handle "under 1000" type search
	if (preg_match('/under\s+(\d+)/i', $search_term, $matches)) {
		$price_limit = (int) $matches[1];
		$conditions[] = "price <= ?";
		$params[] = $price_limit;
		$types[] = 'i';

		// Remove "under 1000" from search text so only keywords remain
		$search_term = preg_replace('/under\s+\d+/i', '', $search_term);
		$search_term = trim($search_term);
	}

	if (!empty($search_term)) {
		$like = "%$search_term%";
		$conditions[] = "(product_name LIKE ? 
                        OR category LIKE ? 
                        OR short_description LIKE ? 
                        OR description LIKE ? 
                        OR tags LIKE ?)";
		$params = array_merge($params, [$like, $like, $like, $like, $like]);
		$types[] = 'sssss';
	}
}



if ($enable_price_filter) {
    $conditions[] = "price BETWEEN ? AND ?";
    $params[] = $min_price;
    $params[] = $max_price;
    $types[] = 'dd';
}

$where = !empty($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '';
$type_str = implode('', $types);

// ---------- Get Total Products ----------
$count_sql = "SELECT COUNT(*) AS total FROM products" . $where;
$count_stmt = $conn->prepare($count_sql);
if ($type_str) $count_stmt->bind_param($type_str, ...$params);
$count_stmt->execute();
$total_products = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

// ---------- Get Products for Current Page ----------
$sql = "SELECT * FROM products" . $where . $order_by . " LIMIT ?, ?";
$params_with_limit = array_merge($params, [$offset, $limit]);
$type_str_with_limit = $type_str . 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($type_str_with_limit, ...$params_with_limit);
$stmt->execute();
$result = $stmt->get_result();
$allRows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- Categories for sidebar ----------
$cat_sidebar_sql = "SELECT * FROM categories ORDER BY created_at DESC";
$cat_sidebar_stmt = $conn->prepare($cat_sidebar_sql);
$cat_sidebar_stmt->execute();
$categories = $cat_sidebar_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$cat_sidebar_stmt->close();
?>




<!doctype html>
<html class="no-js" lang="">


<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Balaji Category</title>
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
<style>

	.product-img .add-to-cart-btn {
   position: absolute;
    bottom: -60px; /* Pehle hidden */
    left: 50%;
    transform: translateX(-50%);
    width: 80%;  /* Thoda chhota aur center me */
    transition: all 0.3s ease;
    opacity: 0;
}

.product-img:hover .add-to-cart-btn {
  bottom: 15px;   /* Bottom se thoda upar */
    opacity: 1;
}
.add-to-cart-btn a {
    background-color: #f5f6f2; /* Bootstrap Danger Red */
    border: none;
    border-radius: 30px; /* Rounded look */
    padding: 10px 15px;
    color: #845848;
    font-weight: 600;
    font-size: 14px;
    display: block;
    text-align: center;
    text-decoration: none;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: background-color 0.3s, transform 0.2s;
}
.add-to-cart-btn a:hover {
    background-color: #845848; /* Darker Red */
	color: #fff;
    transform: scale(1.05);
}

 /* .AddCart a ::before {
  display: inline-block;
  font-family: "FontAwesome";
  font-size: 14px;
  font-weight: normal;
  padding: 6px 10px;
  content:none;
}
.AddCart a {
	padding: 5px 15px;
}  */

  .add-to-cart-Btn{
        font-family: Poppins, sans-serif;
    font-weight: 400;
    display: inline-block;
    position: relative;
    z-index: 0;
    padding: 15px 30px;
    text-decoration: none;
    background: #845848 ! important;
    color: white;
    overflow: hidden;
    cursor: pointer;
    text-transform: uppercase;
    border-radius: 5px;
    font-size: 15px
    }
    /* .add-to-cart-btn:hover{
     background-color: #e393a7 !important;
     color:#fff;
    } */
    .wishlist-btn{
      font-size:18px;
      background-color: #845848 !important;
	   text-decoration: none !important;
    }
    .wishlist-btn:hover{
       background-color: #e393a7 !important;
     color:#fff;
    }

.sort-by label,.show label,.show span {

  font-size: 15px !important;
  font-weight:500 !important;
  }
  .single-product .product-img .new,.modal-pic .new{
  background: #ffffff none repeat scroll 0 0 !important;}


/* Category Banner Styles */
.category-banner-section {
    position: relative;
    margin-bottom: 30px;
}

.banner-container {
    position: relative;
    height: 350px;
    overflow: hidden;
}

.category-banner-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
}

.banner-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 15px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.breadcrumbs {
    font-size: 1.1rem;
}

.breadcrumbs a {
    color: white;
    text-decoration: none;
}

.breadcrumbs a:hover {
    text-decoration: underline;
}

.breadcrumbs span {
    color: #f8f9fa;
    font-weight: 500;
}

@media (max-width: 768px) {
    .banner-container {
        height: 250px;
    }
    
    .banner-title {
        font-size: 2rem;
    }
    
    .breadcrumbs {
        font-size: 0.9rem;
    }
}
</style>

<body>
	<!-- header-start -->
	<?php include 'header.php'; ?>
	</header>

	<!-- header-end -->

<!-- Dynamic Category Banner Section -->
<section class="category-banner-section">
    <div class="banner-container">
        <img src="<?php echo $bannerImage; ?>" alt="<?php echo $currentCategory ? htmlspecialchars($currentCategory) : 'All Categories'; ?>" class="category-banner-img">
        <!-- <div class="banner-overlay">
            <h1 class="banner-title"><?php echo $currentCategory ? htmlspecialchars($currentCategory) : 'All Categories'; ?></h1>
            <div class="breadcrumbs">
                <a href="index.php">Home</a> / 
                <a href="shop.php">Categories</a>
                <?php if ($currentCategory): ?>
                    / <span><?php echo htmlspecialchars($currentCategory); ?></span>
                <?php endif; ?>
            </div>
        </div> -->
    </div>
</section>

	<!-- shop-2-area-start -->
	<!-- <div class="shop-2-area" id="product-list"> -->
	<!-- <section class="AboutSection">
		<div class="image-wrapper">
			<img src="img\slider\5.jpg" class="AboutwrapperImage" />
			<h1 class="aboutUs-Heading">CATEGORY</h1>
			<div class="AboutDivWrapper">
				<a class="AboutHome"> HOME </a> &nbsp/ &nbsp <a class="AboutHome">CATEGORY</a>
			</div>
		</div>
	</section> -->
	
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
									<!-- <li><a href="#list_view" role="tab" data-bs-toggle="tab">
											<i class="fa fa-th-list"></i></a>
									</li> -->
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

    // --- PRODUCT-SPECIFIC VARIABLE SETUP ---
    $product_id = $row['id'];

    // Reviews
    $review_query = "SELECT COUNT(*) AS total_reviews, AVG(rating) AS avg_rating FROM reviews WHERE product_id = $product_id";
    $review_result = mysqli_query($conn, $review_query);
    if ($review_result && mysqli_num_rows($review_result) > 0) {
        $reviewData = mysqli_fetch_assoc($review_result);
        $total_reviews = $reviewData['total_reviews'] ?? 0;
        $avg_rating = round($reviewData['avg_rating'] ?? 0);
    } else {
        $total_reviews = 0;
        $avg_rating = 0;
    }

    // Pricing
    $price = isset($row['price']) ? floatval($row['price']) : 0;
    $discount = isset($row['discount']) ? floatval($row['discount']) : 0;
    $corporate_discount = isset($row['corporate_discount']) ? floatval($row['corporate_discount']) : 0;
    $old_price = $price;
    $final_price = $price - $discount;
    if (!empty($user_account_type) && $user_account_type === 'commercial' && $corporate_discount > 0) {
        $final_price -= $corporate_discount;
    }

    // Images
    $images = json_decode($row['images'], true);
    $firstImage = is_array($images) && !empty($images) ? $images[0] : 'default.jpg';

?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="single-product">
            <div class="product-img" style="position: relative; overflow: hidden;">
                <a href="product-details.php?id=<?php echo $row['id']; ?>">
                    <img src="./admin/<?php echo htmlspecialchars($firstImage); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" />
                </a>
                
                <!-- Wishlist Button -->
                <div class="wishlist" style="position: absolute; top: 10px; right: 10px; z-index: 2; font-size: 20px;">
                    <a class="wishlistBtn" href="wishlist.php?action=add&id=<?php echo $row['id']; ?>" title="Add to wishlist">
                        <i class="fa fa-heart" aria-hidden="true"></i>
                    </a>
                </div>

                <!-- Add to Cart Button (Hidden by default, show on hover) -->
                <div class="add-to-cart-btn">
                    <a class="btn btn-danger w-100" 
                       href="shopping-cart.php?action=add&id=<?php echo $row['id']; ?>" 
                       title="Add to cart">
                        <i class="fa fa-shopping-cart"></i> Add to Cart
                    </a>
                </div>
            </div>

            <div class="product-content text-center">
                <h5 class="product-name">
                    <a href="product-details.php?id=<?php echo $row['id']; ?>" title="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <?php echo htmlspecialchars($row['product_name']); ?>
                    </a>
                </h5>
                <!-- ⭐ Reviews Section -->
                <div class="reviews">
                    <div class="star-content clearfix">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <?php if ($i < $avg_rating): ?>
                                <span class="star star-on"></span>
                            <?php else: ?>
                                <span class="star"></span>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <div class="comment">
                        <span class="reviewcount"><?php echo $total_reviews; ?></span> Review(s)
                    </div>
                </div>
                <div class="price-box">
                    <?php if ($final_price < $old_price): ?>
                        <span class="old-price" style="text-decoration:line-through; color:#999;">₹ <?php echo number_format($old_price, 2); ?></span>
                    <?php endif; ?>
                    <span class="price">₹ <?php echo number_format($final_price, 2); ?></span>
                    <?php if (!empty($user_account_type) && $user_account_type === 'commercial' && $corporate_discount > 0): ?>
                        <p style="color:green; font-weight:bold; margin:0;">Special Commercial Price Applied</p>
                    <?php endif; ?>
                </div>
                <!-- <div class="stock-info">
                    <small class="text-muted">Stock: <?php echo $row['stock']; ?> available</small>
                </div> -->
            </div>
        </div>
    </div>
<?php } // End foreach ?>
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
										<?php endforeach; ?>

										
										
				<!-- <div class="shop-pagination">
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

							

						</div>
					</div>
				</div> -->
			</div>
		</div>
	</div>
	</div>

	<?php
$total_pages = ceil($total_products / $limit);
if ($total_pages > 1): ?>
<div class="shop-pagination">
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li><a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">&laquo;</a></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li><a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">&raquo;</a></li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>

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

	<?php include('footer.php'); ?>
	
	<!-- footer-end -->
	<!-- modal start -->
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			
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


	
</body>
<?php include 'footer.php'; ?>

</html>