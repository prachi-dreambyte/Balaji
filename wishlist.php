<?php 
include 'connect.php'; 

$wishlistItems = [
    [
        'image' => 'img/wishlist/1.jpg',
        'name' => 'Vestibulum suscipit',
        'price' => '£165.00',
        'stock' => 'In Stock',
    ],
    [
        'image' => 'img/wishlist/2.jpg',
        'name' => 'Vestibulum dictum magna',
        'price' => '£50.00',
        'stock' => 'In Stock',
    ]
];
$totalItems = count($wishlistItems);
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
		<link rel="stylesheet" href="wishlist1.css">
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
						<div class="entry-header pt-4">
							<h1 class="entry-title">My Wishlist (<?= $totalItems ?>)</h1>

						</div>
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
											<tr>
												
												<td class="product-thumbnail">
													<a href="#">
														<img src="img/wishlist/1.jpg" alt="" />
													</a>
												</td>
												<td class="product-name">
													<a href="#">Vestibulum suscipit</a>
												</td>
												<td class="product-price">
													<span class="amount">£165.00</span>
												</td>
												<td class="product-stock-status">
													<span class="wishlist-in-stock">In Stock</span>
												</td>
												<td class="product-add-to-cart">
													<a href="#"> Add to Cart</a>
												</td>
												<td class="product-remove">
													<a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                                    </svg></a>
												</td>
											</tr>
											<tr>
												
												<td class="product-thumbnail">
													<a href="#">
														<img src="img/wishlist/2.jpg" alt="" />
													</a>
												</td>
												<td class="product-name">
													<a href="#">Vestibulum dictum magna</a>
												</td>
												<td class="product-price">
													<span class="amount">£50.00</span>
												</td>
												<td class="product-stock-status">
													<span class="wishlist-in-stock">In Stock</span>
												</td>
												<td class="product-add-to-cart">
													<a href="#"> Add to Cart</a>
												</td>
												<td class="product-remove">
													<a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                                                </svg></i></a>
												</td>
											</tr>
										</tbody>
									<tbody>
											<tr>
												
												<td class="product-thumbnail">
													<a href="#">
														<img src="img/wishlist/1.jpg" alt="" />
													</a>
												</td>
												<td class="product-name">
													<a href="#">Vestibulum suscipit</a>
												</td>
												<td class="product-price">
													<span class="amount">£165.00</span>
												</td>
												<td class="product-stock-status">
													<span class="wishlist-in-stock">In Stock</span>
												</td>
												<td class="product-add-to-cart">
													<a href="#"> Add to Cart</a>
												</td>
												<td class="product-remove">
													<a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                                    </svg></a>
												</td>
											</tr>
											<tr>
												
												<td class="product-thumbnail">
													<a href="#">
														<img src="img/wishlist/2.jpg" alt="" />
													</a>
												</td>
												<td class="product-name">
													<a href="#">Vestibulum dictum magna</a>
												</td>
												<td class="product-price">
													<span class="amount">£50.00</span>
												</td>
												<td class="product-stock-status">
													<span class="wishlist-in-stock">In Stock</span>
												</td>
												<td class="product-add-to-cart">
													<a href="#"> Add to Cart</a>
												</td>
												<td class="product-remove">
													<a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                                                </svg></i></a>
												</td>
											</tr>
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