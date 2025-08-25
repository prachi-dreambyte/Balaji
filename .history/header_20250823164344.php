<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'connect.php';

// Fetch categories for menu
$sql = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $sql);

// Cart item count by user
$cart_count = 0;
$total_price = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_id = $_SESSION['user_id'];

    // $stmt = $conn->prepare("SELECT SUM(quantity) as total_items, SUM(quantity * p.price) as total_price
    $stmt = $conn->prepare("SELECT SUM(quantity) as total_items, SUM(quantity * p.price) as total_price
                            FROM cart c
                            JOIN products p ON c.product_id = p.id
                            WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    $row = $cart_result->fetch_assoc();
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    $row = $cart_result->fetch_assoc();

    $cart_count = $row['total_items'] ?? 0;
    $total_price = $row['total_price'] ?? 0;
    $cart_count = $row['total_items'] ?? 0;
    $total_price = $row['total_price'] ?? 0;
}

// if (isset($_GET['action']) && $_GET['action'] === 'logout') {
//   session_destroy();
//   header('Location: index.php');
//   exit;
// }

// --- DYNAMIC CATEGORY GROUPS LOGIC (insert at top of file, after session and DB connect) ---
$groups = [];
$q = "
    SELECT 
        TRIM(COALESCE(Main_Category_name,'')) AS main_cat,
        TRIM(category_name) AS category_name,
        TRIM(COALESCE(category_image,'')) AS category_image
    FROM categories
    WHERE TRIM(COALESCE(Main_Category_name,'')) <> ''
    ORDER BY main_cat ASC, category_name ASC
";
$res = mysqli_query($conn, $q);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $main = $row['main_cat'];
        if (!isset($groups[$main])) $groups[$main] = [];
        $groups[$main][] = [
            'name' => $row['category_name'],
            'image' => $row['category_image'],
        ];
    }
}
function categoryImagePath($relPath) {
    $relPath = ltrim($relPath ?? '', '/');
    if ($relPath === '') return 'assets/images/placeholder.png';
    $diskPath = __DIR__ . '/admin/' . $relPath;
    if (file_exists($diskPath)) {
        return 'admin/' . $relPath;
    }
    $diskPath2 = __DIR__ . '/' . $relPath;
    if (file_exists($diskPath2)) {
        return $relPath;
    }
    return 'assets/images/placeholder.png';
}
?>

