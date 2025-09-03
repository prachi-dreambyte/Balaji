<!-- Floating GeM Logo -->
<a href="https://gem.gov.in/" class="floating-gem" target="_blank" title="Visit GeM">
    <img src="img/brand/logo1.jpg" alt="GeM" />
</a>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'connect.php';

// Fetch categories for menu
$sql = "SELECT * FROM categories ORDER bY display_order ASC";
$categories_result = mysqli_query($conn, $sql);

// Cart item count by user
$cart_count = 0;
$total_price = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT SUM(quantity) as total_items, SUM(quantity * p.price) as total_price
                            FROM cart c
                            JOIN products p ON c.product_id = p.id
                            WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    $row = $cart_result->fetch_assoc();

    $cart_count = $row['total_items'] ?? 0;
    $total_price = $row['total_price'] ?? 0;
}

// --- DYNAMIC CATEGORY GROUPS LOGIC ---
$groups = [];
$q = "
    SELECT 
        TRIM(COALESCE(Main_Category_name,'')) AS main_cat,
        TRIM(category_name) AS category_name,
        TRIM(COALESCE(category_image,'')) AS category_image
    FROM categories
    WHERE TRIM(COALESCE(Main_Category_name,'')) <> ''
    ORDER BY display_order ASC
";
$res = mysqli_query($conn, $q);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $main = $row['main_cat'];
        if (!isset($groups[$main]))
            $groups[$main] = [];
        $groups[$main][] = [
            'name' => $row['category_name'],
            'image' => $row['category_image'],
        ];
    }
}
function categoryImagePath($relPath)
{
    $relPath = ltrim($relPath ?? '', '/');
    if ($relPath === '')
        return 'assets/images/placeholder.png';
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
<!-- Responsive enhancements -->
<link rel="stylesheet" href="css/responsive-enhancements.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<style>
    .floating-gem {
    position: fixed;
    bottom: 250px;  /* little upward */
    right: 20px;
    z-index: 9999;
    display: inline-block;
    transition: transform 0.3s ease-in-out;
}

.floating-gem img {
    width: 80px;   /* adjust size as needed */
    height: auto;
    border-radius: 2%;  /* optional – makes it round */
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.floating-gem:hover {
    transform: scale(1.1);
}

.floating-gem {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0px rgba(150, 150, 155, 0.7); }
    50% { box-shadow: 0 0 20px rgba(80, 80, 80, 0.9); }
    100% { box-shadow: 0 0 0px rgba(138,43,226, 0.7); }
}
   


    /* Base styles */
    .dropdown-toggle::after {
        display: none !important;
    }

    /* Category dropdown styles */
    .img-bg {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .img-bg img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 4px;
        object-fit: cover;
    }

    /* Gradient backgrounds for categories */
    .gradient-blue {
        background: linear-gradient(135deg, #cceaff, #99d6ff);
    }

    .gradient-brown {
        background: linear-gradient(135deg, #f5d2b0, #e8b27a);
    }

    .gradient-purple {
        background: linear-gradient(135deg, #e5ccff, #c299ff);
    }

    .gradient-green {
        background: linear-gradient(135deg, #e0f8d8, #a8e6a3);
    }

    .gradient-yellow {
        background: linear-gradient(135deg, #fff3c2, #ffe699);
    }

    .gradient-orange {
        background: linear-gradient(135deg, #ffd6b3, #ffb366);
    }

    .gradient-default {
        background: linear-gradient(135deg, #f0f0f0, #cccccc);
    }

    /* Category dropdown container */
    .mega-menu-wrapper {
        position: relative;
    }

  .mega-menu {
    position: absolute;
    top: 100%;
    left: 205%;
    transform: translateX(-50%);
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    padding: 20px;
    display: none;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    z-index: 1000;

    /* 👇 key change */
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: 220px;  /* width of each column */
    gap: 30px;
    min-width: 500px; /* starting width */
    max-width: 95vw; /* responsive limit */
    overflow-x: auto; /* scroll if too many categories */
}


    .nav-item-with-mega:hover .mega-menu {
        display: grid;
        opacity: 1;
        visibility: visible;
    }

    /* Gap between category link and dropdown */
    .nav-item-with-mega {
        position: relative;
    }

    .mega-menu::before {
        content: '';
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid #fff;
        z-index: 1001;
    }

    /* Each column box */
    .category-column {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .category-column h3 {
        font-size: 15px;
        font-weight: 700;
        margin: 0 0 10px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f0f0;
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
        transition: transform 0.2s ease;
    }

    .category-list li:hover {
        transform: translateX(5px);
    }

    .category-list a {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #555;
        padding: 5px 8px;
        border-radius: 6px;
        transition: all 0.2s ease;
        width: 100%;
    }

    .category-list a:hover {
        background: #f8f9fa;
        color: #845848;
    }

    .category-list h4 {
        font-size: 13px;
        margin: 0;
        font-weight: 500;
        color: #444;
    }

    /* Header styles */
    .header-section {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1050;
        /* padding: 10px 0px !important; */
        background-color: #F5F6F2 !important;
        border: none;
        color: #363636 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        margin: 0;
    }

    .logo {
        width: 160px;
        height: auto;
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .logo:hover {
        transform: scale(1.05);
        filter: drop-shadow(0 0 5px rgba(0, 0, 0, 0.3));
    }

    .nav-link {
        font-weight: 500;
        letter-spacing: 0.5px;
        padding: 0;
        font-size: 20px;
        position: relative;
        transition: color 0.3s ease, transform 0.3s ease;
        color: #363636 !important;
    }

    .nav-link::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 0%;
        height: 2px;
        background-color: #845848;
        transition: width 0.3s ease;
    }

    .nav-link.active {
        color: #845848 !important;
    }

    .nav-link:hover {
        color: #845848 !important;
        transform: translateY(-2px);
    }

    .nav-link:hover::after {
        width: 100%;
    }

    /* Search box */
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

    /* Cart styles */
    .cart-wrapper {
        position: relative;
    }

    .top-cart-content {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background-color: #fff;
        color: #333;
        width: 300px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        z-index: 1000;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
        border-radius: 4px;
    }

    .top-cart-content .media-body {
        margin-left: 10px;
    }

    .cart-total {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-weight: bold;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
        margin: 10px 0;
    }

    .checkout a {
        display: block;
        background-color: #845848;
        text-align: center;
        color: #fff;
        padding: 10px;
        margin-top: 10px;
        text-decoration: none;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .checkout a:hover {
        background-color: #6a463a;
    }

    /* Suggestions Dropdown */
    #suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        z-index: 1000;
        display: none;
        overflow: hidden;
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
        color: #007bff;
    }

    /* Preloader */
/* ---------- Preloader overlay ---------- */
#preloader {
  position: fixed;
  inset: 0;
  background:
    radial-gradient(1200px 800px at 50% 40%, #ffffff 0%, #f7f7f7 40%, #f2f2f2 100%);
  display: grid;
  place-items: center;
  z-index: 9999;
  transition: opacity .6s ease, visibility .6s ease;
}

/* Prevent scrolling while preloader is visible */
html.preloading, body.preloading {
  height: 100%;
  overflow: hidden;
}

.preloader-inner {
  will-change: transform, opacity;
  transform-origin: center center;
}

/* Subtle floating idle motion */
@keyframes floatUpDown {
  0%   { transform: translateY(0) }
  50%  { transform: translateY(-6px) }
  100% { transform: translateY(0) }
}
#balajiMorph {
  animation: floatUpDown 3s ease-in-out infinite;
}

/* When the app is ready, fade preloader out AFTER the logo flies away */
.app-ready #preloader {
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
}

/* Respect reduced motion */
@media (prefers-reduced-motion: reduce) {
  #balajiMorph, .preloader-inner { animation: none !important; }
  * { transition: none !important; }
}
#preloader {
  position: fixed;
  inset: 0;
  background: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}
#balajiMorph { width: 140px; height: 140px; }


    /* Responsive styles */
    @media (max-width: 1300px) {
        .mega-menu {
            width: 650px;
        }
    }

    @media (max-width: 992px) {
        .search-box {
            display: none;
        }

        .mega-menu {
            width: 500px;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .headerText {
            font-size: 16px !important;
        }

        .nav-link {
            font-size: 16px;
        }

        .logo {
            width: 130px;
        }

        .mega-menu {
            width: 350px;
            grid-template-columns: 1fr;
            left: 0;
            transform: translateX(0);
        }

        .mega-menu::before {
            left: 30px;
        }
    }

    @media (max-width: 576px) {
        .search-box input {
            width: 140px;
        }

        .nav-link {
            font-size: 14px;
        }

        .header-Side {
            font-size: 13px !important;
        }
    }

    /* Improved mobile nav */
    .mobile-nav {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 15px;
        margin-top: 10px;
    }

    .mobile-nav .nav-link {
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 16px;
    }

    .mobile-nav .nav-link:last-child {
        border-bottom: none;
    }

    /* Category link arrow */
    .nav-link.dropdown-toggle i {
        margin-left: 5px;
        font-size: 14px;
        transition: transform 0.3s ease;
    }

    .nav-item-with-mega:hover .nav-link.dropdown-toggle i {
        transform: rotate(180deg);
    }
</style>
<!-- PRELOADER -->
<!-- <div id="preloader" aria-hidden="true">
  <div class="preloader-inner">
    <svg id="balajiMorph" viewBox="0 0 200 200" width="160" height="160" role="img">
      <path id="morphPath"
            fill="#111"
            d="M60,150 L60,112 L78,112 L78,62 Q78,48 92,48 L138,48 Q150,48 150,60 L150,112 L158,112 L158,130 L112,130 L112,150 L96,150 L96,130 L60,130 Z" />
    </svg>
  </div>
</div> -->


<!-- Marquee Start -->
<!-- <div style="background: #845848;
    color: #fff;
    font-size: 14px;
    padding: 6px 0;
    text-align: center;">
    <marquee behavior="scroll" direction="left" scrollamount="5">
        100% MONEY BACK GUARANTEE &nbsp; | &nbsp; FREE SHIPPING ON ORDER OVER ₹3000 &nbsp; | &nbsp; ONLINE SUPPORT 24/7
    </marquee>
</div> -->
<!-- Marquee End -->
<header class="header-section">
    <div class="container-fluid px-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <!-- Logo -->
            <div class="d-flex align-items-center flex-shrink-0">
                <a href="index.php"><img class="logo img-responsive" src="img/balaji/balaji-furniture-2048x1368.png"
                    alt="Balaji Furniture" /></a>
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
                            CATEGORY <i class="fas fa-chevron-down"></i>
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
                                                    <div class="img-bg"><img src="<?php echo htmlspecialchars($imagePath); ?>"
                                                            alt="<?php echo $name; ?>"></div>
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
                                <ul class="category-list">
                                    <li><span>No categories found.</span></li>
                                </ul>
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
                        <input type="text" name="search" id="searchInput" placeholder="Search products..."
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                            autocomplete: "off" spellcheck="false"
                            style="width:100%; padding:10px 40px 10px 14px; border:1px solid #ddd; border-radius:25px; font-size:15px;">

                        <input type="hidden" name="category"
                            value="<?php echo isset($category_name) ? htmlspecialchars($category_name) : ''; ?>">
                        <input type="hidden" name="sort_by"
                            value="<?php echo isset($sort_by) ? htmlspecialchars($sort_by) : ''; ?>">
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
    <button class="btn btn-dark" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>
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
                            <div style="font-weight:700; font-size:14px; padding:6px 0; border-bottom:1px solid #eee;">
                                <?php echo htmlspecialchars($mainTitle); ?>
                            </div>
                            <?php foreach ($items as $it):
                                $name = htmlspecialchars($it['name']);
                                $imagePath = categoryImagePath($it['image']);
                                ?>
                                <a class="nav-link" href="shop.php?category=<?php echo urlencode($name); ?>#product-list"
                                    style="display:flex; align-items:center; gap:10px;">
                                    <span class="img-bg" style="width:28px; height:28px; border-radius:6px; overflow:hidden;">
                                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo $name; ?>"
                                            style="width:100%; height:100%; object-fit:cover;">
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
<!-- Floating WhatsApp Button -->
<a
  href="https://wa.me/918979892185"

  class="whatsapp-float"
  target="_blank"
  rel="noopener"
  aria-label="Chat on WhatsApp"
>
  <!-- Inline WhatsApp SVG (no external icons needed) -->
  <svg viewBox="0 0 32 32" aria-hidden="true">
    <path d="M19.11 17.19c-.27-.14-1.58-.78-1.83-.87-.25-.09-.44-.14-.62.14-.18.27-.71.87-.87 1.05-.16.18-.32.2-.59.07-.27-.14-1.12-.41-2.13-1.3-.79-.7-1.33-1.56-1.49-1.82-.16-.27-.02-.41.12-.54.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.62-1.49-.85-2.05-.22-.53-.44-.46-.62-.46-.16-.02-.34-.02-.52-.02-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.29s.99 2.65 1.13 2.83c.14.18 1.95 2.98 4.73 4.06.66.27 1.18.43 1.58.55.66.21 1.26.18 1.73.11.53-.08 1.58-.64 1.8-1.25.22-.62.22-1.15.16-1.25-.05-.11-.23-.18-.5-.32zM16.01 3.2c-7.07 0-12.8 5.73-12.8 12.8 0 2.26.6 4.37 1.66 6.2L3.2 28.8l6.77-1.63c1.76.96 3.78 1.51 5.94 1.51 7.07 0 12.8-5.73 12.8-12.8s-5.73-12.8-12.8-12.8zm0 22.98c-1.93 0-3.72-.58-5.22-1.58l-.37-.24-4.02.97.98-3.92-.25-.4a9.98 9.98 0 0 1-1.6-5.34c0-5.53 4.5-10.02 10.02-10.02S26.03 9.14 26.03 14.67 21.54 26.18 16.01 26.18z"/>
  </svg>
</a>

<style>
.whatsapp-float {
  position: fixed;
  right: 18px;          /* distance from right edge */
  bottom: 88px;         /* distance from bottom */
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #25D366;  /* WhatsApp green */
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  z-index: 9999;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.whatsapp-float svg {
  width: 28px;
  height: 28px;
  fill: #fff;
}

.whatsapp-float:hover {
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 12px 26px rgba(0,0,0,0.3);
}

/* Optional subtle pulse on mobile to draw attention */
@media (max-width: 576px) {
  .whatsapp-float {
    width: 52px;
    height: 52px;
    right: 14px;
    bottom: 14px;
    animation: wapp-pulse 2.5s infinite;
  }
  .whatsapp-float svg { width: 26px; height: 26px; }
}

@keyframes wapp-pulse {
  0%, 100% { box-shadow: 0 8px 20px rgba(37,211,102,0.35); }
  50%      { box-shadow: 0 8px 28px rgba(37,211,102,0.55); }
}
</style>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const mobileNav = document.getElementById('mobileNav');
        if (mobileToggle && mobileNav) {
            mobileToggle.addEventListener('click', function () {
                mobileNav.classList.toggle('d-none');
            });
        }


        // Preloader
        window.addEventListener("load", function () {
            const preloader = document.getElementById("preloader");
            preloader.classList.add("hidden");
        });
    });
</script>




<!-- Floating WhatsApp Button -->
<a
  href="https://wa.me/918979892185"

  class="whatsapp-float"
  target="_blank"
  rel="noopener"
  aria-label="Chat on WhatsApp"
>
  <!-- Inline WhatsApp SVG (no external icons needed) -->
  <svg viewBox="0 0 32 32" aria-hidden="true">
    <path d="M19.11 17.19c-.27-.14-1.58-.78-1.83-.87-.25-.09-.44-.14-.62.14-.18.27-.71.87-.87 1.05-.16.18-.32.2-.59.07-.27-.14-1.12-.41-2.13-1.3-.79-.7-1.33-1.56-1.49-1.82-.16-.27-.02-.41.12-.54.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.62-1.49-.85-2.05-.22-.53-.44-.46-.62-.46-.16-.02-.34-.02-.52-.02-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.29s.99 2.65 1.13 2.83c.14.18 1.95 2.98 4.73 4.06.66.27 1.18.43 1.58.55.66.21 1.26.18 1.73.11.53-.08 1.58-.64 1.8-1.25.22-.62.22-1.15.16-1.25-.05-.11-.23-.18-.5-.32zM16.01 3.2c-7.07 0-12.8 5.73-12.8 12.8 0 2.26.6 4.37 1.66 6.2L3.2 28.8l6.77-1.63c1.76.96 3.78 1.51 5.94 1.51 7.07 0 12.8-5.73 12.8-12.8s-5.73-12.8-12.8-12.8zm0 22.98c-1.93 0-3.72-.58-5.22-1.58l-.37-.24-4.02.97.98-3.92-.25-.4a9.98 9.98 0 0 1-1.6-5.34c0-5.53 4.5-10.02 10.02-10.02S26.03 9.14 26.03 14.67 21.54 26.18 16.01 26.18z"/>
  </svg>
</a>

<style>
.whatsapp-float {
  position: fixed;
  right: 18px;          /* distance from right edge */
  bottom: 88px;         /* distance from bottom */
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #25D366;  /* WhatsApp green */
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  z-index: 9999;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.whatsapp-float svg {
  width: 28px;
  height: 28px;
  fill: #fff;
}

.whatsapp-float:hover {
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 12px 26px rgba(0,0,0,0.3);
}

/* Optional subtle pulse on mobile to draw attention */
@media (max-width: 576px) {
  .whatsapp-float {
    width: 52px;
    height: 52px;
    right: 14px;
    bottom: 14px;
    animation: wapp-pulse 2.5s infinite;
  }
  .whatsapp-float svg { width: 26px; height: 26px; }
}

@keyframes wapp-pulse {
  0%, 100% { box-shadow: 0 8px 20px rgba(37,211,102,0.35); }
  50%      { box-shadow: 0 8px 28px rgba(37,211,102,0.55); }
}
</style>




<!-- Floating Instagram Button -->
<a
  href="https://instagram.com/yourusername"
  class="instagram-float"
  target="_blank"
  rel="noopener"
  aria-label="Visit Instagram"
>
  <!-- Inline Instagram SVG -->
  <svg viewBox="0 0 448 512" aria-hidden="true">
    <path fill="currentColor"
      d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9
      0 63.6 51.3 114.9 114.9 114.9
      63.6 0 114.9-51.3 114.9-114.9
      0-63.6-51.3-114.9-114.9-114.9zm0 
      189.6c-41.2 0-74.7-33.5-74.7-74.7
      0-41.2 33.5-74.7 74.7-74.7
      41.2 0 74.7 33.5 74.7 74.7
      0 41.2-33.5 74.7-74.7 74.7zm146.4-194.3
      c0 14.9-12 26.9-26.9 26.9-14.9 0-26.9-12-26.9-26.9
      s12-26.9 26.9-26.9c14.9 0 26.9 12 26.9 26.9zm76.1 
      27.2c-1.7-35.9-9.9-67.7-36.2-93.9
      -26.2-26.2-58-34.4-93.9-36.2
      -37-2.1-147.9-2.1-184.9 0
      -35.9 1.7-67.7 9.9-93.9 36.2
      -26.2 26.2-34.4 58-36.2 93.9
      -2.1 37-2.1 147.9 0 184.9
      1.7 35.9 9.9 67.7 36.2 93.9
      26.2 26.2 58 34.4 93.9 36.2
      37 2.1 147.9 2.1 184.9 0
      35.9-1.7 67.7-9.9 93.9-36.2
      26.2-26.2 34.4-58 36.2-93.9
      2.1-37 2.1-147.8 0-184.8zM398.8 
      388c-7.8 19.6-22.9 34.7-42.6 42.6
      -29.5 11.7-99.5 9-132.1 9s-102.7 
      2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6
      -11.7-29.5-9-99.5-9-132.1s-2.6-102.7 
      9-132.1c7.8-19.6 22.9-34.7 42.6-42.6
      29.5-11.7 99.5-9 132.1-9s102.7-2.6 
      132.1 9c19.6 7.8 34.7 22.9 42.6 42.6
      11.7 29.5 9 99.5 9 132.1s2.7 
      102.7-9 132.1z"/>
  </svg>
</a>

<style>
.instagram-float {
  position: fixed;
  right: 18px;
  bottom: 160px;   /* adjust above WhatsApp if using both */
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: radial-gradient(circle at 30% 107%, 
    #fdf497 0%, #fdf497 5%, 
    #fd5949 45%, #d6249f 60%, #285AEB 90%);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  z-index: 9999;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.instagram-float svg {
  width: 28px;
  height: 28px;
  color: #fff;
}

.instagram-float:hover {
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 12px 26px rgba(0,0,0,0.3);
}

@media (max-width: 576px) {
  .instagram-float {
    width: 52px;
    height: 52px;
    right: 14px;
    bottom: 80px; /* keep above WhatsApp button */
    animation: insta-pulse 2.5s infinite;
  }
  .instagram-float svg { width: 26px; height: 26px; }
}

@keyframes insta-pulse {
  0%, 100% { box-shadow: 0 8px 20px rgba(214,36,159,0.35); }
  50%      { box-shadow: 0 8px 28px rgba(214,36,159,0.55); }
}
</style>
<!-- Anime.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>

<script>
(function() {
  const pathEl = document.getElementById('morphPath');
  const inner  = document.querySelector('#preloader .preloader-inner');
  const overlay = document.getElementById('preloader');

  // Paths
  const CHAIR =
    "M60,150 L60,112 L78,112 L78,62 Q78,48 92,48 L138,48 Q150,48 150,60 L150,112 L158,112 L158,130 L112,130 L112,150 L96,150 L96,130 L60,130 Z";
  const B =
    "M90,50 L110,50 L110,98 Q118,86 136,86 Q158,86 158,106 Q158,132 136,132 Q118,132 110,118 L110,150 L90,150 Z";

  // Loop animation (chair ↔ B)
  const loopTL = anime.timeline({ loop: true, easing: 'easeInOutCubic' });
  loopTL
    .add({ targets: pathEl, d: [{ value: CHAIR }], duration: 1 })
    .add({ targets: pathEl, d: [{ value: B }], duration: 800 })
    .add({ targets: pathEl, d: [{ value: CHAIR }], duration: 800 });

  // Exit animation
  function flyToHeader() {
    loopTL.pause();

    const target = document.getElementById('siteLogo');
    const targetRect = target
      ? target.getBoundingClientRect()
      : { left: 20, top: 20, width: 60, height: 60 };

    const svgRect = inner.getBoundingClientRect();
    const translateX = (targetRect.left + targetRect.width/2) - (svgRect.left + svgRect.width/2);
    const translateY = (targetRect.top + targetRect.height/2) - (svgRect.top + svgRect.height/2);
    const scale = Math.min(targetRect.width / svgRect.width, targetRect.height / svgRect.height);

    // Fly effect
    anime({
      targets: inner,
      translateX: translateX,
      translateY: translateY,
      scale: scale,
      duration: 1000,
      easing: 'easeInOutExpo',
      complete: () => {
        // Fade overlay out
        overlay.style.transition = "opacity .6s ease";
        overlay.style.opacity = 0;
        setTimeout(() => overlay.remove(), 600);
      }
    });
  }

  // Run on full page load
  window.addEventListener('load', flyToHeader);

  // Safety: force close after 5s
  setTimeout(() => {
    if (document.body.contains(overlay)) flyToHeader();
  }, 5000);
})();
</script>