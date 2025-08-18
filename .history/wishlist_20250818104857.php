<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error elegantly
    header("Location: loginSignUp/login.php");
    exit;
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

    header("Location: wishlist.php?status=vonia_added");
    exit;
}

// Handle Remove from Wishlist
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $remove = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $remove->bind_param("ii", $user_id, $product_id);
    $remove->execute();
    header("Location: wishlist.php?status=vonia_removed");
    exit;
}

// Fetch wishlist items from both tables
$wishlist_items = [];

// First, get all wishlist entries
$wishlist_stmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
$wishlist_stmt->bind_param("i", $user_id);
$wishlist_stmt->execute();
$wishlist_result = $wishlist_stmt->get_result();

while ($wishlist_row = $wishlist_result->fetch_assoc()) {
    $product_id = $wishlist_row['product_id'];
    $product = null;
    
    // Try to find product in products table
    $product_stmt = $conn->prepare("SELECT id, product_name, price, images, stock FROM products WHERE id = ?");
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
    $product = $product_result->fetch_assoc();
    
    // If not found in products, try home_daily_deal table
    if (!$product) {
        $deal_stmt = $conn->prepare("SELECT id, product_name, price, images, stock FROM home_daily_deal WHERE id = ?");
        $deal_stmt->bind_param("i", $product_id);
        $deal_stmt->execute();
        $deal_result = $deal_stmt->get_result();
        $product = $deal_result->fetch_assoc();
    }
    
    if ($product) {
        $image_array = json_decode($product['images'], true);
        $image = isset($image_array[0]) ? $image_array[0] : 'default.jpg';

        $wishlist_items[] = [
            'id' => $product['id'],
            'name' => $product['product_name'],
            'price' => $product['price'],
            'image' => $image,
            'stock' => $product['stock']
        ];
    }
}

$totalItems = count($wishlist_items);
?>

<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>My Wishlist (<?= $totalItems ?>) || Vonia</title>
    <meta name="description" content="Your personal wishlist on Vonia.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link href='https://fonts.googleapis.com/css?family=Inter:400,500,600,700,800&display=swap' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:700,800,900&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" href="css/meanmenu.min.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/nivo-slider.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="wishlist.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="vonia-breadcrumb-area vonia-bg-light-gray vonia-ptb-50" data-aos="fade-down">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="vonia-breadcrumb-content vonia-text-center">
                        <h2>My Wishlist</h2>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li class="vonia-active">Wishlist</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="vonia-wishlist-section vonia-pt-100 vonia-pb-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="vonia-wishlist-wrapper" data-aos="fade-up" data-aos-delay="200">
                        <h3 class="vonia-wishlist-heading">Your Favorite Items (<?= $totalItems ?>)</h3>
                        <?php if (isset($_GET['status']) && $_GET['status'] === 'vonia_added'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-in">
                                Product added to wishlist!
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'vonia_removed'): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert" data-aos="fade-in">
                                Product removed from wishlist.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($wishlist_items)): ?>
                            <div class="vonia-wishlist-table-container table-responsive">
                                <table class="table vonia-wishlist-table">
                                    <thead>
                                        <tr>
                                            <th class="vonia-product-thumbnail-cell">Image</th>
                                            <th class="vonia-product-name-cell">Product Name</th>
                                            <th class="vonia-product-price-cell">Price</th>
                                            <th class="vonia-product-stock-cell">Stock Status</th>
                                            <th class="vonia-product-add-to-cart-cell">Add to Cart</th>
                                            <th class="vonia-product-remove-cell">Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($wishlist_items as $index => $item): ?>
                                            <tr data-aos="fade-up" data-aos-delay="<?= 300 + ($index * 50) ?>">
                                                <td class="vonia-product-thumbnail-cell" data-label="Image">
                                                    <a href="product-details.php?id=<?= $item['id'] ?>">
                                                        <img src="./admin/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                                    </a>
                                                </td>
                                                <td class="vonia-product-name-cell" data-label="Product Name">
                                                    <a href="product-details.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
                                                </td>
                                                <td class="vonia-product-price-cell" data-label="Price">
                                                    <span class="vonia-amount-price">₹<?= number_format($item['price'], 2) ?></span>
                                                </td>
                                                <td class="vonia-product-price-cell " data-label="Price" >
                                                    <span class="vonia-amount-price">₹<?= number_format($item['price'], 2) ?></span>
                                                </td>
                                                <td class="vonia-product-stock-cell" data-label="Stock Status">
                                                    <?php if ($item['stock'] > 0): ?>
                                                        <span class="vonia-badge vonia-badge-success">In Stock</span>
                                                    <?php else: ?>
                                                        <span class="vonia-badge vonia-badge-danger">Out of Stock</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="vonia-product-add-to-cart-cell" data-label="Add to Cart">
                                                    <?php if ($item['stock'] > 0): ?>
                                                        <a href="shopping-cart.php?action=add&id=<?= $item['id'] ?>" class="vonia-btn vonia-btn-primary vonia-btn-add-to-cart" title="Add to cart">
                                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="vonia-btn vonia-btn-secondary vonia-btn-unavailable" disabled>
                                                            <i class="fas fa-ban"></i> Unavailable
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="vonia-product-remove-cell" data-label="Remove">
                                                    <a href="wishlist.php?action=remove&id=<?= $item['id'] ?>" class="vonia-btn vonia-btn-danger vonia-btn-remove-item" title="Remove from wishlist">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="vonia-empty-state" data-aos="zoom-in" data-aos-delay="300">
                                <i class="far fa-heart"></i>
                                <p>Your wishlist is looking a little empty!</p>
                                <p>Start Browse our amazing products and add your favorites here.</p>
                                <a href="shop.php" class="vonia-btn vonia-btn-primary vonia-mt-4">Start Shopping</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="brand-area owl-carousel-space">
        <div class="container">
            <div class="row">
                <div class="brands">
                    <div class="brand-carousel">
                        <div class="col-md-12">
                            <div class="single-brand">
                                <a href="#"><img src="img/brand/1.jpg" alt="" /></a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-brand">
                                <a href="#"><img src="img/brand/2.jpg" alt="" /></a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-brand">
                                <a href="#"><img src="img/brand/3.jpg" alt="" /></a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-brand">
                                <a href="#"><img src="img/brand/4.jpg" alt="" /></a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-brand">
                                <a href="#"><img src="img/brand/5.jpg" alt="" /></a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-brand">
                                <a href="#"><img src="img/brand/6.jpg" alt="" /></a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-brand">
                                <a href="#"><img src="img/brand/7.jpg" alt="" /></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.scrollUp.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.meanmenu.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/jquery.nivo.slider.pack.js"></script>
    <script src="js/countdown.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS library
        AOS.init({
            duration: 800, // global duration for animations
            once: true,    // whether animation should happen only once - default
        });

        // Optional: Trigger animations on page load for initial elements
        // This might be handled well by AOS itself, but if you want specific immediate effects,
        // you could add simple CSS @keyframes for elements like .vonia-wishlist-heading
        // and add a class like 'loaded' to the body after DOMContentLoaded.

        document.addEventListener('DOMContentLoaded', function() {
            // Example of a simple fade-in for the main wishlist container on page load
            // This is primarily done via AOS 'fade-up' on .vonia-wishlist-wrapper
            // but you can add custom CSS animations if you want a different effect.
        });
    </script>
</body>
</html>