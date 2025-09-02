<?php
session_start();
$compare_count = isset($_SESSION['compare_list']) ? count($_SESSION['compare_list']) : 0;
include 'connect.php';

// Initialize the cart if needed
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// AJAX Suggestions handler
if (isset($_GET['suggest']) && !empty($_GET['suggest'])) {
	$suggest = "%" . trim($_GET['suggest']) . "%";

	$sql = "SELECT id, product_name 
            FROM products 
            WHERE product_name LIKE ? 
               OR short_description LIKE ?  
               OR tags LIKE ? 
            LIMIT 10";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("sss", $suggest, $suggest, $suggest);
	$stmt->execute();
	$result = $stmt->get_result();

	$suggestions = [];
	while ($row = $result->fetch_assoc()) {
		// Limit product name to max 3 words for Google-like clean look
		$words = explode(" ", $row['product_name']);
		$shortName = implode(" ", array_slice($words, 0, 3));

		$suggestions[] = [
			"id" => $row['id'],
			"name" => $shortName
		];
	}

	header('Content-Type: application/json');
	echo json_encode($suggestions);
	exit;
}


// ✅ Function to fetch banners dynamicall-
function getCategoryBanners($conn, $category_name = '')
{
	if (!empty($category_name)) {
		// Single category
		$stmt = $conn->prepare("SELECT banner_image, category_name FROM categories WHERE category_name = ?");
		$stmt->bind_param("s", $category_name);
		$stmt->execute();
		$result = $stmt->get_result();
	} else {
		// All categories
		$result = $conn->query("SELECT banner_image, category_name FROM categories WHERE banner_image IS NOT NULL ORDER BY created_at DESC");
	}

	$banners = [];
	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$banners[] = [
				"name" => $row['category_name'],
				"image" => !empty($row['banner_image']) ? 'admin/' . $row['banner_image'] : 'assets/images/placeholder.png'
			];
		}
	}
	return $banners;
}

// ✅ Get category from URL
$currentCategory = isset($_GET['category']) ? urldecode($_GET['category']) : '';

// ✅ Fetch banners
$bannerData = getCategoryBanners($conn, $currentCategory);

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

