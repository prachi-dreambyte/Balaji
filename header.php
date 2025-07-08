<?php
include 'connect.php'; // replace with actual path if needed

// Step 2: Write the query
$sql = "SELECT * FROM categories";

// Step 3: Execute the query
$result = mysqli_query($conn, $sql);

// Step 4: Check for errors (optional but good practice)
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

?>


<header class="header-4">
				<div class="header-top">
					<div class="container">
						<div class="row">
							<div class="col-md-6 col-sm-7 d-none d-sm-block">
								<div class="language">
									<div class="current">
										<span>English</span>
									</div>
									<ul class="lan-cur">
										<li class="selected">
											<img src="img/1/1.jpg" alt="en" />
											<span>English</span>
										</li>
										<li>
											<img src="img/1/2.jpg" alt="ar" />
											<a href="#" rel="alternate" title="اللغة العربية (Arabic)">
												<span>اللغة العربية</span>
											</a>
										</li>
									</ul>
								</div>
								<div class="currencies">
									<div class="current">
										<span class="cur-label">Currency :</span>
										<strong>GBP</strong>
									</div>
									<ul class="lan-cur">
										<li>
											<a href="#"> Dollar (USD) </a>
										</li>
										<li>
											<a href="#"> Pound (GBP) </a>
										</li>
									</ul>
								</div>
							</div>
							<div class="col-md-6 col-sm-5 col-xs-12">
								<div class="header-userinfo">
									<ul class="header-links">
										<li class="first">
											<a class="link-myaccount" title="My account" href="#"> My account </a>
										</li>
										<li>
											<a class="link-wishlist wishlist_block" title="My wishlist" href="#">My wishlist</a>
										</li>
										<li>
                                          <a class="link-checkout" title="Checkout" href="http://localhost/vonia/checkout.php">Checkout</a>
                                        </li>

										<li>
											<a class="login" title="Log in to your customer account" rel="nofollow" href="http://localhost/vonia/loginSignUp/login.php">Log in</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="header-middle">
					<div class="container">
						<div class="row align-items-center">
							<div class="col-md-4 col-sm-6 col-xs-12 d-none d-md-block">
								<div class="search-block-top">
									<input type="text" value="" placeholder="Search" >
									<button class="btn btn-default " name="submit_search" type="submit"></button>
								</div>
							</div>
							<div class="col-md-4 col-sm-6 col-6">
								<div class="pos-logo">
									<a href="index.php">
										<img class="logo img-responsive" src="img/logo-4.png" alt="" />
									</a>
								</div>
							</div>						
							<div class="col-md-4 col-sm-6 col-6">
								<div class="shopping-cart">
									<a href="#" rel="nofollow" title="View my shopping cart">
										<b>cart 2 item</b>
									</a>
									<div class="top-cart-content">
										<div class="media header-middle-checkout">
											<div class="media-left check-img">
												<a href="#">
													<img src="img/cart-img/blouse.jpg" alt="" />
												</a>
											</div>
											<div class="media-body checkout-content">
												<h4 class="media-heading">
													<span class="cart-count">1x</span>
													<a href="#">Suspendisse</a>
													<span class="btn-remove checkout-remove" title="remove this product from my cart">
														<i class="fa fa-times" aria-hidden="true"></i>
													</span>
												</h4>
												<p class="product-detail"><a href="#" title="product detail">S, Yellow</a></p>
												<p>£ 34.78</p>
											</div>
										</div>
										<div class="media header-middle-checkout last-child">
											<div class="media-left check-img">
												<a href="#">
													<img src="img/cart-img/printed-summer-dress.jpg" alt="" />
												</a>
											</div>
											<div class="media-body checkout-content">
												<h4 class="media-heading">
													<span class="cart-count">1x</span>
													<a href="#">Suspendisse</a>
													<span class="btn-remove checkout-remove" title="remove this product from my cart">
														<i class="fa fa-times" aria-hidden="true"></i>
													</span>
												</h4>
												<p class="product-detail"><a href="#" title="product detail">S, Black</a></p>
												<p>£ 32.40</p>
											</div>
										</div>
										<div class="cart-total">
											<span>Total</span>
											<span><b>£ 67.18</b></span>
										</div>
										<div class="checkout">
											<a href="#">
												<span>checkout
													<i class="fa fa-angle-right" aria-hidden="true"></i></span>
											</a>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>
				</div>
				<!-- mainmenu-area-start -->
				<div class="main-menu-area d-none d-lg-block">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<div class="main-menu">
									<nav>
										<ul>
											<li>
												<a class="active" href="index.php">home</a>
												
											</li>
	<li>
    <a href="shop.php">CATEGORY</a>
    <div class="mega-menu">
        <span style="display: grid; grid-template-columns: 200px 200px; gap: 10px;">
    <?php
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <?php echo $row['category_name']; ?>
        </a>
    <?php
        }
    } else {
    ?>
        <span>Category not found.</span>
    <?php
    }
    ?>
</span>

    </div>
</li>



		
										<li>
											<a href="shop.php">OFFER</a>
											
										</li>
										<li>
                                          <a href="contact.php">CONTACT</a>
                                        </li>
										<li>
											<a href="#">ABOUT</a>
											<div class="version pages">
												<span>
													<a href="blog.php">Blog</a>
													<!-- <a href="contact-us.php">Contact Us</a> -->
													<a class="link-checkout" title="Checkout" href="http://localhost/vonia/checkout.php">Checkout</a>
													<a href="my-account.php">My account</a>
													<a href="product-details.php">Product details</a>
													<a href="shop.php">Shop Page</a>
													<a href="shopping-cart.php">Shoping Cart</a>
													<a href="wishlist.php">Wishlist</a>
													<!-- <a href="404.php">404 Error</a> -->
												</span>
										
												</div>
											</li>
										</ul>
									</nav>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- mainmenu-area-end -->
				<!-- mobile-menu-area-start -->
				<div class="mobile-menu-area d-lg-none d-block">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<div class="mobile_menu">
									<nav id="mobile_menu_active">
										<ul>
											<li><a href="index.php">Home</a>
												<!-- <ul>
													<li><a href="index11.php">Home 1</a></li>
													<li><a href="index-2.php">Home 2</a></li>
													<li><a href="index-3.php">Home 3</a></li>
													<li><a href="index.php">Home 4</a></li>
												</ul> -->
											</li>
											<li>
    <a href="shop.php">CATEGORY</a>
    <div class="mega-menu">
        <span style="display: grid; grid-template-columns: 200px 200px; gap: 10px;">
    <?php
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <a href="#" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <?php echo $row['category_name']; ?>
        </a>
    <?php
        }
    } else {
    ?>
        <span>Category not found.</span>
    <?php
    }
    ?>
</span>

    </div>
</li>

										
										<li>
											<a href="shop.php">OFFER</a>
											
										</li>
										<li>
                                          <a href="contact.php">CONTACT</a>
                                         </li>
										<li>
											<a href="#">ABOUT</a>
											<div class="version pages">
												<span>
													<a href="blog.php">Blog</a>
													
													<a class="link-checkout" title="Checkout" href="http://localhost/vonia/checkout.php">Checkout</a>
													<a href="my-account.php">My account</a>
													<a href="product-details.php">Product details</a>
													<a href="shop.php">Shop Page</a>
													<a href="shopping-cart.php">Shoping Cart</a>
													<a href="wishlist.php">Wishlist</a>
													<a href="404.php">404 Error</a>
												</span>
										
											</li>
										</ul>
									</nav>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- mobile-menu-area-end -->
			</header>
