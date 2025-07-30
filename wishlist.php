<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login to access your wishlist.");
}

// ✅ Create wishlist table if not exists
$createTableQuery = "
CREATE TABLE IF NOT EXISTS wishlist (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";
$conn->query($createTableQuery);


$user_id = $_SESSION['user_id'];

// Handle Add to Wishlist
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Check if already in wishlist
    $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();
    }

    header("Location: wishlist.php?added=1");
    exit;
}

// Handle Remove from Wishlist
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $remove = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $remove->bind_param("ii", $user_id, $product_id);
    $remove->execute();
    header("Location: wishlist.php?removed=1");
    exit;
}

// Fetch wishlist items
// Fetch cart items from cart table
$stmt = $conn->prepare("SELECT p.id, p.product_name, p.price, p.images, p.stock FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_result = $stmt->get_result();
$totalItems = $wishlist_result->num_rows;
$wishlist_items = [];

while ($row = $wishlist_result->fetch_assoc()) {
    $image_array = json_decode($row['images'], true);
    $image = isset($image_array[0]) ? $image_array[0] : 'default.jpg';

    $wishlist_items[] = [
        'id' => $row['id'],
        'name' => $row['product_name'],
        'price' => $row['price'],
        'image' => $image,
		'stock' => $row['stock']
    ];
}
?>



<!doctype html>
<html class="no-js" lang="">
    

<head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Wishlist || Vonia</title>
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
		<link rel="stylesheet" href="wishlist.css">
		 <link rel="stylesheet" href="header.css">
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
		<!-- wishlist-start -->
<div class="wishlist-area">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						
							<h1 class="entry-title">My Wishlist (<?= $totalItems ?>)</h1>
		
						
						<div class="wishlist-content">
							<form action="#">
								<div class="wishlist-table table-responsive">
									<table>
										<thead>
											<tr>
												<th class="product-thumbnail">Image</th>
												<th class="product-name">
													<span class="nobr">Product Name</span>
												</th>
												<th class="product-price">
													<span class="nobr">Price </span>
												</th>
												<th class="product-stock-stauts">
													<span class="nobr"> Stock </span>
												</th>
												<th class="product-add-to-cart">
													<span class="nobr">add-to-cart </span>
												</th>
												<th class="product-remove">
													<span class="nobr">Remove</span>
												</th>
											</tr>
										</thead>
										<tbody>
                                          <?php if (!empty($wishlist_items)): ?>
                           <?php foreach ($wishlist_items as $item): ?>
        <tr>
            <td class="product-thumbnail">
                <a href="product-detail.php?id=<?= $item['id'] ?>">
                    <img src="./admin/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="80">
                </a>
            </td>
            <td class="product-name">
                <a href="product-detail.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
            </td>
            <td class="product-price">
                <span class="amount">₹<?= number_format($item['price'], 2) ?></span>
            </td>
            <td class="product-stock-status">
                <span class="wishlist-in-stock"><?= $item['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?></span>
            </td>
            <td class="product-add-to-cart">
                <?php if ($item['stock'] > 0): ?>
					<a href="shopping-cart.php?action=add&id=<?= $item['id'] ?>" title="Add to cart">Add to Cart</a>
                <?php else: ?>
                    <span class="text-muted">Unavailable</span>
                <?php endif; ?>
            </td>
            <td class="product-remove">
				<a href="wishlist.php?action=remove&id=<?= $item['id'] ?>"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                                                </svg></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6">🛒 Your wishlist is empty.</td></tr>
<?php endif; ?>
</tbody>

									</table>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- wishlist-end -->
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
    </body>


</html>