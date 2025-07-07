<!doctype html>
<html class="no-js" lang="">
    

<head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Checkout || Vonia</title>
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
		<!-- modernizr css -->
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
		<!-- header-start -->
		<?php
		include 'header.php';
		?>
		<!-- header-end -->
		<!--checkout-start-->
		<div class="checkout-top-area">
			<div class="container">
				<div class="breadcrumb-area">
					<div class="breadcrumb">
						<a href="index.php" title="Return to Home">
							<i class="icon-home"></i>
						</a>
						<span class="navigation-pipe">></span>
						<span class="navigation-page">Checkout</span>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="entry-header">
							<h1 class="entry-title">Checkout</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- coupon-area start -->
		<div class="coupon-area">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="coupon-accordion">
							<!-- ACCORDION START -->
							<h3>Returning customer? <span id="showlogin">Click here to login</span></h3>
							<div id="checkout-login" class="coupon-content">
								<div class="coupon-info">
									<p class="coupon-text">Quisque gravida turpis sit amet nulla posuere lacinia. Cras sed est sit amet ipsum luctus.</p>
									<form action="#">
										<p class="form-row-first">
											<label>Username or email <span class="required">*</span></label>
											<input type="text" />
										</p>
										<p class="form-row-last">
											<label>Password  <span class="required">*</span></label>
											<input type="text" />
										</p>
										<p class="form-row">					
											<input type="submit" value="Login" />
											<label>
												<input type="checkbox" />
												 Remember me 
											</label>
										</p>
										<p class="lost-password">
											<a href="#">Lost your password?</a>
										</p>
									</form>
								</div>
							</div>
							<!-- ACCORDION END -->	
							<!-- ACCORDION START -->
							<h3>Have a coupon? <span id="showcoupon">Click here to enter your code</span></h3>
							<div id="checkout_coupon" class="coupon-checkout-content">
								<div class="coupon-info">
									<form action="#">
										<p class="checkout-coupon">
											<input type="text" placeholder="Coupon code" />
											<input type="submit" value="Apply Coupon" />
										</p>
									</form>
								</div>
							</div>
							<!-- ACCORDION END -->						
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- coupon-area end -->
		<!-- checkout-area start -->
		<div class="checkout-area">
			<div class="container">
				<form action="#">
					<div class="row">
						<div class="col-lg-6 col-md-6">
							<div class="checkbox-form">						
								<h3>Billing Details</h3>
								<div class="row">
									<div class="col-md-12">
										<div class="country-select">
											<label>Country <span class="required">*</span></label>
											<select>
											  <option value="volvo">bangladesh</option>
											  <option value="saab">Algeria</option>
											  <option value="mercedes">Afghanistan</option>
											  <option value="audi">Ghana</option>
											  <option value="audi2">Albania</option>
											  <option value="audi3">Bahrain</option>
											  <option value="audi4">Colombia</option>
											  <option value="audi5">Dominican Republic</option>
											</select> 										
										</div>
									</div>
									<div class="col-md-6">
										<div class="checkout-form-list">
											<label>First Name <span class="required">*</span></label>										
											<input type="text" placeholder="" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="checkout-form-list">
											<label>Last Name <span class="required">*</span></label>										
											<input type="text" placeholder="" />
										</div>
									</div>
									<div class="col-md-12">
										<div class="checkout-form-list">
											<label>Company Name</label>
											<input type="text" placeholder="" />
										</div>
									</div>
									<div class="col-md-12">
										<div class="checkout-form-list">
											<label>Address <span class="required">*</span></label>
											<input type="text" placeholder="Street address" />
										</div>
									</div>
									<div class="col-md-12">
										<div class="checkout-form-list">									
											<input type="text" placeholder="Apartment, suite, unit etc. (optional)" />
										</div>
									</div>
									<div class="col-md-12">
										<div class="checkout-form-list">
											<label>Town / City <span class="required">*</span></label>
											<input type="text" placeholder="Town / City" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="checkout-form-list">
											<label>State / County <span class="required">*</span></label>										
											<input type="text" placeholder="" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="checkout-form-list">
											<label>Postcode / Zip <span class="required">*</span></label>										
											<input type="text" placeholder="Postcode / Zip" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="checkout-form-list">
											<label>Email Address <span class="required">*</span></label>										
											<input type="email" placeholder="" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="checkout-form-list">
											<label>Phone  <span class="required">*</span></label>										
											<input type="text" placeholder="Postcode / Zip" />
										</div>
									</div>
									<div class="col-md-12">
										<div class="checkout-form-list create-acc">	
											<input id="cbox" type="checkbox" />
											<label>Create an account?</label>
										</div>
										<div id="cbox_info" class="checkout-form-list create-account">
											<p>Create an account by entering the information below. If you are a returning customer please login at the top of the page.</p>
											<label>Account password  <span class="required">*</span></label>
											<input type="password" placeholder="password" />	
										</div>
									</div>								
								</div>
								<div class="different-address">
										<div class="ship-different-title">
											<h3>
												<label>Ship to a different address?</label>
												<input id="ship-box" type="checkbox" />
											</h3>
										</div>
									<div id="ship-box-info" class="row">
										<div class="col-md-12">
											<div class="country-select">
												<label>Country <span class="required">*</span></label>
												<select>
												  <option value="volvo">bangladesh</option>
												  <option value="saab">Algeria</option>
												  <option value="mercedes">Afghanistan</option>
												  <option value="audi">Ghana</option>
												  <option value="audi2">Albania</option>
												  <option value="audi3">Bahrain</option>
												  <option value="audi4">Colombia</option>
												  <option value="audi5">Dominican Republic</option>
												</select> 										
											</div>
										</div>
										<div class="col-md-6">
											<div class="checkout-form-list">
												<label>First Name <span class="required">*</span></label>										
												<input type="text" placeholder="" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="checkout-form-list">
												<label>Last Name <span class="required">*</span></label>										
												<input type="text" placeholder="" />
											</div>
										</div>
										<div class="col-md-12">
											<div class="checkout-form-list">
												<label>Company Name</label>
												<input type="text" placeholder="" />
											</div>
										</div>
										<div class="col-md-12">
											<div class="checkout-form-list">
												<label>Address <span class="required">*</span></label>
												<input type="text" placeholder="Street address" />
											</div>
										</div>
										<div class="col-md-12">
											<div class="checkout-form-list">									
												<input type="text" placeholder="Apartment, suite, unit etc. (optional)" />
											</div>
										</div>
										<div class="col-md-12">
											<div class="checkout-form-list">
												<label>Town / City <span class="required">*</span></label>
												<input type="text" placeholder="Town / City" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="checkout-form-list">
												<label>State / County <span class="required">*</span></label>										
												<input type="text" placeholder="" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="checkout-form-list">
												<label>Postcode / Zip <span class="required">*</span></label>										
												<input type="text" placeholder="Postcode / Zip" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="checkout-form-list">
												<label>Email Address <span class="required">*</span></label>										
												<input type="email" placeholder="" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="checkout-form-list">
												<label>Phone  <span class="required">*</span></label>										
												<input type="text" placeholder="Postcode / Zip" />
											</div>
										</div>								
									</div>
									<div class="order-notes">
										<div class="checkout-form-list">
											<label>Order Notes</label>
											<textarea id="checkout-mess" cols="30" rows="10" placeholder="Notes about your order, e.g. special notes for delivery." ></textarea>
										</div>									
									</div>
								</div>													
							</div>
						</div>	
						<div class="col-lg-6 col-md-6">
							<div class="your-order">
								<h3>Your order</h3>
								<div class="your-order-table table-responsive">
									<table>
										<thead>
											<tr>
												<th class="product-name">Product</th>
												<th class="product-total">Total</th>
											</tr>							
										</thead>
										<tbody>
											<tr class="cart_item">
												<td class="product-name">
													Vestibulum suscipit <strong class="product-quantity"> × 1</strong>
												</td>
												<td class="product-total">
													<span class="amount">£165.00</span>
												</td>
											</tr>
											<tr class="cart_item">
												<td class="product-name">
													Vestibulum dictum magna	<strong class="product-quantity"> × 1</strong>
												</td>
												<td class="product-total">
													<span class="amount">£50.00</span>
												</td>
											</tr>
										</tbody>
										<tfoot>
											<tr class="cart-subtotal">
												<th>Cart Subtotal</th>
												<td><span class="amount">£215.00</span></td>
											</tr>
											<tr class="shipping">
												<th>Shipping</th>
												<td>
													<ul>
														<li>
															<input type="radio" />
															<label>
																Flat Rate: <span class="amount">£7.00</span>
															</label>
														</li>
														<li>
															<input type="radio" />
															<label>Free Shipping:</label>
														</li>
														<li></li>
													</ul>
												</td>
											</tr>
											<tr class="order-total">
												<th>Order Total</th>
												<td><strong><span class="amount">£215.00</span></strong>
												</td>
											</tr>								
										</tfoot>
									</table>
								</div>
								<div class="payment-method">
									<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
									  <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingOne">
										  <h4 class="panel-title">
											<a role="button" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
											  Direct Bank Transfer
											</a>
										  </h4>
										</div>
										<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
										  <div class="panel-body">
											<p>Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won’t be shipped until the funds have cleared in our account.</p>
										  </div>
										</div>
									  </div>
									  <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingTwo">
										  <h4 class="panel-title">
											<a class="collapsed" role="button" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
											  Cheque Payment
											</a>
										  </h4>
										</div>
										<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
										  <div class="panel-body">
											 <p>Please send your cheque to Store Name, Store Street, Store Town, Store State / County, Store Postcode.</p>
										  </div>
										</div>
									  </div>
									  <div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingThree">
										  <h4 class="panel-title">
											<a class="collapsed" role="button" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
											  PayPal
											</a>
										  </h4>
										</div>
										<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
										  <div class="panel-body">
											<p>Pay via PayPal; you can pay with your credit card if you don’t have a PayPal account.</p>
										  </div>
										</div>
									  </div>
									</div>								
									<div class="order-button-payment">
										<input type="submit" value="Place order" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- checkout-area end -->	
		<!-- checkout-end -->
		<!-- brand-area-start -->
		<div class="brand-area">
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
		<footer>
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
												<a title="Contact us" href="#">Contact us</a>
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
		</footer>
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