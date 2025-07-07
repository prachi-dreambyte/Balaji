<?php
include 'connect.php'; 
?>


<!doctype html>
<html class="no-js" lang="">
    

<head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Home four || Vonia</title>
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
    <body class="home-4-body">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
		<!-- header-start -->
		<div class="wrapper-box">
			<?php
			include 'header.php';
			?>
						<!-- header-end -->
			<!-- slider-start -->
			<div class="slider-container">
				<div class="slider">
					<!-- Slider Image -->
					<div id="mainslider" class="nivoSlider slider-image">
						<img src="img/slider/5.jpg" alt="main slider" title="#htmlcaption1"/>
						<img src="img/slider/6.jpg" alt="main slider" title="#htmlcaption2"/>
					</div>
					<!-- Slider Caption 1 -->
					<div id="htmlcaption1" class="nivo-html-caption slider-caption-1">
						<div class="slider-progress"></div>	
						<div class="slide1-text slide-1">
							<div class="middle-text">
								<div class="cap-dec wow bounceInLeft" data-wow-duration="0.9s" data-wow-delay="0s">
									<h1>wooflamp</h1>
								</div>	
								<div class="cap-title wow bounceInRight" data-wow-duration="1.2s" data-wow-delay="0.2s">
									<h3>From: $99.00</h3>
								</div>
								<div class="cap-readmore wow bounceInUp" data-wow-duration="1.3s" data-wow-delay=".5s">
									<a href="#">Shop Now</a>
								</div>	
							</div>
						</div>						
					</div>
					<!-- Slider Caption 2 -->
					<div id="htmlcaption2" class="nivo-html-caption slider-caption-2">
						<div class="slider-progress"></div>
						<div class="slide1-text slide-2">
							<div class="middle-text">
								<div class="cap-dec wow bounceIn" data-wow-duration="0.7s" data-wow-delay="0s">
									<h1>Sale!</h1>
								</div>	
								<div class="cap-title wow bounceIn" data-wow-duration="1s" data-wow-delay="0.2s">
									<h3>10% off all products</h3>
								</div>
								<div class="cap-readmore wow bounceIn" data-wow-duration="1.1s" data-wow-delay=".5s">
									<a href="#">Shop Now</a>
								</div>										
							</div>										
						</div>
					</div>
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
											<img src="img/body/4_2.jpg" alt="" />
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
			<!--=====daily-deals-start=====-->
			<div class="home-4-daily-deals-area">
				<div class="container">
					<div class="row ">
						<div class="col-md-12 owl-carousel-space">
							<div class="product-title">
								<h2>
									<span>daily deals</span>
								</h2>
							</div>
							<div class="row">
								<div class="daily-deal">
									<div class="daily-deal-carousel">
										<div class="col-md-12">
											<div class="single-product">
												<div class="daily-products">
													<div class="product-img">
														<a href="#">
															<img src="img/tab-pro/printed-dress.jpg" alt="" />
														</a>
														<span class="new">new</span>
													</div>
													<div class="daily-content">
														<h5 class="product-name">
															<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
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
															<span class="price"> £ 34.78 </span>
															<span class="old-price"> £ 36.61 </span>
														</div>
													</div>
													<div class="upcoming">
														<span class="is-countdown"> </span>
														<div data-countdown="2018/01/01"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="single-product">
												<div class="daily-products">
													<div class="product-img">
														<a href="#">
															<img src="img/tab-pro/printed-summer-dress.jpg" alt="" />
														</a>
														<span class="new">new</span>
														<span class="sale">sale</span>
													</div>
													<div class="daily-content">
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
															<span class="price"> £ 55.07 </span>
															<span class="old-price"> £ 61.19 </span>
														</div>
													</div>
													<div class="upcoming">
														<span class="is-countdown"> </span>
														<div data-countdown="2018/05/01"></div>
													</div>	
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="single-product">
												<div class="daily-products">
													<div class="product-img">
														<a href="#">
															<img src="img/tab-pro/summer-dress.jpg" alt="" />
														</a>
														<span class="new">new</span>
														<span class="sale">sale</span>
													</div>
													<div class="daily-content">
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
															<span class="price"> £ 55.07 </span>
															<span class="old-price"> £ 61.19 </span>
														</div>
													</div>
													<div class="upcoming">
														<span class="is-countdown"> </span>
														<div data-countdown="2017/02/01"></div>
													</div>	
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="single-product">
												<div class="daily-products">
													<div class="product-img">
														<a href="#">
															<img src="img/tab-pro/lamp.jpg" alt="" />
														</a>
														<span class="new">new</span>
													</div>
													<div class="daily-content">
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
															<span class="price"> £ 38.12 </span>
															<span class="old-price"> £ 42.30 </span>
														</div>
													</div>
													<div class="upcoming">
														<span class="is-countdown"> </span>
														<div data-countdown="2018/11/01"></div>
													</div>	
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--=====daily-deals-end=====-->
			<!--=====product-tab-start=====-->
			<div class="home-4-product-tab">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="feature-tab-area">
								<!-- Nav tabs -->
								<ul class="tabs" role="tablist">
									<li><a class="active" href="#newarrival" aria-controls="newarrival" role="tab" data-bs-toggle="tab">new arrival</a></li>
									<li><a href="#onsale" aria-controls="onsale" role="tab" data-bs-toggle="tab">onsale</a></li>
									<li><a href="#bestseller" aria-controls="bestseller" role="tab" data-bs-toggle="tab">bestseller</a></li>
								</ul>
								<div class="tab-content owl-carousel-space">
									<div role="tabpanel" class="tab-pane active fade show" id="newarrival">
										<div class="row">
											<div class="product-carousel">
												<!-- single-product start -->
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/printed-chiffon-dress.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<span class="sale">sale!</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
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
																<a href="#" title="Printed Chiffon Dress">Printed Chiffon Dress</a>
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
																<span class="price"> £ 19.68 </span>
																<span class="old-price"> £ 24.60 </span>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/lamp.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
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
																<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
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
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/printed-summer-dress.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<span class="sale">sale!</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
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
																<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
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
																<span class="old-price"> £ 36.61 </span>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/printed-dress.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
																				<i class="fa fa-bar-chart" aria-hidden="true"></i>
																			</a>
																		</li>
																	</ul>
																	<div class="quick-view">
																		<a href="#" title="Quick view" data-bs-toggle="modal" data-target="#myModal">
																			<span>Quick view</span>
																		</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title="Printed Dress">Printed Dress</a>
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
																<span class="old-price"> £ 61.19 </span>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/cup.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<span class="sale">sale</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
																				<i class="fa fa-bar-chart" aria-hidden="true"></i>
																			</a>
																		</li>
																	</ul>
																	<div class="quick-view">
																		<a href="#" title="Quick view" data-bs-toggle="modal" data-target="#myModal">
																			<span>Quick view</span>
																		</a>
																	</div>
																</div>
															</div>
														</div>
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
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane fade" id="onsale">
										<div class="row">
											<div class="product-carousel">
												<!-- single-product start -->
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/printed-summer-dress.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<span class="sale">sale!</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
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
																<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
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
																<span class="old-price"> £ 36.61  </span>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/printed-dress.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
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
																<a href="#" title="Printed Dress">Printed Dress</a>
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
																<span class="old-price"> £ 61.19 </span>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/cup.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<span class="sale">sale!</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
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
																<a href="#" title="Printed Chiffon Dress">Printed Chiffon Dress</a>
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
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/cooks.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<span class="sale">sale</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
																				<i class="fa fa-bar-chart" aria-hidden="true"></i>
																			</a>
																		</li>
																	</ul>
																	<div class="quick-view">
																		<a href="#" title="Quick view" data-bs-toggle="modal" data-target="#myModal">
																			<span>Quick view</span>
																		</a>
																	</div>
																</div>
															</div>
														</div>
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
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/been.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
																				<i class="fa fa-bar-chart" aria-hidden="true"></i>
																			</a>
																		</li>
																	</ul>
																	<div class="quick-view">
																		<a href="#" title="Quick view" data-bs-toggle="modal" data-target="#myModal">
																			<span>Quick view</span>
																		</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
															</h5>
															<div class="reviews">
																<div class="star-content clearfix">
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star"></span>
																</div>
															</div>
															<div class="price-box">
																<span class="price"> £ 55.07 </span>
																<span class="old-price"> £ 61.19 </span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane fade" id="bestseller">
										<div class="row">
											<div class="product-carousel">
												<!-- single-product start -->
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/faded-short-sleeves-tshirt.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
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
																<a href="#" title="Blouse">Blouse</a>
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
																<span class="price"> £ 30.78 </span>
																<span class="old-price"> £ 32.40 </span>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/vass.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<span class="sale">sale</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
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
																<a href="#" title="Printed Chiffon Dress">Printed Summer Dress</a>
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
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/cooks.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<span class="sale">sale!</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
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
																<a href="#" title="Printed Chiffon Dress">Printed Summer Dress</a>
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
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/blouse.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
																				<i class="fa fa-bar-chart" aria-hidden="true"></i>
																			</a>
																		</li>
																	</ul>
																	<div class="quick-view">
																		<a href="#" title="Quick view" data-bs-toggle="modal" data-target="#myModal">
																			<span>Quick view</span>
																		</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title="Blouse">Blouse</a>
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
																<span class="price"> £ 34.78 </span>
																<span class="old-price"> £ 36.61 </span>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="single-product">
														<div class="product-img">
															<a href="#">
																<img src="img/tab-pro/lamp.jpg" alt="" />
															</a>
															<span class="new">new</span>
															<span class="sale">sale</span>
															<div class="product-action">
																<div class="add-to-links">
																	<ul>
																		<li>
																			<a href="#" title="Add to cart">
																				<i class="fa fa-shopping-cart"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to wishlist">
																				<i class="fa fa-heart" aria-hidden="true"></i>
																			</a>
																		</li>
																		<li>
																			<a href="#" title="Add to compare">
																				<i class="fa fa-bar-chart" aria-hidden="true"></i>
																			</a>
																		</li>
																	</ul>
																	<div class="quick-view">
																		<a href="#" title="Quick view" data-bs-toggle="modal" data-target="#myModal">
																			<span>Quick view</span>
																		</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="product-content">
															<h5 class="product-name">
																<a href="#" title="Printed Chiffon Dress">Printed Dress</a>
															</h5>
															<div class="reviews">
																<div class="star-content clearfix">
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star star-on"></span>
																	<span class="star"></span>
																</div>
															</div>
															<div class="price-box">
																<span class="price"> £ 34.78 </span>
																<span class="old-price"> £ 36.61 </span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- product-tab-end -->
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
			<!--===== feature-product-start =====-->
			<div class="home-4-feature-product">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="product-title">
								<h2>
									<span>Featured Products</span>
								</h2>
							</div>
							<div class="row">
								<div class="feature-product-tab">
									<div class="feature-product-carousel">
										<div class="col-md-12">
											<div class="item-product item-pro-mar">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/faded-short-sleeves-tshirt.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
													<h5 class="product-name">
														<a href="#" title="Faded Short Sleeves T-shirt">Faded Short Sleeves T-shirt</a>
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
														<span class="price"> £ 16.84 </span>
														<span class="old-price"> £ 19.81 </span>
													</div>
												</div>
											</div>
											<div class="item-product item-pro-mar">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/vass.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
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
														<span class="old-price"> £ 32.40 </span>
													</div>
												</div>
											</div>
											<div class="item-product">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/cooks.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
													<h5 class="product-name">
														<a href="#" title="Printed Dress">Printed Dress</a>
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
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="item-product item-pro-mar">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/chair.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
													<h5 class="product-name">
														<a href="#" title="Printed Dress">Printed Dress</a>
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
												</div>
											</div>
											<div class="item-product item-pro-mar">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/summer-dress.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
													<h5 class="product-name">
														<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
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
														<span class="old-price"> £ 36.61 </span>
													</div>
												</div>
											</div>
											<div class="item-product">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/lamp2.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
													<h5 class="product-name">
														<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
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
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="item-product item-pro-mar">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/been.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
													<h5 class="product-name">
														<a href="#" title="Printed Chiffon Dress">Printed Chiffon Dress</a>
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
												</div>
											</div>
											<div class="item-product item-pro-mar">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/blouse.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
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
														<span class="price"> £ 32.40 </span>
													</div>
												</div>
											</div>
											<div class="item-product">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/cup.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
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
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="item-product item-pro-mar">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/printed-dress.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
													<h5 class="product-name">
														<a href="#" title="Printed Dress">Printed Dress</a>
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
														<span class="old-price"> £ 61.19 </span>
													</div>
												</div>
											</div>
											<div class="item-product item-pro-mar">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/printed-summer-dress.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
													<h5 class="product-name">
														<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
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
														<span class="old-price"> £ 36.61 </span>
													</div>
												</div>
											</div>
											<div class="item-product">
												<div class="products-inner">
													<a href="#" title="Faded Short Sleeves T-shirt">
														<img src="img/tab-pro/lamp.jpg" alt="" />
													</a>
												</div>
												<div class="product-contents">
													<h5 class="product-name">
														<a href="#" title="Printed Summer Dress">Printed Summer Dress</a>
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
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--===== feature-product-end =====-->
			<!--===== banner-2-start =====-->
			<div class="home-4-banner-2">
				<div class="container">
					<div class="banner-box">
						<a href="#">
							<img src="img/banner/9_1.jpg" alt="" />
						</a>
					</div>
				</div>
			</div>
			<!--===== banner-2-end =====-->
			<!--===== latest-blog-start =====-->
			<div class="home-4-latest-blog">
				<div class="container">
					<div class="blog">
						<div class="product-title">
							<h2>
								<span>latest blog</span>
							</h2>
						</div>
						<div class="owl-carousel-space">
							<div class="row ">
								<div class="blogs-carousel">
									<div class="col-md-12">
										<div class="single-blog">
											<div class="blog-img">
												<a href="#">
													<img src="img/latest-blog/1.jpg" alt="" />
												</a>
											</div>
											<div class="blog-content">
												<h4 class="blog-title">
													<a href="#">Share the Love for 1.6</a>
												</h4>
												<p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
												Lorem Ipsum has been...
												</p>
												<span class="blog-date">2016-03-09 13:40:04</span>
												<a class="blog-read-more" href="#">
													<span>Read More</span>
												</a>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="single-blog">
											<div class="blog-img">
												<a href="#">
													<img src="img/latest-blog/3.jpg" alt="" />
												</a>
											</div>
											<div class="blog-content">
												<h4 class="blog-title">
													<a href="#">Answers to your Questions about...</a>
												</h4>
												<p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
												Lorem Ipsum has been...
												</p>
												<span class="blog-date">2016-03-09 13:40:04</span>
												<a class="blog-read-more" href="#">
													<span>Read More</span>
												</a>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="single-blog">
											<div class="blog-img">
												<a href="#">
													<img src="img/latest-blog/2.jpg" alt="" />
												</a>
											</div>
											<div class="blog-content">
												<h4 class="blog-title">
													<a href="#">What is Bootstrap? – The History...</a>
												</h4>
												<p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
												Lorem Ipsum has been...
												</p>
												<span class="blog-date">2016-03-09 13:40:04</span>
												<a class="blog-read-more" href="#">
													<span>Read More</span>
												</a>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="single-blog">
											<div class="blog-img">
												<a href="#">
													<img src="img/latest-blog/1.jpg" alt="" />
												</a>
											</div>
											<div class="blog-content">
												<h4 class="blog-title">
													<a href="#">From Now we are certified web...</a>
												</h4>
												<p> Smartdatasoft is an offshore web development company located in Bangladesh. 
												We are serving this...
												</p>
												<span class="blog-date">2016-03-09 13:40:04</span>
												<a class="blog-read-more" href="#">
													<span>Read More</span>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--===== latest-blog-end =====-->
			<!--===== testimonial-area-start =====-->
			<div class="testimonial-area">
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
			</div>
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
								<div class="col-md-6 col-sm-6 col-xs-12 address">
									Copyright  © 
									<a href="http://bootexperts.com/">Bootexperts</a>
									. All Rights Reserved
								</div>
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
											<input id="quantity_wanted" class="text" type="text" value="1" name="qty" style="border: 1px solid rgb(189, 194, 201);">
										
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
    </body>


</html>
