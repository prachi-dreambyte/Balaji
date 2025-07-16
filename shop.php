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
$category_name = isset($_GET['category']) ? trim($_GET['category']) : '';

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


// Get category name if category_id is provided
// $category_name = '';
// if (!empty($category_id)) {
//     // First, get the category name
//     $cat_sql = "SELECT category_name FROM categories WHERE  = ?";
//     $cat_stmt = $conn->prepare($cat_sql);
//     $cat_stmt->bind_param("i", $category_id);
//     $cat_stmt->execute();
//     $cat_result = $cat_stmt->get_result();
    
//     if ($cat_result->num_rows > 0) {
//         $category_name = $cat_result->fetch_assoc()['category_name'];
//     }
//     $cat_stmt->close();
// }

// Debug information (remove this in production)
// $debug_info = [];
// $debug_info['category_id'] = $category_id;
// $debug_info['category_name'] = $category_name;

// Fetch products based on product name + category name
$like = "%$search_term%";

if (!empty($category_name) && !empty($search_term)) {
    // Search by product name + category name
    $sql = "SELECT * FROM products WHERE category = ? AND product_name LIKE ?" . $order_by . " LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $category_name, $like, $limit);
} elseif (!empty($category_name)) {
    // Filter by category name only
    $sql = "SELECT * FROM products WHERE category = ?" . $order_by . " LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $category_name, $limit);
}elseif (!empty($search_term)) {
    // Search by product name or category name
    $sql = "SELECT * FROM products WHERE product_name LIKE ? OR category LIKE ?" . $order_by . " LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $like, $like, $limit);
}

 else {
    // No search and no category - show all products
    $sql = "SELECT * FROM products" . $order_by . " LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
}





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
		<!-- responsive css -->
        <link rel="stylesheet" href="css/responsive.css">
		<!-- modernizr js -->
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
		
		<!-- header-start -->
		 <?php include 'header.php'; ?>
			<!-- mainmenu-area-end -->
			<!-- mobile-menu-area-start -->
			<!-- <div class="mobile-menu-area d-lg-none d-block">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="mobile_menu">
								<nav id="mobile_menu_active">
									<ul>
										<li><a href="index.php">Home</a> -->
											<!-- <ul>
												<li><a href="index11.php">Home 1</a></li>
												<li><a href="index-2.php">Home 2</a></li>
												<li><a href="index-3.php">Home 3</a></li>
												<li><a href="index.php">Home 4</a></li>
											</ul> -->
										<!-- </li>
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
</li> -->
<!-- <li><a href="shop.php">Kitchen & Bar</a>
											<ul>
												<li><a href="#">Bags</a>
													<ul>
														<li><a href="#">Boots Bags</a></li>
														<li><a href="#">Blazers</a></li>
														<li><a href="#">Sweaters</a></li>
														<li><a href="#">Hoodies</a></li>
													</ul>											
												</li>
												<li><a href="#">Tops & Tees</a>
													<ul>
														<li><a href="#">Long Sleeve</a></li>
														<li><a href="#">Short sleeves</a></li>
														<li><a href="#">Polo short sleeves</a></li>
														<li><a href="#">Short Sleevs</a></li>
													</ul>											
												</li>
												<li><a href="#">Lingerie</a>
													<ul>
														<li><a href="#">Bands</a></li>
														<li><a href="#">CATEGORY</a></li>
														<li><a href="#">Wedges</a></li>
														<li><a href="#">Vests</a></li>
													</ul>											
												</li>
											</ul>
										</li> -->
										<!-- <li><a href="shop.php">OFFER</a> -->
											<!-- <ul>
												<li><a href="#">Footwear Man</a>
													<ul>
														<li><a href="#">Gold Ring</a></li>
														<li><a href="#">Platinum Rings</a></li>
														<li><a href="#">Silver Ring</a></li>
														<li><a href="#">Tungsten Ring</a></li>
													</ul>											 -->
												<!-- </li>
												<li><a href="#">Footwear Womens</a>
													<ul>
														<li><a href="#">Bands Gold</a></li>
														<li><a href="#">Platinum Bands</a></li>
														<li><a href="#">Silver Bands</a></li>
														<li><a href="#">Tungsten Bands</a></li>
													</ul>											
												</li>
											</ul> -->
										<!-- </li>
										<li>
                                          <a href="contact.php">CONTACT</a>
                                        </li>
										<li><a href="#">ABOUT</a>
											<ul>
												<li><a href="blog.php">Blog</a></li>
												<li><a href="contact-us.php">Contact Us</a></li>
												<li><a href="checkout.php">Checkout</a></li>
												<li><a href="my-account.php">My account</a></li>
												<li><a href="product-details.php">Product details</a></li>
												<li><a href="shop.php">Shop Page</a></li>
												<li><a href="shopping-cart.php">Shoping Cart</a></li>
												<li><a href="wishlist.php">Wishlist</a></li>
												<li><a href="404.php">404 Error</a></li>
											</ul>
										</li>
									</ul>
								</nav>
							</div>
						</div>
					</div>
				</div>
			</div> -->
			<!-- mobile-menu-area-end -->
		</header>
		
		<!-- header-end -->

		<!-- <form method="GET" id="sortForm" style="margin-bottom: 20px;">
    <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
    <label for="sort_by"><strong>Sort By:</strong></label>
    <select name="sort_by" id="sort_by" onchange="document.getElementById('sortForm').submit()">
        <option value="">Default</option>
        <option value="price_asc" <?php if ($_GET['sort_by'] == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
        <option value="price_desc" <?php if ($_GET['sort_by'] == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
        <option value="name_asc" <?php if ($_GET['sort_by'] == 'name_asc') echo 'selected'; ?>>Name: A to Z</option>
        <option value="name_desc" <?php if ($_GET['sort_by'] == 'name_desc') echo 'selected'; ?>>Name: Z to A</option>
    </select>
</form> -->



		<!-- shop-2-area-start -->
		<div class="shop-2-area">
			<div class="container">
				
				<div class="breadcrumb">
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
				</div>
				<div class="row">
					<div class="left-column col-sm-3">
						<div class="left-column-block">
							<h1>Catalog</h1>
							<div class="block-content">
								<div class="content-box">
									<h3 class="content-box-heading">
										Categories
									</h3>
									<ul>
										<?php foreach ($categories as $cat): ?>
											<li class="<?php echo ($category_id == $cat['id']) ? 'active' : ''; ?>">
												<span class="checkit">
													<input class="checkbox" type="checkbox" <?php echo ($category_id == $cat['id']) ? 'checked' : ''; ?>>
												</span>
												<label class="check-label">
													<a href="shop.php?category_id=<?php echo intval($cat['id']); ?>">
														<?php echo htmlspecialchars($cat['category_name']); ?>
													</a>
												</label>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
								<div class="content-box">
									<h3 class="content-box-heading">
										Availability
									</h3>
									<ul>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">In stock (13)</a>
											</label>
										</li>
									</ul>
								</div>
								<div class="content-box">
									<h3 class="content-box-heading">
										Condition
									</h3>
									<ul>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">New (13)</a>
											</label>
										</li>
									</ul>
								</div>
								<div class="content-box">
									<h3 class="content-box-heading">
										Manufacturer
									</h3>
									<ul>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">Fashion Manufacturer (13)</a>
											</label>
										</li>
									</ul>
								</div>
								<div class="content-box">
									<h3 class="content-box-heading">price</h3>
									<div class="info_widget">
										<div class="price_filter">
											<div id="slider-range"></div>
												<div class="price_slider_amount">
												<input type="text" id="amount" name="price"  placeholder="Add Your Price" />
												<input type="submit"  value="Range"/>  
											</div>
										</div>
									</div>	
								</div>
								<!-- <div class="content-box">
									<h3 class="content-box-heading">Size</h3>
									<ul>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">S (13)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">L (13)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">M (13)</a>
											</label>
										</li>
									</ul>
								</div>
								<div class="content-box">
									<h3 class="content-box-heading">
										Color
									</h3>
									<ul>
										<li>
											<input class="color-option Beige" type="checkbox">
											<label class="check-label">
												<a href="#">Beige (2)</a>
											</label>
										</li>
										<li>
											<input class="color-option white" type="checkbox">
											<label class="check-label">
												<a href="#">White (4)</a>
											</label>
										</li>
										<li>
											<input class="color-option black" type="checkbox">
											<label class="check-label">
												<a href="#">Black (4)</a>
											</label>
										</li>
										<li>
											<input class="color-option orange" type="checkbox">
											<label class="check-label">
												<a href="#">Orange (5)</a>
											</label>
										</li>
										<li>
											<input class="color-option blue" type="checkbox">
											<label class="check-label">
												<a href="#">Blue (3)</a>
											</label>
										</li>
										<li>
											<input class="color-option green" type="checkbox">
											<label class="check-label">
												<a href="#">Green (2)</a>
											</label>
										</li>
										<li>
											<input class="color-option yellow" type="checkbox">
											<label class="check-label">
												<a href="#">Yellow (6)</a>
											</label>
										</li>
										<li>
											<input class="color-option pink" type="checkbox">
											<label class="check-label">
												<a href="#">Pink (2)</a>
											</label>
										</li>
									</ul>  
								</div> -->
								<div class="content-box">
									<h3 class="content-box-heading">
										Compositions
									</h3>
									<ul>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">Cotton (5)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">Polyester (4)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">Viscose (4)</a>
											</label>
										</li>
									</ul>
								</div>
								<div class="content-box">
									<h3 class="content-box-heading">
										Styles
									</h3>
									<ul>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">Casual (5)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#"> Dressy (2)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#"> Girly (6)</a>
											</label>
										</li>
									</ul>
								</div>
								<div class="content-box">
									<h3 class="content-box-heading">
										Properties
									</h3>
									<ul>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">Colorful Dress (2)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">Maxi Dress (2)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">Midi Dress (2)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#">Short Dress (4)</a>
											</label>
										</li>
										<li>
											<span class="checkit">
												<input class="checkbox" type="checkbox">
											</span>
											<label class="check-label">
												<a href="#"> Short Sleeve (3) </a>
											</label>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="left-column-block left-col-mar">
							<h1>Tags</h1>
							<div class="tags">
								<a href="#">new</a>
								<a href="#">fashion</a>
								<a href="#">CATEGORY</a>
								<a href="#">sale</a>
								<a href="#">accessories</a>
								<a href="#">lighting</a>
							</div>
						</div>
					</div>
					<div class="col-sm-9">
						<div class="shop-banner"></div>
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

								
                                     <div class="compare">
                                        <a href="compare.php "> compare (<span class="compare-count"><?php echo $compare_count; ?></span>) </a>
                                         <i class="fa fa-angle-right"></i>
                                     </div>

							</div>
							<div class="shop-category-product" id="product-list">

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
					<a href="#">
						<img src="./admin/<?php echo $firstImage ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" />
					</a>
					<span class="new">new</span>
					
					<div class="product-action">
						<div class="add-to-links">
							<ul>
								<li class="cart">
                                <a href="shopping-cart.php?action=add&id=<?php echo $row['id']; ?>" title="Add to cart">
                                 <i class="fa fa-shopping-cart"></i>
                                 <span>add to cart</span>
                                </a>
                               </li>

								<li>
									<a href="#" title="Add to wishlist">
										<i class="fa fa-heart" aria-hidden="true"></i>
									</a>
								</li>
								<!-- <li>
									<a href="#" title="Add to compare">
										<i class="fa fa-bar-chart" aria-hidden="true"></i>
									</a>
								</li> -->
								<li>
                                    <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
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
				</div>
			</div>
		</div>
	<?php } ?>

											</div>
										</div>
										<div role="tabpanel" class="tab-pane fade" id="list_view">
											<div class="list-view">
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4 col-sm-5">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/faded-short-sleeves-tshirt.jpg" alt="" />
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
													</div>
													<div class="col-md-8 col-sm-7">
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
															<p class="product-desc"> Faded short sleeves t-shirt with high neckline. 
															Soft and stretchy material for a comfortable fit. Accessorize 
															with a straw hat and you're ready for summer!
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
																	<li>
                                                                    <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                        <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                     </a>
                                                                    </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/vass.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title="Blouse">Blouse</a>
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
																<span class="price"> £ 30.78 </span>
																<span class="old-price"> £ 3.40 </span>
															</div>
															<p class="product-desc">Short-sleeved blouse with feminine draped sleeve detail.
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
																	<!-- <li>
																		<a href="#" title="Add to compare">
																			<i class="fa fa-bar-chart" aria-hidden="true"></i>
																		</a>
																	</li> -->
																	<li>
                                                                      <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                            <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                        </a>
                                                                    </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/cooks.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title=" Faded Short Sleeves T-shirt "> Faded Short Sleeves T-shirt </a>
															</h5>
															<div class="reviews">
																<div class="star-content clearfix">
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star"></span>
																</div>
																<div class="comment">
																	<span class="reviewcount">1</span>
																	Review(s)
																</div>
															</div>
															<div class="price-box">
																<span class="price"> £ 28.70 </span>
																<span class="old-price"> £ 31.20 </span>
															</div>
															<p class="product-desc">100% cotton double printed dress. Black and white 
															striped top and orange high waisted skater skirt bottom.
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
																	<!-- <li>
																		<a href="#" title="Add to compare">
																			<i class="fa fa-bar-chart" aria-hidden="true"></i>
																		</a>
																	</li> -->
																	 <li>
                                                                       <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                           <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                       </a>
                                                                     </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
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
													</div>
													<div class="col-md-8">
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
																	<li>
                                                                      <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                            <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                          </a>
                                                                      </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/summer-dress.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title="Printed Summer Dress">Printed Summer Dress </a>
															</h5>
															<div class="reviews">
																<div class="star-content clearfix">
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star"></span> 
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
															<p class="product-desc">100% cotton double printed dress. Black and white 
															striped top and orange high waisted skater skirt bottom.
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
																	<li>
                                                                      <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                         <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                      </a>
                                                                    </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/lamp2.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
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
																<span class="price"> £ 36.60 </span>
															</div>
															<p class="product-desc">100% cotton double printed dress. Black and white 
															striped top and orange high waisted skater skirt bottom.
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
																	<li>
                                                                        <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                          <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                        </a>
                                                                    </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/been.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title=" Faded Short Sleeves T-shirt "> Faded Short Sleeves T-shirt </a>
															</h5>
															<div class="reviews">
																<div class="star-content clearfix">
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star"></span>
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
															<p class="product-desc">100% cotton double printed dress. Black and white 
															striped top and orange high waisted skater skirt bottom.
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
																	<li>
                                                                   <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                     <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                     </a>
                                                                      </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/blouse.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
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
																<span class="price"> £ 32.40 </span>
															</div>
															<p class="product-desc">100% cotton double printed dress. Black and white 
															striped top and orange high waisted skater skirt bottom.
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
																	<li>
                                                                      <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                           <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                       </a>
                                                                    </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/cup.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title="Printed Summer Dress ">Printed Summer Dress </a>
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
															<p class="product-desc">100% cotton double printed dress. Black and white 
															striped top and orange high waisted skater skirt bottom.
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
																	<li>
                                                                      <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                            <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                          </a>
                                                                    </li>

																	</li>
																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/printed-dress.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
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
																<span class="price"> £ 55.07 </span>
															</div>
															<p class="product-desc">100% cotton double printed dress. Black and white 
															striped top and orange high waisted skater skirt bottom.
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
																	<li>
                                                                       <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                            <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                        </a>
                                                                    </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/printed-summer-dress.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title=" Faded Short Sleeves T-shirt "> Faded Short Sleeves T-shirt </a>
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
																<span class="old-price"> £ 40.10 </span>
															</div>
															<p class="product-desc">100% cotton double printed dress. Black and white 
															striped top and orange high waisted skater skirt bottom.
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
																	<li>
                                                                     <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                        <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                     </a>
                                                                    </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
												<div class="list-view-single row list-view-mar">
													<div class="col-md-4">
														<div class="single-product">
															<div class="product-img">
																<a href="#">
																	<img src="img/tab-pro/printed-chiffon-dress.jpg" alt="" />
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
													</div>
													<div class="col-md-8">
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
																<span class="price"> £ 28.70 </span>
															</div>
															<p class="product-desc">100% cotton double printed dress. Black and white 
															striped top and orange high waisted skater skirt bottom.
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
																	<li>
                                                                      <a href="#" class="add-to-compare" data-id="<?php echo $row['id']; ?>" title="Add to compare">
                                                                         <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                                      </a>
                                                                    </li>

																</ul>
															</div>
															<span class="availability">
																<span> In stock </span>
															</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
							</div>
						</div>
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
									
                                <div class="compare">
                                <a href="compare.php"> compare (<span class="compare-count"><?php echo $compare_count; ?></span>) </a>
                                    <i class="fa fa-angle-right"></i>
                                </div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
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
		 <?php include 'footer.php'; ?>
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
											<input id="quantity_wanted" class="text" type="text" value="1" >
										
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
            data: { product_id: productId },
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


</html>
