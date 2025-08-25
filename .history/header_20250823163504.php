<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'connect.php';

/* -------------------- CART TOTALS -------------------- */
$cart_count = 0;
$total_price = 0;

if (!empty($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare("
        SELECT 
            COALESCE(SUM(c.quantity),0) AS total_items,
            COALESCE(SUM(c.quantity * p.price),0) AS total_price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $totals = $stmt->get_result()->fetch_assoc();
    $cart_count  = (int)($totals['total_items'] ?? 0);
    $total_price = (float)($totals['total_price'] ?? 0.0);
    $stmt->close();
}

/* -------------------- DYNAMIC CATEGORY GROUPS -------------------- */
/*
    DB table: categories
    Columns: id, category_name, Main_Category_name, category_image, banner_image, created_at
    We will group by Main_Category_name and render one column per group.
*/
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

/* Helper: resolve image path safely */
function categoryImagePath($relPath) {
    $relPath = ltrim($relPath ?? '', '/');
    if ($relPath === '') return 'assets/images/placeholder.png';
    // Try admin/<db path>
    $diskPath = __DIR__ . '/admin/' . $relPath;
    if (file_exists($diskPath)) {
        return 'admin/' . $relPath;
    }
    // Try as given relative (for older uploads)
    $diskPath2 = __DIR__ . '/' . $relPath;
    if (file_exists($diskPath2)) {
        return $relPath;
    }
    return 'assets/images/placeholder.png';
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* ====== Base Header Styling ====== */
.header-section {
  position: fixed; top: 0; left: 0; width: 100%; z-index: 1050;
  padding: 10px 0 !important; background-color: #F5F6F2 !important; color: #363636 !important; border: none;
}
body { margin:0; padding:0; font-family: 'Segoe UI', sans-serif; padding-top: 88px; }
.logo { width:160px; height:auto; }
.logo:hover { transform:scale(1.05); transition: transform .3s ease; filter: drop-shadow(0 0 5px black); }
.headerText { font-size:18px !important; padding-right:10px !important; font-weight:500 !important; }
.header-Side { font-size:15px !important; text-decoration:none !important; }

.nav-link {
  font-weight:500; letter-spacing:.5px; padding:0; font-size:20px; position:relative;
  transition: color .3s ease, transform .3s ease;
}
.nav-link::after {
  content:""; position:absolute; left:0; bottom:-5px; width:0%; height:2px; background:#000; transition: width .3s ease;
}
.nav-link.active { color:#000 !important; }
.nav-link:hover { color:#845848 !important; transform: scale(1.05) !important; }
.dropdown-toggle::after { display:none !important; }
a { transition: all .3s ease-in-out; }

/* ====== Search ====== */
.search-box { position:relative; }
.search-box input {
  padding:10px 30px 10px 10px; border-radius:5px; border:none; outline:none; width:210px;
  box-shadow:1px 2px 3px #dddde1; font-size:15px;
}
.search-box i {
  position:absolute; top:50%; right:10px; transform: translateY(-50%);
  transition: color .3s ease; color:#333;
}
.search-box:hover i { color:#845848; }

/* ====== Category Mega Menu ====== */
.nav-item-with-mega { position:relative; }
.nav-item-with-mega:hover .mega-menu { display: grid; opacity:1; visibility:visible; }

.mega-menu {
  position:absolute; top:100%; left:0;
  background:#fff; border:1px solid #ddd; border-radius:8px; padding:15px;
  display:none; opacity:0; visibility:hidden; transition: all .25s ease-in-out; z-index:1000;
  min-width: 540px;
  /* Dynamic columns based on number of main categories */
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: minmax(240px, 1fr);
  gap: 18px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* a single column (one main category group) */
.category-column { display:flex; flex-direction:column; gap:10px; min-width: 240px; }
.category-column h3 {
  font-size:15px; font-weight:700; margin:0 0 6px 0; padding-bottom:6px; border-bottom:1px solid #eee; color:#333;
}

/* the list inside a column */
.category-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:8px; }
.category-list li { display:flex; align-items:center; }
.category-list a {
  display:flex; align-items:center; gap:10px; text-decoration:none; color:#333;
  padding:6px 8px; border-radius:6px; transition: background .2s ease;
}
.category-list a:hover { background:#f5f5f5; }

/* image box */
.img-bg {
  width:36px; height:36px; border-radius:6px; overflow:hidden; flex-shrink:0; background:#f9f9f9;
  display:flex; align-items:center; justify-content:center;
}
.img-bg img { width:100%; height:100%; object-fit:cover; }
.category-list h4 { font-size:14px; margin:0; font-weight:500; }

/* ====== Cart mini (kept minimal/hidden by default) ====== */
.cart-wrapper { position:relative; }
.top-cart-content {
  display:none; position:absolute; right:0; top:100%;
  background:#000; color:#fff; width:300px; padding:15px; border:1px solid #333; z-index:1000;
}
.cart-wrapper:hover .top-cart-content { display:block; }

/* ====== Suggestions (unused JS placeholder) ====== */
#suggestions{
  position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ddd; border-radius:2px;
  box-shadow:0 4px 10px rgba(0,0,0,0.08); z-index:1000; display:none; overflow:hidden;
}
.suggestion-item{ padding:10px 14px; font-size:15px; color:#333; cursor:pointer; transition: background .2s, padding-left .2s; border-bottom:1px solid #f5f5f5; }
.suggestion-item:last-child{ border-bottom:none; }
.suggestion-item:hover{ background:#f9f9f9; padding-left:18px; }
.suggestion-highlight{ font-weight:600; color:#007bff; }

/* ====== Preloader ====== */
#preloader{
  position:fixed; inset:0; background:#fff; display:flex; justify-content:center; align-items:center; z-index:9999;
  transition: opacity .5s ease, visibility .5s ease;
}
#preloader.hidden{ opacity:0; visibility:hidden; }

/* ====== Responsive ====== */
@media (max-width: 992px){ .search-box{ display:none; } }
@media (max-width: 576px){
  .search-box input{ width:140px; }
  .nav-link{ font-size:14px; }
}
/* Mobile dropdown rendering (stack columns) */
@media (max-width: 1200px){
  .mega-menu{ grid-auto-flow: row; grid-auto-rows: auto; grid-template-columns: 1fr; min-width: 260px; }
}
</style>

<!-- Preloader -->
<div id="preloader">
  <img src="img/balaji/loading.gif" alt="Loading..." />
</div>

<!-- Marquee -->
<div style="background:#845848; color:#fff; font-size:14px; padding:6px 0; text-align:center;">
  <marquee behavior="scroll" direction="left" scrollamount="5">
    100% MONEY BACK GUARANTEE &nbsp; | &nbsp; FREE SHIPPING ON ORDER OVER ₹3000 &nbsp; | &nbsp; ONLINE SUPPORT 24/7
  </marquee>
</div>

<header class="header-section">
  <div class="container-fluid px-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

      <!-- Logo -->
      <div class="d-flex align-items-center flex-shrink-0">
        <img class="logo img-responsive" src="img/balaji/balaji-furniture-2048x1368.png" alt="Balaji Furniture" />
      </div>

      <!-- Desktop Nav -->
      <nav class="d-none d-xl-flex flex-wrap justify-content-center gap-3 flex-grow-1">
        <p class="headerText"><a href="index.php" class="nav-link <?php echo ($current_page=='index.php')?'active':''; ?>">HOME</a></p>

        <!-- CATEGORY (Dynamic Mega Menu) -->
        <div class="nav-item-with-mega">
          <p class="headerText">
            <a href="shop.php" class="nav-link dropdown-toggle <?php echo ($current_page=='shop.php')?'active':''; ?>" id="categoryLink">
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

        <p class="headerText"><a href="index.php#deals" class="nav-link">OFFER</a></p>
        <p class="headerText"><a href="about-us.php" class="nav-link <?php echo ($current_page=='about-us.php')?'active':''; ?>">ABOUT US</a></p>
        <p class="headerText"><a href="blog.php" class="nav-link <?php echo ($current_page=='blog.php')?'active':''; ?>">BLOG</a></p>
        <p class="headerText"><a href="contact.php" class="nav-link <?php echo ($current_page=='contact.php')?'active':''; ?>">CONTACT</a></p>
      </nav>

      <!-- Right: Search + Account + Cart -->
      <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
        <div class="search-box d-none d-md-block" style="position:relative; max-width:300px;">
          <form id="searchForm" action="shop.php" method="get" autocomplete="off">
            <input type="text" name="search" id="searchInput" placeholder="Search products..."
              value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autocomplete="off" spellcheck="false"
              style="width:100%; padding:10px 40px 10px 14px; border:1px solid #ddd; border-radius:25px; font-size:15px;">
            <input type="hidden" name="category" value="<?php echo isset($category_name) ? htmlspecialchars($category_name) : ''; ?>">
            <input type="hidden" name="sort_by"  value="<?php echo isset($sort_by) ? htmlspecialchars($sort_by) : ''; ?>">
            <input type="hidden" name="limit"    value="<?php echo isset($limit) ? (int)$limit : 12; ?>">
            <button type="submit" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#666;">
              <i class="fas fa-search"></i>
            </button>
          </form>
          <div id="suggestions"></div>
        </div>

        <a href="my-account.php" class="d-flex align-items-center header-Side text-black"><i class="fas fa-cog me-1"></i> Account</a>

        <div class="cart-wrapper position-relative">
          <a href="shopping-cart.php" class="d-flex align-items-center header-Side text-black">
            <i class="fas fa-shopping-cart me-1"></i>
            Cart <?php echo $cart_count . ' item' . ($cart_count == 1 ? '' : 's'); ?>
          </a>
        </div>

        <?php if (!empty($_SESSION['user'])): ?>
          <a href="logout.php" class="d-flex align-items-center header-Side text-black"><i class="fas fa-lock me-1"></i> Log Out</a>
        <?php else: ?>
          <a href="loginSignUp/login.php" class="d-flex align-items-center header-Side text-black"><i class="fas fa-lock me-1"></i> Log In</a>
        <?php endif; ?>
      </div>

      <!-- Mobile toggle -->
      <div class="d-xl-none">
        <button class="btn btn-outline-light" id="mobileToggle"><i class="fas fa-bars"></i></button>
      </div>
    </div>

    <!-- Mobile Nav (stacked, grouped) -->
    <div class="mobile-nav d-xl-none mt-3 d-none" id="mobileNav">
      <nav class="nav flex-column">
        <a href="index.php" class="nav-link">HOME</a>
        <a href="shop.php" class="nav-link">CATEGORY</a>

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

        <a href="index.php#deals" class="nav-link">OFFER</a>
        <a href="contact.php" class="nav-link">CONTACT</a>
        <a href="about-us.php" class="nav-link">ABOUT US</a>
        <a href="blog.php" class="nav-link">BLOG</a>
      </nav>
    </div>
  </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Preloader hide
  window.addEventListener("load", function () {
    const preloader = document.getElementById("preloader");
    if (preloader) preloader.classList.add("hidden");
  });

  // Mobile nav toggle
  const mobileToggle = document.getElementById('mobileToggle');
  const mobileNav = document.getElementById('mobileNav');
  if (mobileToggle && mobileNav) {
    mobileToggle.addEventListener('click', function () {
      mobileNav.classList.toggle('d-none');
    });
  }

  // Optional: click-to-toggle for desktop menu (kept hover as default)
  const catLink = document.getElementById('categoryLink');
  const catMenu = document.getElementById('categoryMenu');
  if (catLink && catMenu) {
    catLink.addEventListener('click', function(e) {
      if (window.innerWidth >= 1200) { // desktop
        e.preventDefault();
        const isVisible = catMenu.style.display === 'grid';
        if (isVisible) {
          catMenu.style.display = 'none';
          catMenu.style.opacity = '0';
          catMenu.style.visibility = 'hidden';
        } else {
          catMenu.style.display = 'grid';
          catMenu.style.opacity = '1';
          catMenu.style.visibility = 'visible';
        }
      }
    });

    document.addEventListener('click', function(e){
      if (!e.target.closest('.nav-item-with-mega')) {
        catMenu.style.display = 'none';
        catMenu.style.opacity = '0';
        catMenu.style.visibility = 'hidden';
      }
    });
  }
});
</script>
