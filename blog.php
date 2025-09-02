<?php
session_start();
include 'connect.php';


// Pagination settings
$limit = 10; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch products from the database
$sql = "SELECT * FROM blog LIMIT $limit OFFSET $offset";
$allBlogs = $conn->query($sql);

// Count total products for pagination
$countSql = "SELECT COUNT(*) AS total FROM blog";
$countResult = $conn->query($countSql);
$totalProducts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $limit);
?>

<!doctype html>
<html class="no-js" lang="">
 <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Balaji Blog</title>
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
		<link rel="stylesheet" href="blog.css">
		<link rel="stylesheet" href="header.css">
		<link rel="stylesheet" href="blog-detail.css">
		<link rel="stylesheet" href="css/animate.css">

		<!-- responsive css -->
        <link rel="stylesheet" href="css/responsive.css">
		<!-- modernizr js -->
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
		<style>
			.blog-button{
    font-family: Poppins, sans-serif !important;
    font-weight: 400 !important;
    display: inline-block !important;
    position: relative !important;
    z-index: 0 !important;
    padding: 10px 15px !important;
    text-decoration: none;
    background: #845848 ! important;
    color: white !important;
    overflow: hidden !important;
    cursor: pointer !important;
    text-transform: uppercase !important;
    border-radius: 5px !important;
}
		</style>
    </head>
    <body>
		<!-- header-start -->
        <?php include "header.php"; ?>
		<!-- header-end -->
		<!-- blog-area-start -->
		 <section class="AboutSection">
  <div class="image-wrapper">
    <img src="img\balaji\f29223411f5783a2e17276e9da95c140.jpg" class="AboutwrapperImage" />
  </div>
</section>
		<div class="shop-2-area">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-md-12">
							<div class="blog-heading">
							</div>
							</div>
<?php if ($allBlogs && $allBlogs->num_rows > 0): ?>
    <?php while ($row = $allBlogs->fetch_assoc()): ?>
        <div class="col-sm-4">
            <div class="single-blog blog-margin" style="border:1px solid #ddd; padding:10px; margin-bottom:15px;">
                <div class="blog-img">
                    <a href="blog/<?= htmlspecialchars($row['slug']); ?>">
                        <img src="admin/uploads/<?= htmlspecialchars($row['main_images']); ?>" 
                             alt="<?= htmlspecialchars($row['slug']); ?>" 
                             style="height:300px; width:300px;" 
                             onerror="this.src='img/no-image.png';">
                    </a>
                </div>
                <div class="blog-content">
                    <h4 class="blog-title"><?= htmlspecialchars($row['title']); ?></h4>
                    <p><?= strip_tags(substr($row['main_content'], 0, 100)); ?>...</p>
                    <span class="blog-date"><?= $row['created_at']; ?></span>
                    <a class="blog-button" href="blog/<?= htmlspecialchars($row['slug']); ?>">
                        <span>Read More</span>
                    </a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No blog found.</p>
<?php endif; ?>


						</div>
						<div class="blog-pagination">
							<div class="row">
								<div class="col-md-6 col-xs-6">
									<div class="product-count">
									   Showing <?= ($offset + 1) ?> - <?= min($offset + $limit, $totalProducts) ?> of <?= $totalProducts ?> items

									</div>
									<ul class="pagination">
    <!-- Previous button -->
    <li class="pagination-previous-bottom <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a href="?page=<?= max(1, $page - 1) ?>"><i class="fa fa-angle-left"></i></a>
    </li>

    <!-- Page numbers -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="<?= ($page == $i) ? 'active current' : '' ?>">
            <a href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
    <?php endfor; ?>

    <!-- Next button -->
    <li class="pagination-next-bottom <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
        <a href="?page=<?= min($totalPages, $page + 1) ?>"><i class="fa fa-angle-right"></i></a>
    </li>
</ul>

								</div>
							</div>
						</div>
					</div>
						<!-- <div class="left-column-block left-col-mar">
							<h1>Tags</h1>
							<div class="tags">
								<a href="#">new</a>
								<a href="#">fashion</a>
								<a href="#">CATEGORY</a>
								<a href="#">sale</a>
								<a href="#">accessories</a>
								<a href="#">lighting</a>
							</div>
						</div> -->
					</div>
					<div class="col-md-12">
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- blog-area-end -->
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
		<?php include "footer.php"; ?>
		<!-- footer-end -->
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
											<input id="quantity_wanted" class="text" type="text" value="1" >
										
										</p>
										<div class="shop-add-cart">
											<button class="exclusive">
												<span>Add to cart</span>
											</button>
										</div>
										<ul class="usefull-links">
											<li class="sendtofriend">
												<a class="send-friend-button" href="#"> Send to a friend </a>
											</li>
											<li class="print">
												<a class="#" href="#"> Print </a>
											</li>
										</ul>
										<p class="add-wishlist">
											<a class="add-wish" href="#">
												 Add to wishlist 
											</a>
										</p>
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
		<script src="js/wow.min.js"></script>
<script>
    new WOW().init();
</script>
    </body>


</html>