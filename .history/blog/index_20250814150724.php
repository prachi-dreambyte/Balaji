<?php
include '../connect.php';

$recentBlogsQuery = "SELECT id, title, main_images, slug, created_at FROM blog ORDER BY created_at DESC LIMIT 2";

$recentBlogsResult = mysqli_query($conn, $recentBlogsQuery);

$requestUri = $_SERVER['REQUEST_URI']; // Example: /vonia/blog/index.php/pariatur-eligendi-v

$parts = explode('/', $requestUri);

// Get the last non-empty part
$slug = end($parts);

// Optional: remove query strings (if any)
$slug = explode('?', $slug)[0];
$blogsql = "SELECT * FROM blog WHERE slug = ?";
$blogstmt = $conn->prepare($blogsql);
$blogstmt->bind_param("s", $slug);
$blogstmt->execute();
$blogresult = $blogstmt->get_result();
$blogdetails = $blogresult->fetch_assoc();

?>
<?php include '../header.php'; ?>
<html class="no-js" lang="">


<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Blog || Vonia</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <!-- Place favicon.ico in the root directory -->
    <!-- google font -->
    <link href='https://fonts.googleapis.com/css?family=Lato:400,100,300,700,900' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>
    <!-- all css here -->
    <!-- bootstrap v3.3.6 css -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- animate css -->
    <link rel="stylesheet" href="../css/animate.css">
    <!-- jquery-ui.min css -->
    <link rel="stylesheet" href="../css/jquery-ui.min.css">
    <!-- meanmenu css -->
    <link rel="stylesheet" href="../css/meanmenu.min.css">
    <!-- owl.carousel css -->
    <link rel="stylesheet" href="../css/owl.carousel.css">
    <!-- font-awesome css -->
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <!-- nivo-slider css -->
    <link rel="stylesheet" href="../css/nivo-slider.css">
    <!-- style css -->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="../blog-detail.css">
    <!-- responsive css -->
    <link rel="stylesheet" href="../css/responsive.css">
    <!-- modernizr js -->
    <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <!-- <?php include './header2.php'; ?> -->
    <section class="AboutSection">
        <div class="image-wrapper">
            <img src="../img\latest-blog\bg_testimonials.jpg" class="AboutwrapperImage" />
            <h1 class="aboutUs-Heading">BLOG DETAILS</h1>
            <div class="AboutDivWrapper">
                <a class="AboutHome" href="../blog.php">BLOG</a> &nbsp / &nbsp <a class="AboutHome" href="blog-details.php">BLOG DETAILS</a>
            </div>
        </div>
    </section>
    <section class="BlogDetailSection">
        <div class="container">
            <div class="row">
                <div class="left-column-blocks col-md-8 col-lg-8 pt-5">
                    <div class="single-blog blog-margin">
                        <?php
                        $mainImageArr = $blogdetails['main_images'];

                        ?>
                        <img src='../admin/uploads/<?= $mainImageArr ?>' alt="<?= $blogdetails['slug'] ?>" style="width:100%; max-height:400px; object-fit:cover;" />

                    </div>
                    <div>
                        <span class="postboxBlog">
    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.5 14C11.0899 14 14 11.0899 14 7.5C14 3.91015 11.0899 1 7.5 1C3.91015 1 1 3.91015 1 7.5C1 11.0899 3.91015 14 7.5 14Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M7.5 3.59961V7.49961L10.1 8.79961" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
    </svg>
    <?= date('M d, Y h:i A', strtotime($blogdetails['created_at'])); ?>