<!-- Font Awesome 5 (solid icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>

    .dropdown-toggle::after {
    display: none !important;
}

    .img-bg {
        width: 60px;
        height: 60px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    .img-bg {
        width: 60px;
        height: 60px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .img-bg img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 4px;
    }
    .img-bg img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 4px;
    }

    /* Match your first screenshot colors */
    .gradient-blue {
        background: linear-gradient(135deg, #cceaff, #99d6ff);
    }
    /* Match your first screenshot colors */
    .gradient-blue {
        background: linear-gradient(135deg, #cceaff, #99d6ff);
    }

    .gradient-brown {
        background: linear-gradient(135deg, #f5d2b0, #e8b27a);
    }
    .gradient-brown {
        background: linear-gradient(135deg, #f5d2b0, #e8b27a);
    }

    .gradient-purple {
        background: linear-gradient(135deg, #e5ccff, #c299ff);
    }
    .gradient-purple {
        background: linear-gradient(135deg, #e5ccff, #c299ff);
    }

    .gradient-green {
        background: linear-gradient(135deg, #e0f8d8, #a8e6a3);
    }
    .gradient-green {
        background: linear-gradient(135deg, #e0f8d8, #a8e6a3);
    }

    .gradient-yellow {
        background: linear-gradient(135deg, #fff3c2, #ffe699);
    }
    .gradient-yellow {
        background: linear-gradient(135deg, #fff3c2, #ffe699);
    }

    .gradient-orange {
        background: linear-gradient(135deg, #ffd6b3, #ffb366);
    }
    .gradient-orange {
        background: linear-gradient(135deg, #ffd6b3, #ffb366);
    }

    .gradient-default {
        background: linear-gradient(135deg, #f0f0f0, #cccccc);
    }
    .gradient-default {
        background: linear-gradient(135deg, #f0f0f0, #cccccc);
    }

    .category-list {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 5px;
        background: #F5F6F2;
        color: #363636;
        position: absolute;
        top: 100%;
        /* Changed from 50px to position directly below */
        left: 0;
        width: 250px;
        border-radius: 8px;
        box-shadow: 0px 5px 15px rgba(66, 49, 49, 0.3);
        margin-top: 5px;
        /* Added small gap */

        
    }

    /* Scrollbar styles */
    .category-list::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
    /* Scrollbar styles */
    .category-list::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .category-list::-webkit-scrollbar-track {
        /* background: #222; */
        border-radius: 10px;
        margin: 5px 0;
    }

    .category-list::-webkit-scrollbar-thumb {
        background: black;
        border-radius: 10px;
        border: 2px solid #222;
    }

    .category-list::-webkit-scrollbar-thumb:hover {
        /* background: #d87b94; */
    }

    /* For Firefox */
    .category-list {
        scrollbar-width: thin;
        scrollbar-color: #918b8dff #222;
    }

    .category-list li {
        display: flex;
        align-items: center;
        padding: 8px;
        border-radius: 8px;
        transition: background 0.2s ease;
    }

    .category-list li:hover {
        /* background: rgba(0, 0, 0, 0.05); */
    }

    .category-list a {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: inherit;
    }

    .img-bg {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dbeafe, #fce7f3);
        margin-right: 12px;
        flex-shrink: 0;
    }

    .img-bg img {
        width: 36px;
        height: 36px;
        object-fit: contain;
    }

    .category-list .text h4 {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }

    .category-list .text p {
        margin: 0;
        font-size: 13px;
        color: gray;
    }

.header-section {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1050;
    padding: 10px 0px !important;
    background-color: #F5F6F2 !important;
    border: none;
    color: #363636 !important;
}


    .header-Side {
        font-size: 15px !important;
        text-decoration: none !important;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', sans-serif;
        padding-top: 88px;
    }

    .headerText {
        font-size: 18px !important;
        padding-right: 10px !important;
        font-weight: 500 !important;
    }

    .logo {
        width: 160px;
        height: auto;
    }

    .logo:hover {
        transform: scale(1.05);
        transition: transform 0.3s ease;
        filter: drop-shadow(0 0 5px black);
    }

    .nav-link {
        font-weight: 500;
        letter-spacing: 0.5px;
        padding: 0;
        font-size: 20px;
        position: relative;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .nav-link::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 0%;
        height: 2px;
        background-color: black;
        transition: width 0.3s ease;
    }

    .nav-link.active {
        color: black !important;
    }

    .nav-link:hover {
        color: #845848 !important;
        transform: scale(1.05) !important;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
           padding: 10px 30px 10px 10px;
    border-radius: 5px;
    border: none;
    outline: none;
    width: 210px;
    box-shadow: 1px 2px 3px #dddde1;
    font-size: 15px;
    }

    .search-box i {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        transition: color 0.3s ease;
        color: #333;
    }

    .search-box:hover i {
        color: #845848;
    }

    a {
        transition: all 0.3s ease-in-out;
    }

    .nav-item-with-mega {
        position: relative;
    }

    .nav-item-with-mega:hover .mega-menu {
        display: block;
        opacity: 1;
        visibility: visible;
    }

   .mega-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  display: none;
  opacity: 0;
  visibility: hidden;
  transition: all 0.25s ease-in-out;
  z-index: 1000;
  min-width: 500px; /* wide enough for 2 cols */
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);

  display: grid;
  grid-template-columns: 1fr 1fr; /* 2 columns */
  gap: 20px;
}

.nav-item-with-mega:hover .mega-menu {
  display: grid;
  opacity: 1;
  visibility: visible;
}

/* Each column box */
.category-column {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.category-column h3 {
  font-size: 15px;
  font-weight: bold;
  margin: 0 0 8px 0;
  padding-bottom: 6px;
  border-bottom: 1px solid #eee;
  color: #333;
}

.category-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.category-list li {
  display: flex;
  align-items: center;
}

.category-list a {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  color: #333;
  padding: 6px 8px;
  border-radius: 6px;
  transition: background 0.2s ease;
}

.category-list a:hover {
  background: #f5f5f5;
}

.img-bg {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  overflow: hidden;
  flex-shrink: 0;
  background: #f9f9f9;
  display: flex;
  align-items: center;
  justify-content: center;
}

.img-bg img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.category-list h4 {
  font-size: 14px;
  margin: 0;
  font-weight: 500;
}

    .category-list p {
        font-size: 12px;
        color: #666;
        margin: 0;
    }

    .cart-wrapper {
        position: relative;
    }

    .top-cart-content {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background-color: #000;
        color: #fff;
        width: 300px;
        padding: 15px;
        border: 1px solid #333;
        z-index: 1000;
    }

    .cart-wrapper:hover .top-cart-content {
        display: block;
    }

    .top-cart-content .media {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .top-cart-content .media img {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }

    .top-cart-content .media-body {
        margin-left: 10px;
    }

    .cart-total {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-weight: bold;
        border-top: 1px solid #555;
        border-bottom: 1px solid #555;
    }

    .checkout a {
        display: block;
        background-color: black;
        text-align: center;
        color: #fff;
        padding: 10px;
        margin-top: 10px;
        text-decoration: none;
        border-radius: 3px;
    }

    .checkout a:hover {
        background-color: #845848;
    }

    /* Responsive overrides */
    @media (max-width: 1300px) {}

    @media (max-width: 992px) {
        .search-box {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .search-box input {
            width: 140px;
        }

        .nav-link {
            font-size: 14px;
        }
    }
/* Suggestions Dropdown */
#suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: #fff;
  border: 1px solid #ddd;
  border-radius:2px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
  z-index: 1000;
  display: none;
  overflow: hidden; /* no scroll */
}

.suggestion-item {
  padding: 10px 14px;
  font-size: 15px;
  color: #333;
  cursor: pointer;
  transition: background 0.2s, padding-left 0.2s;
  border-bottom: 1px solid #f5f5f5;
}

.suggestion-item:last-child {
  border-bottom: none;
}

.suggestion-item:hover {
  background: #f9f9f9;
  padding-left: 18px;
}

.suggestion-highlight {
  font-weight: 600;
  color: #007bff; /* highlight match */
}
/* Fullscreen overlay */
#preloader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: #fff; /* adjust if you want dark mode */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  transition: opacity 0.5s ease, visibility 0.5s ease;
}


/* When hidden */
#preloader.hidden {
  opacity: 0;
  visibility: hidden;
}

/* Simple loader animation */
.loader {
  border: 6px solid #f3f3f3;
  border-top: 6px solid #3498db;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

</style>
<!-- Preloader -->
<div id="preloader">
  <img src="img\balaji\loading.gif" alt="Loading..." />
</div>

 <!-- Marquee Start -->
<div style= "background: #845848;
    color: #fff;
    font-size: 14px;
    padding: 6px 0;
    text-align: center;">
  <marquee behavior="scroll" direction="left" scrollamount="5">
    100% MONEY BACK GUARANTEE &nbsp; | &nbsp; FREE SHIPPING ON ORDER OVER ₹3000 &nbsp; | &nbsp; ONLINE SUPPORT 24/7
  </marquee>
</div>
<!-- Marquee End -->
<header class=" header-section">
    <div class="container-fluid px-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <!-- Logo -->
            <div class="d-flex align-items-center flex-shrink-0">
                <img class="logo img-responsive" src="img/balaji/balaji-furniture-2048x1368.png" alt="Balaji Furniture" />
            </div>

            <!-- Nav (desktop only) -->
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>
            <nav class="d-none d-xl-flex flex-wrap justify-content-center gap-3 flex-grow-1">
                <p class="headerText"><a href="index.php"
                        class="nav-link  <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">HOME</a>
                </p>

               <div class="nav-item-with-mega">
  <p class="headerText">
    <a href="shop.php"
       class="nav-link dropdown-toggle <?php echo ($current_page == 'shop.php') ? 'active' : ''; ?>"
       id="categoryLink">
       CATEGORY
    </a>
  </p>
  <div class="mega-menu" id="categoryMenu">
    <?php if (!empty($groups)): ?>
      <?php foreach ($groups as $mainTitle => $items): ?>
        <div class="category-column">
          <h3><?php echo htmlspecialchars($mainTitle); ?></h3>
          <ul class="category-list">
            <?php foreach ($items as $it): 
              $name = htmlspecialchars($it['name']);
              $imagePath = categoryImagePath($it['image']);
            ?>
              <li>
                <a href="shop.php?category=<?php echo urlencode($name); ?>#product-list">
                  <div class="img-bg"><img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo $name; ?>"></div>
                  <h4><?php echo $name; ?></h4>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="category-column">
        <h3>Categories</h3>
        <ul class="category-list"><li><span>No categories found.</span></li></ul>
      </div>
    <?php endif; ?>
  </div>
</div>



                <p class="headerText"><a href="index.php#deals"
                        class="nav-link  <?php echo ($current_page == 'index.php#deals') ? 'active' : ''; ?>">OFFER</a>
                </p>
                
                <p class="headerText"><a href="about-us.php"
                        class="nav-link  <?php echo ($current_page == 'about-us.php') ? 'active' : ''; ?>">ABOUT
                        US</a></p>
                <p class="headerText"><a href="blog.php"
                        class="nav-link  <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>">BLOG</a>
                </p>
                <p class="headerText"><a href="contact.php"
                        class="nav-link  <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">CONTACT</a>
                </p>
            </nav>

            <!-- Search + Account Icons -->
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                
<div class="search-box d-none d-md-block" style="position: relative; max-width:300px;">
    <form id="searchForm" action="shop.php" method="get" autocomplete="off">
        <input type="text" 
               name="search" 
               id="searchInput" 
               placeholder="Search products..."
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autocomplete="off"
                            spellcheck="false"
                            style="width:100%; padding:10px 40px 10px 14px; border:1px solid #ddd; border-radius:25px; font-size:15px;">
                
                        <input type="hidden" name="category"
                            value="<?php echo isset($category_name) ? htmlspecialchars($category_name) : ''; ?>">
                        <input type="hidden" name="sort_by" value="<?php echo isset($sort_by) ? htmlspecialchars($sort_by) : ''; ?>">
                        <input type="hidden" name="limit" value="<?php echo isset($limit) ? (int) $limit : 12; ?>">
                
                        <button type="submit"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#666;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                
                    <!-- Suggestions dropdown -->
                    <div id="suggestions"></div>
                </div>

                <a href="my-account.php" class=" d-flex align-items-center header-Side text-black"><i
                        class="fas fa-cog me-1"></i> Account</a>

                <div class="cart-wrapper position-relative">
                    <a href="shopping-cart.php" class=" d-flex align-items-center header-Side text-black">
                        <i class="fas fa-shopping-cart me-1"></i>
                        Cart <?php echo $cart_count . ' item' . ($cart_count > 1 ? 's' : ''); ?>
                    </a>
                    <!-- <div class="top-cart-content">
                        <?php
                        $total_price = 0;
                        if (isset($_SESSION['user_id'])) {
                            $user_id = $_SESSION['user_id'];
                            $stmt = $conn->prepare("SELECT c.quantity, c.id, p.product_name, p.price, p.images 
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
                                    $product_image = "admin/uploads/" . basename($item['images']);
                                    $product_total = $product_price * $product_qty;
                                    $total_price += $product_total;
                                    ?>
                                    <div class="media header-middle-checkout">
                                        <div class="media-left check-img">
                                            <a href="#"><img src="<?php echo $product_image; ?>" alt=""
                                                    style="width:50px;height:50px;" /></a>
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
                                    <a href="checkout.php"><span>Checkout <i class="fa fa-angle-right"
                                                aria-hidden="true"></i></span></a>
                                </div>
                                <?php
                            } else {
                                echo "<p style='padding:10px;'>Cart is empty.</p>";
                            }
                        } else {
                            echo "<p style='padding:10px;'>Please login to see your cart.</p>";
                        }
                        ?>
                    </div> -->
                </div>

                <?php if (isset($_SESSION['user'])): ?>
                    <a href="logout.php" title="Log out of your customer account"
                        class=" d-flex align-items-center header-Side text-black">
                        <i class="fas fa-lock me-1"></i> Log Out</a>
                <?php else: ?>
                    <a href="loginSignUp/login.php" title="Log in to your customer account"
                        class=" d-flex align-items-center header-Side text-black">
                        <i class="fas fa-lock me-1"></i> Log In</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Toggle -->
            <div class="d-xl-none">
                <button class="btn btn-outline-light" id="mobileToggle"><i class="fas fa-bars"></i></button>
            </div>
        </div>

        <!-- Mobile Nav -->
        <div class="mobile-nav d-xl-none mt-3 d-none" id="mobileNav">
            <nav class="nav flex-column">
                <a href="index.php" class="nav-link ">HOME</a>
                <a href="shop.php" class="nav-link ">CATEGORY</a>

                <?php if (!empty($groups)): ?>
      <?php foreach ($groups as $mainTitle => $items): ?>
        <div class="mt-2" style="padding:8px 0;">
          <div style="font-weight:700; font-size:14px; padding:6px 0; border-bottom:1px solid #eee;"><?php echo htmlspecialchars($mainTitle); ?></div>
          <?php foreach ($items as $it):
            $name = htmlspecialchars($it['name']);
            $imagePath = categoryImagePath($it['image']);
          ?>
            <a class="nav-link" href="shop.php?category=<?php echo urlencode($name); ?>#product-list" style="display:flex; align-items:center; gap:10px;">
              <span class="img-bg" style="width:28px; height:28px; border-radius:6px; overflow:hidden;">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo $name; ?>" style="width:100%; height:100%; object-fit:cover;">
              </span>
              <span style="font-size:14px;"><?php echo $name; ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <span class="nav-link">No categories found.</span>
    <?php endif; ?>

                <a href="index.php#deals" class="nav-link ">OFFER</a>
                <a href="contact.php" class="nav-link ">CONTACT</a>
                <a href="about-us.php" class="nav-link ">ABOUT US</a>
                <a href="blog.php" class="nav-link ">BLOG</a>
            </nav>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Category dropdown toggle
        const categoryLink = document.getElementById('categoryLink');
        const categoryMenu = document.getElementById('categoryMenu');

        if (categoryLink && categoryMenu) {
            // Clicking the main category link goes to shop.php
            categoryLink.addEventListener('click', function (e) {
                // Only prevent default if clicking the dropdown arrow or on mobile
                if (!e.target.classList.contains('dropdown-toggle') && window.innerWidth >= 1200) {
                    e.preventDefault();
                    categoryMenu.classList.toggle('show');
                }
            });

            // Close when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.nav-item-with-mega') && categoryMenu.classList.contains('show')) {
                    categoryMenu.classList.remove('show');
                }
            });
        }

        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const mobileNav = document.getElementById('mobileNav');

        if (mobileToggle && mobileNav) {
            mobileToggle.addEventListener('click', function () {
                mobileNav.classList.toggle('d-none');
            });
        }
    });
</script>
<script>
  window.addEventListener("load", function () {
    const preloader = document.getElementById("preloader");
    preloader.classList.add("hidden");
  });
</script>

