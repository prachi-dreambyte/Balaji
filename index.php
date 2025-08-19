<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'connect.php';

// 🔹 Use account_type from session if available, otherwise fetch from DB
$user_account_type = null;
if (!empty($_SESSION['account_type'])) {
    $user_account_type = strtolower(trim($_SESSION['account_type']));
} elseif (!empty($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT account_type FROM signup WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['account_type'] = $row['account_type'];
        $user_account_type = strtolower(trim($row['account_type']));
    }
    $stmt->close();
}

// Your existing banner code
$slider_banners = [];
$sql = "SELECT * FROM home_banners WHERE type = 'slider' ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $slider_banners = $result->fetch_all(MYSQLI_ASSOC);
}


// Function to get products by tag from database
function getProductsByTag($conn, $tag)
{
    // No quotes here, plain text match
    $searchTerm = '%' . $tag . '%';

    $sql_tag = "SELECT * FROM products WHERE tags LIKE ? ORDER BY created_at DESC LIMIT 8";
    $stmt_tag = $conn->prepare($sql_tag);
    $stmt_tag->bind_param("s", $searchTerm);
    $stmt_tag->execute();
    $result_tag = $stmt_tag->get_result();

    // Optional: Debug
    // if ($result_tag->num_rows === 0) {
    //     echo "<p style='color:red;'>No products found for tag: $tag</p>";
    // }

    return $result_tag->fetch_all(MYSQLI_ASSOC);
}


// Get products for each category
$newArrivalProducts = getProductsByTag($conn, "NEW ARRIVAL");
$onsaleProducts = getProductsByTag($conn, "ONSALE");
$bestsellerProducts = getProductsByTag($conn, "BESTSELLER");
$featuredProducts = getProductsByTag($conn, "FEATURED PRODUCTS");





?>
<!doctype html>
<html class="no-js" lang="">


