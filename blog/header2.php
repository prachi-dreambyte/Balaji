<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
include '../connect.php';

// Fetch categories for menu
$sql = "SELECT * FROM categories";
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


if (isset($_GET['action']) && $_GET['action'] === 'logout') {
	session_destroy();
	header('Location: index.php');
	exit;
}
?>
<style>
  .header-section{
    padding: 10px 0px !important;
  }
  .header-Side{
    font-size: 15px !important;
    text-decoration: none !important;
  }
    body {
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', sans-serif;
}
.headerText{
  font-size: 18px !important;
  padding-right: 10px !important;
}

.logo {
    width: 160px;;
  height: auto;
}
.logo:hover {
  transform: scale(1.05);
  transition: transform 0.3s ease;
  filter: drop-shadow(0 0 5px #845848);
}

.nav-link {
  font-weight: 500;
  letter-spacing: 0.5px;
  padding: 0;
  font-size:20px;
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
  background-color: #845848;
  transition: width 0.3s ease;
}

.nav-link.active {
  color: #845848 !important;
}
.nav-link:hover {
  color: #845848 !important;
  transform: scale(1.05) !important;
}


.search-box {
  position: relative;
}

.search-box input {
  padding: 5px 30px 5px 10px;
  border-radius: 3px;
  border: none;
  outline: none;
  width: 180px;
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
.mega-menu {
  position: absolute;
  background-color: #000; /* black background */
  color: #fff; /* white text */
  padding: 10px;
  z-index: 1000;
  display: none;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
   transition: all 0.4s ease;
  border-radius: 5px;
}
.nav-item-with-mega:hover .mega-menu {
  display: block;
  font-size: 17px;
    padding: 25px 20px;
    transform: translateY(5px);
  background-color: #111; /* slightly lighter than pure black */
  box-shadow: 0px 5px 15px rgba(0,0,0,0.5);
}
.nav-link:hover::after,
.nav-link.active::after {
  width: 100%;
}

/* nav .nav-link:hover + .mega-menu, .mega-menu:hover {
    display: block;
    margin-top: 43px;
    padding: 15px 35px;
    font-size: 15px;
} */
.mega-menu a {
  color: #fff !important; /* ensure text is white */
  text-decoration: none;
  padding: 5px 0;
}

.mega-menu a:hover {
  color: #845848 !important; /* hover color */
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
  background-color: #845848;
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
@media (max-width: 1300px) {
}
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

</style>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Balaji Furniture</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/responsive-enhancements.css">
</head>
<body>

 <header class="bg-black text-white  header-section">
  <div class="container-fluid px-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

      <!-- Logo -->
      <div class="d-flex align-items-center flex-shrink-0">
        <img class="logo img-responsive" src="../img/balaji-logo-top.png" alt=""/>
      </div>

      <!-- Nav (desktop only) -->
       <?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
      <nav class="d-none d-xl-flex flex-wrap justify-content-center gap-3 flex-grow-1">
       <p class="headerText"> <a href="../index.php" class="nav-link text-white <?php echo ($current_page == '../index.php') ? 'active' : ''; ?>">HOME</a></p>
       <div class="nav-item-with-mega"> <p class="headerText">   <a href="../shop.php" class="nav-link text-white <?php echo ($current_page == 'shop.php') ? 'active' : ''; ?>">CATEGORY</a></p>
                                        <div class="mega-menu pt-4">
                                            <span style="display: grid; grid-template-columns: 250px 250px; gap: 5px;">
                                                <?php
                                                if ($categories_result) {
                                                    while ($row = mysqli_fetch_assoc($categories_result)) {
                                                ?>
                                                        <a href="shop.php?category=<?php echo $row['category_name']; ?>#product-list"
                                                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            <?php echo $row['category_name']; ?>
                                                        </a>
                                                <?php
                                                    }
                                                } else {
                                                    echo "<span>Category not found.</span>";
                                                }
                                                ?>
                                            </span>
                                        </div>
                                      </div>
        <p class="headerText"> <a href="../index.php" class="nav-link text-white <?php echo ($current_page == '../index.php') ? 'active' : ''; ?>">OFFER</a></p>
        <p class="headerText">   <a href="contact.php" class="nav-link text-white <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">CONTACT</a></p>
         <p class="headerText"><a href="../about-us.php" class="nav-link text-white <?php echo ($current_page == '../about-us.php') ? 'active' : ''; ?>">ABOUT US</a></p>
         <p class="headerText">  <a href="../blog.php" class="nav-link text-white <?php echo ($current_page == '../blog.php') ? 'active' : ''; ?>">BLOG</a></p>
      </nav>

      <!-- Search + Account Icons -->
      <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
        <div class="search-box d-none d-md-block">
          <input type="text" placeholder="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
           <input type="hidden" name="category" value="<?php echo isset($category_name) ? htmlspecialchars($category_name) : ''; ?>">
            <input type="hidden" name="sort_by" value="<?php echo isset($sort_by) ? htmlspecialchars($sort_by) : ''; ?>">
            <input type="hidden" name="limit" value="<?php echo isset($limit) ? (int)$limit : 12; ?>">
          <i class="fas fa-search"></i>
        </div>
        <a href="../my-account.php" class="text-white d-flex align-items-center header-Side"><i class="fas fa-cog me-1 "></i> Account</a>
        
          <div class="cart-wrapper position-relative">
        <a href="../shopping-cart.php" class="text-white d-flex align-items-center header-Side">
                <i class="fas fa-shopping-cart me-1"></i>
                Cart <?php echo $cart_count . ' item' . ($cart_count > 1 ? 's' : ''); ?>
            </a>
                                <!-- Dropdown Cart -->
                       
  <div class="top-cart-content">
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
                        <a href="#"><img src="<?php echo $product_image; ?>" alt="" style="width:50px;height:50px;" /></a>
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
                <a href="checkout.php"><span>Checkout <i class="fa fa-angle-right" aria-hidden="true"></i></span></a>
            </div>
    <?php
        } else {
            echo "<p style='padding:10px;'>Cart is empty.</p>";
        }
    } else {
        echo "<p style='padding:10px;'>Please login to see your cart.</p>";
    }
    ?>
  </div>
                        </div>
                         <?php if (isset($_SESSION['user'])): ?>
        <a href="?action=logout"  title="Log out of your customer account" class="text-white d-flex align-items-center header-Side">
            <i class="fas fa-lock me-1"></i> Log Out</a>
         <?php else: ?>
         <a href="loginSignUp/login.php"  title="Log in to your customer account" class="text-white d-flex align-items-center header-Side">
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
        <a href="../index.php" class="nav-link text-white">HOME</a>
        <a href="shop.php" class="nav-link text-white">CATEGORY</a>
                                        <div class="mega-menu pt-4">
                                            <span style="display: grid; grid-template-columns: 250px 250px; gap: 5px;">
                                                <?php
                                                if ($categories_result) {
                                                    while ($row = mysqli_fetch_assoc($categories_result)) {
                                                ?>
                                                        <a href="shop.php?category=<?php echo $row['category_name']; ?>#product-list"
                                                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            <?php echo $row['category_name']; ?>
                                                        </a>
                                                <?php
                                                    }
                                                } else {
                                                    echo "<span>Category not found.</span>";
                                                }
                                                ?>
                                            </span>
                                        </div>
        <a href="index.php#deals" class="nav-link text-white">OFFER</a>
        <a href="contact.php" class="nav-link text-white">CONTACT</a>
        <a href="about-us.php" class="nav-link text-white">ABOUT US</a>
        <a href="../blog.php" class="nav-link text-white">BLOG</a>
      </nav>
    </div>
  </div>
</header>


  <script>
    const toggleBtn = document.getElementById('mobileToggle');
    const mobileNav = document.getElementById('mobileNav');
    toggleBtn.addEventListener('click', () => {
      mobileNav.classList.toggle('d-none');
    });
  </script>
</body>
</html>


<script>
// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainMenu = document.querySelector('.main-menu');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            mainMenu.classList.toggle('active');
            mobileMenuToggle.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.main-menu') && !event.target.closest('.mobile-menu-toggle')) {
            mainMenu.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
        }
    });
    
    // Close mobile menu when window is resized to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            mainMenu.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
        }
    });
    
    // Close mobile menu when clicking on a menu item
    const menuItems = document.querySelectorAll('.main-menu nav ul li a');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            mainMenu.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
        });
    });
});
</script>
   