</span>

                    </div>
                    <h1 class="blogfirstHeading">
                        <?= htmlspecialchars($blogdetails['title']); ?>
                    </h1>

                    <p class="blogUniversalPara"><?= $blogdetails['main_content']; ?></p>

                    <div class="blog-DetailsList">
                        <ol>
                            <li class="blogLi">
                                <h4>
                                    <p?><?= $blogdetails['sub_content']; ?></p>
                                </h4>
                            </li>
                            <!-- <li class="blogLi">
                             <h4>Built to last, even in uttarakhand’s weather</h4>
                             <p class="aboutpUniversalPara">Plastic furniture is often judged for being flimsy or short-lived. Not ours. Spark Line uses high-grade virgin plastic that doesn’t fade, crack, or warp - even under the changing weather conditions of Uttarakhand.
                                Rain, heat, or cold, our chairs stay strong and vibrant.</p>
                        </li>
                         <li class="blogLi">
                             <h4>Stylish yet affordable</h4>
                             <p class="aboutpUniversalPara">It’s obvious that every home and business sets and follows a budget. This is why Spark Line has many design options, including both trendy or timeless seat options and all their chairs are offered at low prices.
                                 You don’t need to sacrifice style for savings when you use our company.</p>
                        </li>
                         <li class="blogLi">
                             <h4>Trusted by thousands client</h4>
                             <p class="aboutpUniversalPara">We are more than just a brand. We participate in local activities, shops, and homes. Spark Line chairs have gently merged into daily life, from intimate family get-togethers to grand events.</p>
                        </li>
                         <li class="blogLi">
                             <h4>Easy maintenance & stackable design</h4>
                             <p class="aboutpUniversalPara">Nobody wants high-maintenance furniture, let's face it. Spark Line plastic chairs save you time and space because they are stackable, water-resistant, and simple to clean.
                             Both residential and business areas benefit from that.</p>
                        </li> -
                    </ol>
                 </div> -->
                            <div class="BlogDetailImage">
                                <?php
                                $subimgs = json_decode($blogdetails['sub_images'] ?? '[]', true);
                                if (is_array($subimgs) && count($subimgs) > 0) {
                                    foreach ($subimgs as $img) {
                                ?>
                                        <img src='../admin/uploads/<?= $img ?>' alt='<?= $blogdetails['slug'] ?>' style='height: 100px; width:100px; margin: 5px;' />
                                <?php
                                    }
                                } else {
                                    echo "<p>No images available</p>";
                                }
                                ?>
                            </div>
                            <div class="blogLi">
                                <h4>
                                    <p?><?= $blogdetails['sub_description']; ?></p>
                                </h4>
                            </div>
                    </div>
                   
                </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="sidebar__wrapper bd-sticky pl-30">

                            <div class="sidebar__widget mb-45">
                             <h3 class="sidebar__widget-title">Recent Post</h3>
                               <div class="sidebar__widget-content">
                                   <div class="sidebar__post">
                                       <?php while ($blogdetails = mysqli_fetch_assoc($recentBlogsResult)) { ?>
                                           <div class="rc__post d-flex align-items-center">
                                               <div class="blogWrapperImage">
                                                   <a href="blog-details.php/<?= $blogdetails['slug']; ?>">
                                                     <img src="../admin/uploads/<?= htmlspecialchars($blogdetails['main_images']); ?>" alt="<?= htmlspecialchars($blogdetails['title']); ?>">

                                                    </a>
                                                </div>
                                                <div class="blogRecentDiv">
                                                    <p class="blogRecent">
                                                        <a href="blog-details.php/<?= $blogdetails['slug']; ?>">
                                                         <?= htmlspecialchars($blogdetails['title']); ?>
                                                     </a>
                                                 </p>
                                                 <div class="rc__meta">
                                                     <span>
                                                         <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                             <path d="M7.5 14C11.0899 14 14 11.0899 14 7.5C14 3.91015 11.0899 1 7.5 1C3.91015 1 1 3.91015 1 7.5C1 11.0899 3.91015 14 7.5 14Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                             <path d="M7.5 3.59961V7.49961L10.1 8.79961" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                         </svg>
                                                     <?= date('M d, Y h:i A', strtotime($blogdetails['created_at'])); ?>
                                                     </span>
                                                 </div>
                                             </div>
                                         </div>
                                     <?php } ?>
                                 </div>
                             </div>
                            </div>

                        </div>

                    </div>
            </div>
    </section>
    <?php include './footer2.php'; ?>
    <!-- modal end -->
    <!-- all js here -->
    <!-- jquery latest version -->
    <script src="../js/vendor/jquery-1.12.4.min.js"></script>
    <!-- bootstrap js -->
    <script src="../js/bootstrap.min.js"></script>
    <!--jquery scrollUp js -->
    <script src="../js/jquery.scrollUp.js"></script>
    <!-- owl.carousel js -->
    <script src="../js/owl.carousel.min.js"></script>
    <!-- meanmenu js -->
    <script src="../js/jquery.meanmenu.js"></script>
    <!-- jquery-ui js -->
    <script src="../js/jquery-ui.min.js"></script>
    <!-- wow js -->
    <script src="../js/wow.min.js"></script>
    <!-- nivo slider js -->
    <script src="../js/jquery.nivo.slider.pack.js"></script>
    <!-- countdown js -->
    <script src="../js/countdown.js"></script>
    <!-- plugins js -->
    <script src="../js/plugins.js"></script>
    <!-- main js -->
    <script src="../js/main.js"></script>
  
</body>

</html>