<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Home Balaji</title>
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
    <!-- style1 css -->
    <link rel="stylesheet" href="style1.css">
    <!-- responsive css -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- modernizr css -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />

    <style>
        /* .single-brand{
        margin-right:30px;
    } */
        .product-tabs-section {
            padding: 60px 0;
            background: #f9f9f9;
        }

        .tab-nav {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .tab-btn {
            background: none;
            border: none;
            font-size: 18px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            padding: 10px 20px;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .tab-btn.active {
            color: #333;
            border-bottom: 2px solid #333;
        }

        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0 40px;
            font-size: 14px;
            color: #666;
        }

        .description-text {
            text-align: center;
            margin-bottom: 40px;
            color: #666;
            line-height: 1.6;
        }

        .single-product {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
            margin-right: 30px !important;
        }

        .single-product:hover {
            transform: translateY(-5px);
        }

        .product-img {
            position: relative;
            overflow: hidden;
            height: 250px;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .single-product:hover .product-img img {
            transform: scale(1.05);
        }

        .badge-new,
        .badge-sale {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .badge-new {
            background: #f47653ff;
        }

        .badge-sale {
            background: #f06548;
        }

        .product-content {
            padding: 20px;
        }

        .product-name {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }

        .product-rating {
            margin-bottom: 10px;
            color: #ffc107;
            font-size: 16px;
        }

        .review-count {
            font-size: 12px;
            color: #666;
            margin-left: 5px;
        }

        .price-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .price {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .old-price {
            font-size: 14px;
            color: #999;
            text-decoration: line-through;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .product-action {
            position: absolute;
            bottom: -50px;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            transition: all 0.3s ease;
            opacity: 0;
        }

        .single-product:hover .product-action {
            bottom: 0;
            opacity: 1;
        }

        .product-action ul {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .product-action ul li {
            margin: 0 5px;
        }

        .product-action ul li a {
            display: inline-block;
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            background: #f8f9fa;
            color: #495057;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .product-action ul li a:hover {
            background: #3b5de7;
            color: white;
        }

        .home-4-latest-blog {
            overflow: hidden !important;
        }

        /* ----------------------READ MORE BUTTON-------------------------------- */
   .readmore-btn {
    font-family: Poppins, sans-serif;
    font-weight: 400;
    display: inline-block;
    position: relative;
    z-index: 0;
    padding: 15px 30px;
    text-decoration: none;
    background: #c06b81 ! important;
    color: white;
    overflow: hidden;
    cursor: pointer;
    text-transform: uppercase;
    border-radius: 10px;
    font-size: 15px
   }
    .readmore-btn:hover{
     background-color: #e393a7 !important;
     color:#fff;
    }

    .extra-text-decoration{
        text-decoration:none;
        font-size: 15px;
    }
    </style>



</head>

<body class="home-4-body">
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- header-start -->
    <div class="wrapper">
        <?php include 'header.php'; ?>
        <!-- heade incr-end -->

        <!-- slider-start -->

        <div class="slider-container">
            <div class="slider">
                <!-- Slider Image -->
                <div id="mainslider" class="nivoSlider slider-image">
                    <?php foreach ($slider_banners as $index => $banner): ?>
                        <img src="./admin/<?php echo htmlspecialchars($banner['image']); ?>" alt="slider"
                            title="#htmlcaption<?php echo $index + 1; ?>" />
                    <?php endforeach; ?>
                </div>

                <?php foreach ($slider_banners as $index => $banner): ?>
                    <div id="htmlcaption<?php echo $index + 1; ?>"
                        class="nivo-html-caption slider-caption-<?php echo $index + 1; ?>">
                        <div class="slider-progress"></div>
                       
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!--=====slider-end=====-->
         
        

        <!--=====special-look-start=====-->
        <div class="home-4-special-look">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="product-title">
                            <h2>
                                <span>Special Look</span>
                            </h2>
                        </div>
                        <div class="banner-content">
                            <div class="col-1">
                                <div class="banner-box">
    <a href="#">
        <video autoplay muted loop playsinline 
               style="width:100%; height:100%; object-fit:cover; border-radius:8px; display:block;">
            <source src="img/body/sl.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </a>
</div>


                            </div>
                            <div class="col-2">
                                <div class="banner-box">
                                    <a href="#">
                                        <img src="img/body/1_4.jpg" alt="" />
                                    </a>
                                </div>
                                <div class="banner-box">
                                    <a href="#">
                                        <img src="img/body/6_2.jpg" alt="" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--=====special-look-end=====-->
       
<!--===== Categories Section =====-->
<!--===== Categories Section =====-->
<section class="home-categories-section py-5">
    <div class="container">
        <div class="product-title text-center mb-4" id="deals">
                    <h2><span>Shop by Category</span></h2>
                </div>

        <div class="row justify-content-center">
            <?php
            // Get all categories from database
            $sql = "SELECT * FROM categories ORDER BY category_name ASC";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $name = htmlspecialchars($row['category_name']);
                    $imageFile = isset($row['category_image']) ? $row['category_image'] : '';
                    $imagePath = 'admin/' . htmlspecialchars($imageFile);

                    // Use placeholder if image doesn't exist
                    if (empty($imageFile) || !file_exists($imagePath)) {
                        $imagePath = 'img/placeholder-category.jpg';
                    }

                    echo '
                    <div class="col-lg-2 col-md-3 col-4 mb-4 text-center">
                        <a href="shop.php?category=' . urlencode($name) . '#product-list" class="category-circle">
                            <div class="circle-img">
                                <img src="' . $imagePath . '" alt="' . $name . '">
                            </div>
                            <p class="category-name mt-2">' . $name . '</p>
                        </a>
                    </div>';
                }
            } else {
                echo '<div class="col-12 text-center"><p>No categories found.</p></div>';
            }
            ?>
        </div>

        <div class="text-center mt-4">
            <a href="shop.php" class="view-all-btn">View All Categories</a>
        </div>
    </div>
</section>

<style>
    /* Categories Section */
    .home-categories-section {
        background-color: #fff;
    }

    .category-circle {
        display: block;
        text-decoration: none;
        color: #000;
        transition: transform 0.2s ease;
    }

    .category-circle:hover {
        transform: translateY(-5px);
        text-decoration: none;
    }

    /* Circle Image */
    /* Circle Image */
    .circle-img {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        border-radius: 50%;
        background-color: #f8f8f8;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        /* makes sure image stays inside circle */
        transition: box-shadow 0.2s ease;
    }

    .circle-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* ensures image fills the circle */
    }


    .category-circle:hover .circle-img {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Category Name */
    .category-name {
        font-size: 14px;
        font-weight: 500;
        margin-top: 10px;
    }

    /* View All Button */
    .view-all-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 12px 24px;
        background-color: #c06b81;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .view-all-btn:hover {
        background-color: #e393a7;
        color: #fff;
        text-decoration: none;
    }
</style>
        <!--=====daily-deals-start=====-->
        <div class="home-4-daily-deals-area py-5">
            <!-- <div class="container">
                <div class="product-title text-center mb-4" id="deals">
                    <h2><span>Daily Deals</span></h2>
                </div>
                <div class="daily-deal">
                    <div class="owl-carousel-space">
                        <div class="row">
                            <div class="daily-deal">
                                <div class="daily-deal-carousel owl-carousel owl-theme">
                                    <?php
                                    $sql = "SELECT * FROM home_daily_deal ORDER BY id DESC LIMIT 10";
                                    $result = $conn->query($sql);

                                    while ($row = $result->fetch_assoc()):
                                        $id = (int) $row['id'];
                                        $images = json_decode($row['images'], true);
                                        $firstt = !empty($images[0]) ? $images[0] : 'default-image.jpg';
                                        $product_name = htmlspecialchars($row['product_name']);
                                        $price = number_format((float) $row['price'], 2);
                                        $old_price = number_format((float) $row['old_price'], 2);
                                        $deal_end = !empty($row['deal_end']) ? date("Y/m/d H:i:s", strtotime($row['deal_end'])) : '';
                                        ?>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="single-product">
                                                <div class="daily-products">
                                                    <div class="product-img text-center" style="height: auto;">
                                                        <a href="product-details.php?id=<?= $id ?>">
                                                            <img src="./admin/<?= htmlspecialchars($firstt) ?>"
                                                                alt="<?= $product_name ?>" class="img-fluid" />
                                                        </a>
                                                        <span class="new">new</span>
                                                    </div>
                                                    <div class="daily-content text-center">
                                                        <h5 class="product-name">
                                                            <a class="text-decoration-none" href="product-details.php?id=<?= $id ?>"
                                                                title="<?= $product_name ?>">
                                                                <?= $product_name ?>
                                                            </a>
                                                        </h5>
                                                        <div class="reviews">
                                                            <div class="star-content clearfix">
                                                                <span class="star star-on"></span>
                                                                <span class="star star-on"></span>
                                                                <span class="star star-on"></span>
                                                                <span class="star star-on"></span>
                                                                <span class="star star-on"></span>
                                                            </div>
                                                        </div>
                                                        <div class="price-box">
                                                            <span class="price text-white fw-bold">₹ <?= $price ?>
                                                            </span><br>
                                                            <span
                                                                class="old-price text-white text-muted text-decoration-line-through">₹
                                                                <?= $old_price ?> </span>
                                                        </div>
                                                    </div>
                                                    <?php if ($deal_end): ?>
                                                        <div class="upcoming text-center mt-2"
                                                            style="margin-top: 0px!important;">
                                                            <span class="is-countdown"></span>
                                                            <div data-countdown="<?= $deal_end ?>"></div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
                    <!--=====daily-deals-end=====-->
                    <!--=====product-tab-start=====-->
                    <div class="home-4-product-tab">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="feature-tab-area product-tabs">
                                        <!-- Tab Navigation -->
                                        <div class="tab-nav">
                                            <button class="tab-btn active" data-tab="newarrival">NEW ARRIVAL</button>
                                            <button class="tab-btn" data-tab="onsale">ON SALE</button>
                                            <button class="tab-btn" data-tab="bestseller">BEST SELLER</button>
                                        </div>

                                        <!-- Tab Content -->
                                        <div class="tab-content">
                                            <!-- New Arrival Products -->
                                            <div class="tab-pane active" id="newarrival">
    <div style="position: relative;">
        <!-- Left Arrow -->
        <!-- <button class="scroll-btn left" onclick="scrollNewArrival(-1)">&#10094;</button> -->

        <!-- Scrollable Row -->
        <div class="row" id="newarrival-row" style="overflow-x: auto; white-space: nowrap; scroll-behavior: smooth;">
            <?php foreach ($newArrivalProducts as $product): ?>
                                                            <div class="col-xl-3 col-lg-4 col-md-6" style="display:inline-block; float:none;">
                                                                <div class="single-product">
                                                                    <div class="product-img">
                                                                        <a href="product-details.php?id=<?= $product['id'] ?>">
                                                                            <?php
                                                                            $images = json_decode($product['images'], true);
                                                                            if (!empty($images)): ?>
                                                                                <img src="admin/<?= htmlspecialchars($images[0]) ?>"
                                                                                    alt="<?= htmlspecialchars($product['product_name']) ?>">
                                                                            <?php endif; ?>
                                                                        </a>
                                                                        <span class="badge-new">New</span>
                                                                    </div>
                                                                    <div class="product-content">
                                                                        <h5 class="product-name">
                                                                            <a  class="extra-text-decoration" href="product-details.php?id=<?= $product['id'] ?>">
                                                                                <?= htmlspecialchars($product['product_name']) ?>
                                                                            </a>
                                                                        </h5>
                                                                        <?php
                                                                        $product_id = $product['id'];
                                                                        $query = "SELECT COUNT(*) AS review_count, AVG(rating) AS avg_rating FROM reviews WHERE product_id = $product_id";
                                                                        $result = mysqli_query($conn, $query);

                                                                        if ($result && mysqli_num_rows($result) > 0) {
                                                                            $reviewData = mysqli_fetch_assoc($result);
                                                                            $product['review_count'] = $reviewData['review_count'] ?? 0;
                                                                            $product['rating'] = round($reviewData['avg_rating'] ?? 0);
                                                                        } else {
                                                                            $product['review_count'] = 0;
                                                                            $product['rating'] = 0;
                                                                        }
                                                                        ?>
                                            
                                                                        <div class="product-rating">
                                                                            <?= str_repeat('★', $product['rating']) ?>
                                                                            <?= str_repeat('☆', 5 - $product['rating']) ?>
                                                                            <?php if ($product['review_count'] > 0): ?>
                                                                                <span class="review-count"><?= $product['review_count'] ?> Review(s)</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                            
                                                                        <div class="price-box">
                                                                            <?php
                                                                            $original_price = (float) $product['price'];
                                                                            $final_price = $original_price;
                                                                            if (!empty($product['discount']) && $product['discount'] > 0) {
                                                                                $final_price -= (float) $product['discount'];
                                                                            }
                                                                            $user_account_type = !empty($_SESSION['account_type']) ? strtolower(trim($_SESSION['account_type'])) : null;
                                                                            $is_commercial = ($user_account_type === 'commercial');
                                                                            if ($is_commercial && !empty($product['corporate_discount']) && $product['corporate_discount'] > 0) {
                                                                                $final_price -= (float) $product['corporate_discount'];
                                                                            }
                                                                            ?>
                                                                            <span class="price">₹<?= number_format($final_price, 2) ?></span>
                                                                            <span class="old-price">₹<?= number_format($original_price, 2) ?></span>
                                                                            <?php if ($is_commercial && !empty($product['corporate_discount']) && $product['corporate_discount'] > 0): ?>
                                                                                <div class="corporate-price" style="color:green; font-weight:bold;">
                                                                                    Special Commercial Price Applied
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                            
                                                    <!-- Right Arrow -->
                                                    <!-- <button class="scroll-btn right" onclick="scrollNewArrival(1)">&#10095;</button> -->
                                                </div>
                                            </div>
                                            
                                            


                                            <!-- On Sale Products -->
                                            <div class="tab-pane" id="onsale">
                                                <div class="row">
                                                    <?php foreach ($onsaleProducts as $product): ?>
                                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                                            <div class="single-product">
                                                                <div class="product-img">
                                                                    <a href="product-details.php?id=<?= $product['id'] ?>">
                                                                        <?php
                                                                        $images = json_decode($product['images'], true);
                                                                        if (!empty($images)): ?>
                                                                            <img src="admin/<?= htmlspecialchars($images[0]) ?>"
                                                                                alt="<?= htmlspecialchars($product['product_name']) ?>">
                                                                        <?php endif; ?>
                                                                    </a>
                                                                    <span class="badge-sale">Sale</span>
                                                                </div>
                                                                <div class="product-content">
                                                                    <h5 class="product-name">
                                                                        <a
                                                                            href="product-details.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['product_name']) ?></a>
                                                                    </h5>
                                                                    <?php
                                                                    $product_id = $product['id']; // Use correct ID field
                                                                
                                                                    // Fetch average rating and total reviews
                                                                    $query = "SELECT COUNT(*) AS review_count, AVG(rating) AS avg_rating FROM reviews WHERE product_id = $product_id";
                                                                    $result = mysqli_query($conn, $query);

                                                                    if ($result && mysqli_num_rows($result) > 0) {
                                                                        $reviewData = mysqli_fetch_assoc($result);
                                                                        $product['review_count'] = $reviewData['review_count'] ?? 0;
                                                                        $product['rating'] = round($reviewData['avg_rating'] ?? 0);
                                                                    } else {
                                                                        $product['review_count'] = 0;
                                                                        $product['rating'] = 0;
                                                                    }
                                                                    ?>

                                                                    <div class="product-rating">
                                                                        <?= str_repeat('★', $product['rating']) ?>
                                                                        <?= str_repeat('☆', 5 - $product['rating']) ?>

                                                                        <?php if ($product['review_count'] > 0): ?>
                                                                            <span
                                                                                class="review-count"><?= $product['review_count'] ?>
                                                                                Review(s)</span>
                                                                        <?php endif; ?>
                                                                    </div>

                                                                    <div class="price-box">
                                                                             <?php
                                                                            $original_price = (float) $product['price'];
                                                                            $final_price = $original_price;

                                                                            // Apply normal discount
                                                                            if (!empty($product['discount']) && $product['discount'] > 0) {
                                                                                $final_price -= (float) $product['discount'];
                                                                            }

                                                                            // Read account type from session
                                                                            $user_account_type = !empty($_SESSION['account_type']) ? strtolower(trim($_SESSION['account_type'])) : null;
                                                                            $is_commercial = ($user_account_type === 'commercial');

                                                                            // Apply corporate discount
                                                                            if ($is_commercial && !empty($product['corporate_discount']) && $product['corporate_discount'] > 0) {
                                                                                $final_price -= (float) $product['corporate_discount'];
                                                                            }
                                                                            ?>
                                                                    
                                                                        <span class="price">₹
                                                                            <?= number_format($final_price, 2) ?>
                                                                        </span>
                                                                        <span class="old-price">₹
                                                                            <?= number_format($original_price, 2) ?>
                                                                        </span>
                                                                    
                                                                        <?php if ($is_commercial && !empty($product['corporate_discount']) && $product['corporate_discount'] > 0): ?>
                                                                                <div class="corporate-price" style="color:green; font-weight:bold;">
                                                                                    Special Commercial Price Applied
                                                                                </div>
                                                                        <?php endif; ?>
                                                                    
                                                                        <!-- Temporary debug
                                                                        <div style="margin-top:5px; font-size:14px; color:blue;">
                                                                            Account Type (Debug): <?= htmlspecialchars($user_account_type ?? 'Not Set') ?>
                                                                    </div> -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <!-- Best Seller Products -->
                                        <div class="tab-pane" id="bestseller">
                                            <div class="row">
                                                <?php foreach ($bestsellerProducts as $product): ?>
                                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                                        <div class="single-product">
                                                            <div class="product-img">
                                                                <a href="product-details.php?id=<?= $product['id'] ?>">
                                                                    <?php
                                                                    $images = json_decode($product['images'], true);
                                                                    if (!empty($images)): ?>
                                                                        <img src="admin/<?= htmlspecialchars($images[0]) ?>"
                                                                            alt="<?= htmlspecialchars($product['product_name']) ?>">
                                                                    <?php endif; ?>
                                                                </a>
                                                                <?php if (strtotime($product['created_at']) > strtotime('-30 days')): ?>
                                                                    <span class="badge-new">Best seller</span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php
                                                            // Fetch product rating & review count dynamically
                                                            $product_id = $product['id']; // Ensure this is the correct field name
                                                        
                                                            $query = "SELECT COUNT(*) AS review_count, AVG(rating) AS avg_rating 
          FROM reviews 
          WHERE product_id = $product_id";
                                                            $result = mysqli_query($conn, $query);

                                                            if ($result && mysqli_num_rows($result) > 0) {
                                                                $reviewData = mysqli_fetch_assoc($result);
                                                                $product['review_count'] = (int) ($reviewData['review_count'] ?? 0);
                                                                $product['rating'] = round($reviewData['avg_rating'] ?? 0);
                                                            } else {
                                                                $product['review_count'] = 0;
                                                                $product['rating'] = 0;
                                                            }
                                                            ?>

                                                            <div class="product-content">
                                                                <h5 class="product-name">
                                                                    <a href="product-details.php?id=<?= $product['id'] ?>">
                                                                        <?= htmlspecialchars($product['product_name']) ?>
                                                                    </a>
                                                                </h5>

                                                                <div class="product-rating">
                                                                    <?= str_repeat('★', $product['rating']) ?>
                                                                    <?= str_repeat('☆', 5 - $product['rating']) ?>
                                                                    <?php if ($product['review_count'] > 0): ?>
                                                                        <span class="review-count">
                                                                            <?= $product['review_count'] ?> Review(s)
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>

                                                               <div class="price-box">
    <?php
    $original_price = (float)$product['price'];
    $final_price = $original_price;

    // Apply normal discount
    if (!empty($product['discount']) && $product['discount'] > 0) {
        $final_price -= (float)$product['discount'];
    }

    // Read account type from session
    $user_account_type = !empty($_SESSION['account_type']) ? strtolower(trim($_SESSION['account_type'])) : null;
    $is_commercial = ($user_account_type === 'commercial');

    // Apply corporate discount
    if ($is_commercial && !empty($product['corporate_discount']) && $product['corporate_discount'] > 0) {
        $final_price -= (float)$product['corporate_discount'];
    }
    ?>

    <span class="price">₹<?= number_format($final_price, 2) ?></span>
    <span class="old-price">₹<?= number_format($original_price, 2) ?></span>

    <?php if ($is_commercial && !empty($product['corporate_discount']) && $product['corporate_discount'] > 0): ?>
        <div class="corporate-price" style="color:green; font-weight:bold;">
            Special Commercial Price Applied
        </div>
    <?php endif; ?>

    <!-- Temporary debug
    <div style="margin-top:5px; font-size:14px; color:blue;">
        Account Type (Debug): <?= htmlspecialchars($user_account_type ?? 'Not Set') ?>
    </div> -->
</div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--=====product-tab-end=====-->
            <!-- service-start -->
            <div class="home-4-service home-2-service service-area">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-12 service">
                            <div class="service-logo">
                                <img src="img/service/2.1.png" alt="" />
                            </div>
                            <div class="service-info">
                                <h2>100% money back guarantee</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit auctor nibh.</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12 service">
                            <div class="service-logo">
                                <img src="img/service/2.2.png" alt="" />
                            </div>
                            <div class="service-info">
                                <h2>Free shipping on oder over 500$</h2>
                                <p>Duis luctus libero in quam convallis, idpla cerat tellus convallis.</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12 service">
                            <div class="service-logo">
                                <img src="img/service/2.3.png" alt="" />
                            </div>
                            <div class="service-info">
                                <h2>online support 24/7</h2>
                                <p>Etiam ac purus at lorem commodo vestibulum elementum sed felis.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--=====service-end=====-->

            <!-- feature-product-start -->
            <div class="feature-product-area py-5">
                <div class="container">
                    <div class="row">
                        <div class="product-title text-left">
                            <h2>
                                <span>FEATURED PRODUCTS</span>
                            </h2>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (!empty($featuredProducts)): ?>
                            <?php foreach ($featuredProducts as $product): ?>
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="single-product">
                                        <div class="product-img">
                                            <a href="product-details.php?id=<?= $product['id'] ?>">
                                                <?php
                                                $images = json_decode($product['images'], true);
                                                if (!empty($images)): ?>
                                                    <img src="admin/<?= htmlspecialchars($images[0]) ?>"
                                                        alt="<?= htmlspecialchars($product['product_name']) ?>">
                                                <?php endif; ?>
                                            </a>
                                            <!-- <span class="badge-new">Featured</span> -->
                                        </div>
                                        <?php
                                        // Fetch product rating & review count dynamically
                                        $product_id = $product['id']; // Ensure this is the correct field name
                                
                                        $query = "SELECT COUNT(*) AS review_count, AVG(rating) AS avg_rating 
          FROM reviews 
          WHERE product_id = $product_id";
                                        $result = mysqli_query($conn, $query);

                                        if ($result && mysqli_num_rows($result) > 0) {
                                            $reviewData = mysqli_fetch_assoc($result);
                                            $product['review_count'] = (int) ($reviewData['review_count'] ?? 0);
                                            $product['rating'] = round($reviewData['avg_rating'] ?? 0);
                                        } else {
                                            $product['review_count'] = 0;
                                            $product['rating'] = 0;
                                        }
                                        ?>

                                        <div class="product-content">
                                            <h5 class="product-name">
                                                <a href="product-details.php?id=<?= $product['id'] ?>">
                                                    <?= htmlspecialchars($product['product_name']) ?>
                                                </a>
                                            </h5>

                                            <div class="product-rating">
        <?= str_repeat('★', $product['rating']) ?>
        <?= str_repeat('☆', 5 - $product['rating']) ?>
        <?php if ($product['review_count'] > 0): ?>
            <span class="review-count"><?= $product['review_count'] ?> Review(s)</span>
        <?php endif; ?>
    </div>

                                            <div class="price-box">
    <?php
    $original_price = (float)$product['price'];
    $final_price = $original_price;

    // Apply normal discount
    if (!empty($product['discount']) && $product['discount'] > 0) {
        $final_price -= (float)$product['discount'];
    }

    // Read account type from session
    $user_account_type = !empty($_SESSION['account_type']) ? strtolower(trim($_SESSION['account_type'])) : null;
    $is_commercial = ($user_account_type === 'commercial');

    // Apply corporate discount
    if ($is_commercial && !empty($product['corporate_discount']) && $product['corporate_discount'] > 0) {
        $final_price -= (float)$product['corporate_discount'];
    }
    ?>

    <span class="price">₹<?= number_format($final_price, 2) ?></span>
    <span class="old-price">₹<?= number_format($original_price, 2) ?></span>

    <!-- <?php if ($is_commercial && !empty($product['corporate_discount']) && $product['corporate_discount'] > 0): ?>
        <div class="corporate-price" style="color:green; font-weight:bold;">
            Special Commercial Price Applied
        </div>
    <?php endif; ?> -->

    <!-- Temporary debug -->
    <!-- <div style="margin-top:5px; font-size:14px; color:blue;">
        Account Type (Debug): <?= htmlspecialchars($user_account_type ?? 'Not Set') ?>
    </div> -->
</div>




                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center">
                                <p>No featured products found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- feature-product-end -->

            <!--===== banner-2-start =====-->
            <!-- <div class="home-4-banner-2">
                <div class="container">
                    <div class="banner-box">
                        <a href="#">
                            <img src="img/banner/9_1.jpg" alt="" />
                        </a>
                    </div>
                </div>
            </div> -->
            <!--===== banner-2-end =====-->

            <!--===== latest-blog-start =====-->

           


            <!--===== latest-blog-end =====-->

            <!--===== testimonial-area-start =====-->
<!-- testimonial-area-start -->
<div class="testimonial-area py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container">
        <div class="testimonial text-center">
            <div class="testimonial-container shadow-sm p-4 rounded" style="background: #fff;">
                
                <!-- Header -->
                <h3 class="section-header mb-2" style="font-weight: 700; color: #333;">
                    <!-- <i class="fas fa-quote-left" style="color: #ff9800; margin-right: 8px;"></i> -->
                    What Our Customers Say
                </h3>
                <p class="text-muted mb-4" style="max-width: 600px; margin: 0 auto;">
                    Real Google reviews from our valued customers.
                </p>

                <!-- Google reviews iframe -->
                <div class="responsive-iframe" style="max-width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    <iframe src="https://widgets.sociablekit.com/google-reviews/iframe/25584908"
                        frameborder="0"
                        allowfullscreen
                        loading="lazy"
                        style="width:100%; height:500px; border:0;">
                    </iframe>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- testimonial-area-end -->

 <div class="home-4-latest-blog px-5">
                <div class="blog">
                    <div class="product-title">
                        <h2><span>Latest Blog</span></h2>
                    </div>
                    <div class="owl-carousel-space">
                        <div class="row">
                            <div class="blogs-carousel">
                                <?php
                                // Fetch latest 4 blogs
                                $blogQuery = "SELECT title, slug, main_content, main_images, created_at FROM blog ORDER BY created_at DESC LIMIT 4";
                                $blogResult = $conn->query($blogQuery);

                                if ($blogResult->num_rows > 0) {
                                    while ($blog = $blogResult->fetch_assoc()) {
                                        $title = htmlspecialchars($blog['title']);
                                        $slug = htmlspecialchars($blog['slug']);
                                        $content = strip_tags($blog['main_content']); // remove HTML tags
                                        $contentShort = substr($content, 0, 100) . "...";
                                        $image = !empty($blog['main_images'])
                                            ? "img/latest-blog/" . $blog['main_images']
                                            : "img/latest-blog/default.jpg";

                                        $date = date("F d, Y", strtotime($blog['created_at'])); // formatted date
                                        $link = "blog/" . urlencode($slug);
                                        ?>
                                        <div class="col-md-12">
                                            <div class="single-blog">
                                                <div class="blog-img">
                                                    <a href="<?= $link ?>">
                                                        <img src="<?= $image ?>" alt="<?= $title ?>" />
                                                    </a>
                                                </div>
                                                <div class="blog-content">
                                                    <h4 class="blog-title">
                                                        <a href="<?= $link ?>"><?= $title ?></a>
                                                    </h4>
                                                    <p><?= $contentShort ?></p>
                                                    <span class="blog-date"><?= $date ?></span>
                                                    <a class="readmore-btn" href="<?= $link ?>">
                                                        Read More
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "<p>No blogs found.</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<style>
    @media (max-width: 768px) {
        .testimonial-container iframe {
            height: 400px !important;
        }
    }
</style>

            <!-- <div class="testimonial-area">
            <div class="container">
                <div class="testimonial">
                    <div class="testimonial-container">
                        <div class="testimonial-carousel">
                            <div class="item">
                                <div class="author-content">
                                    <div class="img">
                                        <img src="img/latest-blog/850-untitled-1.jpg" alt="" />
                                    </div>
                                    <div class="content">
                                        <p class="content-name">Mekirin-H</p>
                                        <p class="content-email">demo@posthemes.com</p>
                                    </div>
                                </div>
                                <p class="testimonial-p">" Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus diam arcu,
                                    placerat ut odio vel, ultrices vehicula erat. Ut mauris diam, egestas nec lacus sit amet ."
                                </p>
                            </div>
                            <div class="item">
                                <div class="author-content">
                                    <div class="img">
                                        <img src="img/latest-blog/850-untitled-1.jpg" alt="" />
                                    </div>
                                    <div class="content">
                                        <p class="content-name">Mekirin-H</p>
                                        <p class="content-email">demo@posthemes.com</p>
                                    </div>
                                </div>
                                <p class="testimonial-p">" Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus diam arcu,
                                    placerat ut odio vel, ultrices vehicula erat. Ut mauris diam, egestas nec lacus sit amet ."
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
            <!-- testimonial-area-end -->
            <!-- brand-area-start -->
            <div class="home-4-brand-area">
                <div class="container owl-carousel-space">
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
        </div>
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
                                            <input id="quantity_wanted" class="text" type="text" value="1" name="qty"
                                                style="border: 1px solid rgb(189, 194, 201);">

                                        </p>
                                        <div class="shop-add-cart">
                                            <button class="exclusive">
                                                <span>Add to cart</span>
                                            </button>
                                        </div>
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


        <!-- jQuery (required) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Owl Carousel JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tabBtns = document.querySelectorAll('.tab-btn');
                const tabPanes = document.querySelectorAll('.tab-pane');

                // Show first tab by default
                document.querySelector('.tab-pane').classList.add('active');

                tabBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        // Remove active class from all buttons and panes
                        tabBtns.forEach(b => b.classList.remove('active'));
                        tabPanes.forEach(pane => pane.classList.remove('active'));

                        // Add active class to clicked button
                        this.classList.add('active');

                        // Show corresponding tab content
                        const tabId = this.getAttribute('data-tab');
                        document.getElementById(tabId).classList.add('active');
                    });
                });
            });
        </script>
</body>
</html>