$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search_term)) {
	// Normalize text (lowercase and trim)
	$search_term = strtolower($search_term);

	// Handle "under/below 1000" type searches
	if (preg_match('/(?:under|below)\s+(\d+)/i', $search_term, $matches)) {
		$price_limit = (int) $matches[1];
		$conditions[] = "price <= ?";
		$params[] = $price_limit;
		$types[] = 'i';

		// Remove that part from search
		$search_term = preg_replace('/(?:under|below)\s+\d+/i', '', $search_term);
		$search_term = trim($search_term);
	}

	// Remove filler/common words from search (best, cheap, buy, for, etc.)
	$search_term = preg_replace('/\b(best|cheap|buy|for|top|latest|new|deal|offers?)\b/i', '', $search_term);
	$search_term = trim(preg_replace('/\s+/', ' ', $search_term)); // clean spaces

	if (!empty($search_term)) {
		// Split into keywords (so "office chair" becomes ['office','chair'])
		$keywords = explode(' ', $search_term);

		$like_conditions = [];
		foreach ($keywords as $word) {
			if (!empty($word)) {
				$like_conditions[] = "(product_name LIKE ? 
                                    OR category LIKE ? 
                                    OR short_description LIKE ? 
                                    OR description LIKE ? 
                                    OR hashtags LIKE ?)";
				$params = array_merge($params, array_fill(0, 5, "%$word%"));
				$types[] = 'sssss';
			}
		}

		if (!empty($like_conditions)) {
			$conditions[] = '(' . implode(' AND ', $like_conditions) . ')';
		}
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
$sql = "SELECT * FROM products" . $where . " ORDER BY RAND() LIMIT ?, ?";

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

.banner-container {
    position: relative;
    width: 100%;
    height: auto; /* Let the image define height */
    overflow: hidden;
}

.swiper {
    width: 100%;
    height: auto; /* Auto height */
}

.swiper-slide {
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-banner-img {
    width: 100%;
    height: auto; /* Keep natural aspect ratio */
    max-height: 600px; /* Optional: Limit height on large screens */
    object-fit: cover;
    border-radius: 6px;
}
.swiper-button-next,
.swiper-button-prev {
	
    color: #fff;
    font-weight: bold;

}

.swiper-pagination-bullet {
    background: #fff !important;
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
    .category-banner-img {
        max-height: 300px; /* Smaller height for mobile */
    }

    .breadcrumbs {
        font-size: 0.9rem;
    }
}
</style>

<body>
	<!-- Marquee Start -->
<div style="background: #845848;
    color: #fff;
    font-size: 14px;
    padding: 6px 0;
    text-align: center;">
    <marquee behavior="scroll" direction="left" scrollamount="5">
        100% MONEY BACK GUARANTEE &nbsp; | &nbsp; FREE SHIPPING ON ORDER OVER ₹3000 &nbsp; | &nbsp; ONLINE SUPPORT 24/7
    </marquee>
</div>
<!-- Marquee End -->
	<!-- header-start -->
	<?php include 'header.php'; ?>
	</header>

	<!-- header-end -->
<!-- Dynamic Category Banner Section -->
<section class="category-banner-section">
    <div class="banner-container swiper mySwiper">
        <div class="swiper-wrapper">
            <?php foreach ($bannerData as $banner): ?>
				<div class="swiper-slide">
					<a href="shop.php?category=<?php echo urlencode($banner['name']); ?>#product-list">
					<img src="<?php echo $banner['image']; ?>" alt="<?php echo htmlspecialchars($banner['name']); ?>"
						class="category-banner-img">
						</a>
					<div class="banner-overlay">
						
						<div class="breadcrumbs">
							<a href="index.php">Home</a> 
							<a href="shop.php">Categories</a>
							<?php if ($currentCategory): ?>
								 <span><?php echo htmlspecialchars($banner['name']); ?></span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Swiper Controls -->
		<div class="swiper-pagination text-body-tertiary"></div>
		<div class="swiper-button-next text-body-tertiary"></div>
		<div class="swiper-button-prev text-body-tertiary"></div>
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
							<h3 class="content-box-heading" style="font-size:17px;">
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
						<div class="info_widget">
  <h4 class="filter_heading">Price Range</h4>
  <div class="price_filter">
    <!-- Slider -->
    <div id="slider-range"></div>

    <!-- Price Display -->
    <div class="price_slider_amount">
      <input type="text" id="amount" name="price" placeholder="Select Price Range" readonly />
    </div>
  </div>
</div>

<style>
/* Widget box */
.info_widget {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 10px;
  padding: 18px 15px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* Heading */
.filter_heading {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 12px;
  color: #333;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Price Filter */
.price_filter {
  width: 100%;
}

/* Slider track */
#slider-range {
  height: 6px;
  background: #e5e5e5;
  border-radius: 4px;
  margin: 10px 5px 20px;
  position: relative;
}

/* Active range fill */
#slider-range .ui-slider-range {
  background: #333;
  border-radius: 4px;
}

/* Slider handles */
#slider-range .ui-slider-handle {
  top: -6px;
  height: 18px;
  width: 18px;
  border-radius: 50%;
  background: #333;
  border: none;
  cursor: pointer;
  box-shadow: 0 3px 6px rgba(0,0,0,0.2);
  transition: all 0.2s ease-in-out;
}
#slider-range .ui-slider-handle:hover {
  background: #000;
  transform: scale(1.1);
}

/* Price box */
.price_slider_amount input {
  width: 100%;
  text-align: center;
  padding: 10px;
  font-size: 14px;
  color: #333;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #fafafa;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
}
.price_slider_amount input:focus {
  outline: none;
  border-color: #333;
  box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
}
</style>


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

	$review_query = "
    SELECT 
        COUNT(rating) AS total_reviews, 
        ROUND(AVG(rating), 1) AS avg_rating 
    FROM reviews 
    WHERE product_id = $product_id";

	$review_result = mysqli_query($conn, $review_query);
	$reviewData = mysqli_fetch_assoc($review_result);
	$total_reviews = $reviewData['total_reviews'] ?? 0;
	$avg_rating = $reviewData['avg_rating'] ?? 0;


	// Pricing
$price = isset($row['price']) ? floatval($row['price']) : 0;
$discount = isset($row['discount']) ? floatval($row['discount']) : 0;
$corporate_discount = isset($row['corporate_discount']) ? floatval($row['corporate_discount']) : 0;

$old_price = $price;
$final_price = $price;

// Apply flat discount if available
if ($discount > 0) {
    $final_price = $price - $discount;
} elseif ($corporate_discount > 0 && $user_account_type === "corporate") {
    $final_price = $price - $corporate_discount;
}


    // Images
    $images = json_decode($row['images'], true);
    $firstImage = is_array($images) && !empty($images) ? $images[0] : 'default.jpg';

?>
    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6 mb-4">

        <div class="single-product">
           <div class="product-img" style="position: relative; overflow: hidden;">
    <a href="product-details.php?id=<?php echo $row['id']; ?>" class="product-img-link">
				<img src="./admin/<?php echo htmlspecialchars($firstImage); ?>"
					alt="<?php echo htmlspecialchars($row['product_name']); ?>"
					style="width:100%; display:block; transition: transform 0.6s ease;">
			</a>
		
			<!-- Wishlist Button -->
			<div class="wishlist" style="position: absolute; top: 10px; right: 10px; z-index: 2; font-size: 22px;">
  <a class="wishlistBtn heart" 
     href="wishlist.php?action=add&id=<?php echo $row['id']; ?>" title="Add to wishlist">
					<i class="fa fa-heart"></i>
				</a>
			</div>
			
			<style>
				/* Default transparent heart with black border */
				.heart i {
					color: transparent;
					-webkit-text-stroke: 1.8px black;
					/* border */
					transition: all 0.3s ease;
					display: inline-block;
				}
			
				/* Hover animation */
				.heart:hover i {
					color: rgba(255, 0, 0, 0.6);
					/* light red fill */
					transform: scale(1.2);
					/* enlarge */
					-webkit-text-stroke: 1.8px red;
				}
			
				/* Active (clicked) */
				.heart.active i {
					color: red;
					-webkit-text-stroke: 1.8px red;
					animation: pop 0.3s forwards;
				}
			
				@keyframes pop {
					50% {
						transform: scale(1.5);
					}
			
					100% {
						transform: scale(1);
					}
				}
			</style>
			
			<script>
				document.addEventListener("DOMContentLoaded", function () {
					document.querySelectorAll(".wishlistBtn").forEach(function (btn) {
						btn.addEventListener("click", function (e) {
							this.classList.toggle("active");
							// still goes to wishlist.php
						});
					});
				});
			</script>

		
			<!-- Add to Cart Button (Hidden by default, show on hover) -->
			<div class="add-to-cart-btn">
				<a class="btn btn-danger w-100" href="shopping-cart.php?action=add&id=<?php echo $row['id']; ?>"
					title="Add to cart">
					<i class="fa fa-shopping-cart"></i> Add to Cart
				</a>
			</div>
		</div>
		
		<!-- CSS (can be added in <style> or your CSS file) -->
		<style>
			.product-img-link img {
				transition: transform 0.6s ease;
				/* smooth transition */
			}
		
			.product-img:hover img {
				transform: scale(1.1);
				/* slow zoom effect on hover */
			}
		</style>


            <div class="product-content text-center" style="padding:12px; border:1px solid #eee; border-radius:8px;">
    
    <!-- ✅ Product Name -->
    <h5 class="product-name" style="margin-bottom:8px; font-size:16px; font-weight:600; color:#222;">
        <a href="product-details.php?id=<?php echo $row['id']; ?>"
						title="<?php echo htmlspecialchars($row['product_name']); ?>" style="text-decoration:none; color:inherit;">
						<?php echo htmlspecialchars($row['product_name']); ?>
					</a>
				</h5>
			
				<div class="custom-stars">
    <?php
						if (!empty($avg_rating)) {
							for ($i = 0; $i < 5; $i++) {
								echo $i < round($avg_rating)
									? '<span style="color:#FFD700; font-size:18px;">★</span>'
									: '<span style="color:#ccc; font-size:18px;">★</span>';
							}
							// show average value (optional)
							echo " <span style='font-size:13px; color:#555;'>(" . number_format($avg_rating, 1) . ")</span>";
						} else {
							for ($i = 0; $i < 5; $i++) {
								echo '<span style="color:#ccc; font-size:14px;">★</span>';
							}
						}

						// Show total ratings count
						if (!empty($total_ratings)) {
							echo " <span style='font-size:13px; color:#777;'>• " . $total_ratings . " ratings</span>";
						}
						?>
				</div>

				<!-- 💰 Price Box -->
				<div class="price-box" style="margin-bottom:8px;">
					<div>
						<div class="price" style="font-size:24px; font-weight:500; color:#222;">
							₹<?php echo number_format($final_price, 2); ?>
						</div>
					</div>
					<?php if ($discount > 0):
						$discount_percent = $old_price > 0 ? ($discount / $old_price) * 100 : 0;
						?>
						<span class="old-price" style="text-decoration:line-through; color:#999; font-size:14px; margin-right:6px;">
							₹<?php echo number_format($old_price, 2); ?>
						</span>
			
						<!-- 🔥 Discount Badge -->
						<span class="discount-badge" style="background:#2e7d32; color:#fff; font-size:12px; font-weight:600; 
									 padding:1px 4px; border-radius:4px;">
							-<?php echo round($discount_percent); ?>% OFF
						</span>
					<?php endif; ?>
			
					
			
					<?php if (!empty($user_account_type) && $user_account_type === 'commercial' && $corporate_discount > 0): ?>
						<p style="color:green; font-weight:bold; margin:4px 0 0 0; font-size:13px;">
							Special Commercial Price Applied
						</p>
					<?php endif; ?>
				</div>
			
				<!-- Optional Stock Info -->
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
	<!--<div class="brand-area">-->
	<!--	<div class="container">-->
	<!--		<div class="row">-->
	<!--			<div class="brands">-->
	<!--				<div class="brand-carousel">-->
	<!--					<div class="row">-->
	<!--						<div class="single-brand">-->
	<!--							<a href="#">-->
	<!--								<img src="img/brand/1.jpg" alt="" />-->
	<!--							</a>-->
	<!--						</div>-->
	<!--					</div>-->
	<!--					<div class="col-md-12">-->
	<!--						<div class="single-brand">-->
	<!--							<a href="#">-->
	<!--								<img src="img/brand/2.jpg" alt="" />-->
	<!--							</a>-->
	<!--						</div>-->
	<!--					</div>-->
	<!--					<div class="col-md-12">-->
	<!--						<div class="single-brand">-->
	<!--							<a href="#">-->
	<!--								<img src="img/brand/3.jpg" alt="" />-->
	<!--							</a>-->
	<!--						</div>-->
	<!--					</div>-->
	<!--					<div class="col-md-12">-->
	<!--						<div class="single-brand">-->
	<!--							<a href="#">-->
	<!--								<img src="img/brand/4.jpg" alt="" />-->
	<!--							</a>-->
	<!--						</div>-->
	<!--					</div>-->
	<!--					<div class="col-md-12">-->
	<!--						<div class="single-brand">-->
	<!--							<a href="#">-->
	<!--								<img src="img/brand/5.jpg" alt="" />-->
	<!--							</a>-->
	<!--						</div>-->
	<!--					</div>-->
	<!--					<div class="col-md-12">-->
	<!--						<div class="single-brand">-->
	<!--							<a href="#">-->
	<!--								<img src="img/brand/6.jpg" alt="" />-->
	<!--							</a>-->
	<!--						</div>-->
	<!--					</div>-->
	<!--					<div class="col-md-12">-->
	<!--						<div class="single-brand">-->
	<!--							<a href="#">-->
	<!--								<img src="img/brand/7.jpg" alt="" />-->
	<!--							</a>-->
	<!--						</div>-->
	<!--					</div>-->
	<!--				</div>-->
	<!--			</div>-->
	<!--		</div>-->
	<!--	</div>-->
	<!--</div>-->
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("searchInput");
    const suggestionsBox = document.getElementById("suggestions");

    input.addEventListener("input", function () {
        let query = this.value.trim();
        if (query.length < 2) {
            suggestionsBox.style.display = "none";
            return;
        }

        fetch("shop.php?suggest=" + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                suggestionsBox.innerHTML = "";
                if (data.length === 0) {
                    suggestionsBox.style.display = "none";
                    return;
                }

                data.forEach(item => {
                    let div = document.createElement("div");
                    div.textContent = item.name; // show shortened (max 3 words)
                    div.style.padding = "8px";
                    div.style.cursor = "pointer";
                    div.style.borderBottom = "1px solid #eee";

                    div.addEventListener("click", function () {
                        input.value = item.name;
                        suggestionsBox.style.display = "none";
                        document.getElementById("searchForm").submit();
                    });

                    suggestionsBox.appendChild(div);
                });

                suggestionsBox.style.display = "block";
            })
            .catch(err => console.error(err));
    });

    // Hide when clicking outside
    document.addEventListener("click", function (e) {
        if (!e.target.closest(".search-box")) {
            suggestionsBox.style.display = "none";
        }
    });
});
</script>

<!-- SwiperJS CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
var swiper = new Swiper(".mySwiper", {
    loop: true,
    autoplay: {
        delay: 4000,
        disableOnInteraction: false,
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});
</script>


	
</body>
<?php include 'footer.php'; ?>

</html>