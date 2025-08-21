<?php
session_start();
include "./db_connect.php";
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 }

?>

<!DOCTYPE html>



<html lang="en"> 
<head>
     <!-- Title Meta -->
     <meta charset="utf-8" />
     <title>Create Blog | Larkon - Responsive Admin Dashboard Template</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta name="description" content="A fully responsive premium admin dashboard template" />
     <meta name="author" content="Techzaa" />
     <meta http-equiv="X-UA-Compatible" content="IE=edge" />

     <!-- App favicon -->
     <link rel="shortcut icon" href="assets/images/favicon.ico">

     <!-- Vendor css (Require in all Page) -->
     <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

     <!-- Icons css (Require in all Page) -->
     <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

     <!-- App css (Require in all Page) -->
     <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

     <!-- Theme Config js (Require in all Page) -->
     <script src="assets/js/config.js"></script>
</head>

<body>

     <!-- START Wrapper -->
     <div class="wrapper">

          <!-- ========== Topbar Start ========== -->
         <?php
         include 'header.php'
         ?>

          <!-- ==================================================== -->
          <!-- Start right Content here -->
          <!-- ==================================================== -->
          <div class="page-content">

               <!-- Start Container Fluid -->
               <div class="container-xxl">

                    <div class="row">
                         <form method="post" enctype="multipart/form-data">
  <div class="card-body">
    <div class="row">
      <!-- Blog Title -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Title</label>
          <input type="text" name="title" class="form-control" placeholder=" blog-title">
        </div>
      </div>

      <!-- Blog Slug -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Page Name</label>
          <input type="text" name="slug" class="form-control" placeholder="page-name">
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-12">
        <div class="mb-3">
          <label>Blog Main Content</label>
          <textarea name="main_content" class="form-control" rows="4"></textarea>
        </div>
      </div>

      <!-- Sub Content -->
      <div class="col-md-12">
        <div class="mb-3">
          <label>Blog Sub Content</label>
          <textarea name="sub_content" class="form-control" rows="2"></textarea>
        </div>
      </div>

      <!-- Meta Title -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Meta Title</label>
          <input type="text" name="meta_title" class="form-control">
        </div>
      </div>

      <!-- Meta Description -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Meta Description</label>
          <input type="text" name="meta_description" class="form-control">
        </div>
      </div>

      <!-- OG Title -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>OG Title</label>
          <input type="text" name="og_title" class="form-control">
        </div>
      </div>

      <!-- OG Description -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>OG Description</label>
          <input type="text" name="og_description" class="form-control">
        </div>
      </div>

      <!-- Schema -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Schema</label>
          <input type="text" name="schema" class="form-control">
        </div>
      </div>

      <!-- Keywords -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Keywords</label>
          <input type="text" name="keywords" class="form-control" placeholder="e.g. blog, seo, tech">
        </div>
      </div>

      <!-- Rating -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Rating</label>
          <input type="number" name="rating" step="0.1" max="5" class="form-control">
        </div>
      </div>

      <!-- Main Image (single) -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Main Image</label>
          <input type="file" name="main_images" class="form-control">
        </div>
      </div>

      <!-- Sub Images (multiple) -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Sub Images</label>
          <input type="file" name="sub_images[]" multiple class="form-control">
        </div>
      </div>

      <div class="col-12">
        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>

                    </div>

               </div>
               <!-- End Container Fluid -->

               <!-- ========== Footer Start ========== -->
               <footer class="footer">
                    <div class="container-fluid">
                         <div class="row">
                              <div class="col-12 text-center">
                                   <script>document.write(new Date().getFullYear())</script> &copy; Larkon. Crafted by <iconify-icon icon="iconamoon:heart-duotone" class="fs-18 align-middle text-danger"></iconify-icon> <a href="https://1.envato.market/techzaa" class="fw-bold footer-text" target="_blank">Techzaa</a>
                              </div>
                         </div>
                    </div>
               </footer>
               <!-- ========== Footer End ========== -->

                                   </div>


     </div>
     <!-- Vendor Javascript (Require in all Page) -->
     <script src="assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="assets/js/app.js"></script>

</body>


<!-- Mirrored from techzaa.in/larkon/admin/blog-add.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 20 Mar 2025 09:19:50 GMT -->
</html>



<?php

$tableQuery = "CREATE TABLE IF NOT EXISTS blog (
    id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  main_content TEXT,
  sub_content TEXT,
  meta_title VARCHAR(255),
  meta_description TEXT,
  og_title VARCHAR(255),
  og_description TEXT,
  schema_data TEXT,
  keywords VARCHAR(255),
  rating DECIMAL(2,1),
  main_images VARCHAR(255),
  sub_images TEXT, 
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 )";
$conn->query($tableQuery);

 if ($_SERVER['REQUEST_METHOD'] === 'POST') 
  {
    // Get form data
    $title = $_POST['title'];
   $slug = trim($_POST['slug']);
   $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $slug));
   $slug = trim($slug, '-');

    $main_content = $_POST['main_content'];
    $sub_content = $_POST['sub_content'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $og_title = $_POST['og_title'];
    $og_description = $_POST['og_description'];
    $schema = $_POST['schema'];
    $keywords = $_POST['keywords'];
    $rating = $_POST['rating'];

    // Image upload
    $main_images = '';
    if (!empty($_FILES['main_images']['name'])) {
       $main_images = time() . '_' . basename($_FILES['main_images']['name']);
        move_uploaded_file($_FILES['main_images']['tmp_name'], "uploads/" . $main_images);
    }

    $sub_images = []; // Use this same variable

if (!empty($_FILES['sub_images']['name'][0])) {
  
    foreach ($_FILES['sub_images']['name'] as $key => $name) {
        if ($name !== '') {
            $newName = time() . '_' . basename($name);
            $target = "./uploads/" . $newName;
            if (move_uploaded_file($_FILES['sub_images']['tmp_name'][$key], $target)) {
                $sub_images[] = $newName; 
                
            }
        }
    }
}

   $main_images_json = json_encode($main_images);
    $sub_images_json = json_encode($sub_images);
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO blog 
        (title, slug, main_content, sub_content, meta_title, meta_description, og_title, og_description, schema_data, keywords, rating, main_images, sub_images) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $title, $slug, $main_content, $sub_content, $meta_title, $meta_description, $og_title, $og_description, $schema, $keywords, $rating, $main_images_json,  $sub_images_json);

    if ($stmt->execute()) { 
      //  .then(() => window.location.href='blog-add.php');
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Blog Created',
                text: 'Your blog was added successfully.',
                confirmButtonText: 'OK'
            }).then(() => window.location.href='blog-add.php');
                
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Insert Failed',
                text: 'Something went wrong.',
                confirmButtonText: 'Try Again'
            });
        </script>";
    }
     } else {
         echo "<script>
             Swal.fire({
                 icon: 'warning',
                 title: 'Empty Field!',
                 text: 'Please enter a blog name.',
                 confirmButtonText: 'OK'
             });
         </script>";
     }
  ?>



<!-- ######################################BLOG################################################ -->
 <?php
session_start();
include 'connect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = $_POST['slug'];
	$main_content =  $_POST['main_content'];
	$sub_content = $_POST['sub_content'];
	$meta_title = $_POST['meta_title'];
	$meta_description = $_POST['meta_description'];
	$og_title = $_POST['og_title'];
	$og_description = $_POST['og_description'];
	$schema_data = $_POST['schema_data'];
	$keywords= $_POST['keywords'];
	$rating= $_POST['rating'];
	$main_images= $_FILES['main_images'];
	$sub_images= $_FILES['sub_images'];

    if (isset($_FILES['main_images']) && $_FILES['main_images']['name'] !== '')
		{
           $main_images = time() . '_' . basename($_FILES['main_images']['name']);
           $targetPath = "./admin/uploads/" . $main_images;
           move_uploaded_file($_FILES['main_images']['tmp_name'], $targetPath);
        }
		if (!empty($_FILES['sub_images']['name'][0])) {
        foreach ($_FILES['sub_images']['name'] as $key => $name) {
            if ($name !== '') {
                $newName = time() . '_' . basename($name);
                $target = "./admin/uploads/" . $newName;
                if (move_uploaded_file($_FILES['sub_images']['tmp_name'][$key], $target)) {
                    $sub_image_names[] = $newName;
                }
            }
        }
    }

	$main_images_json = json_encode($main_images);
    $sub_images_json = json_encode($sub_images);
    $stmt = $conn->prepare("INSERT INTO blog 
        (title, slug, main_content, sub_content, meta_title, meta_description, og_title, og_description, schema_data, keywords, rating, main_images, sub_images) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $title, $slug, $main_content, $sub_content, $meta_title, $meta_description, $og_title, $og_description, $schema, $keywords, $rating, $main_images_json, $sub_images_json);

    $stmt->execute();

    echo "Blog post added!";
}
 $sql= "SELECT * FROM `blog`";
    $result =mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc( $result)) {
    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
    echo "<p>" . nl2br(htmlspecialchars($row['slug'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['main_content'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['sub_content'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['meta_title'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['meta_description'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['main_images'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['sub_images'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['og_title'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['og_description'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['schema_data'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['keywords'])) . "</p>";
	echo "<p>" . nl2br(htmlspecialchars($row['rating'])) . "</p>";

    
    if (!empty($row['main_images'])) {
        echo "<p><strong>Main Image:</strong></p>";
        echo "<img src='./admin/uploads/{$row['main_images']}' width='300'><br>";
    }

    // Sub Images
    if (!empty($row['sub_images'])) {
        echo "<p><strong>Sub Images:</strong></p>";
        $sub_images = json_decode($row['sub_images'], true);

        foreach ($sub_images as $img) {
            echo "<img src='./admin/uploads/{$img}' width='150' style='margin: 5px;' />";
        }
    }

    echo "<hr>";
}
?>

<!doctype html>
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
		<link rel="stylesheet" href="blog-detail.css">
		<!-- responsive css -->
        <link rel="stylesheet" href="css/responsive.css">
		<!-- modernizr js -->
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
		<!-- header-start -->
        <?php include "header.php"; ?>
		<!-- header-end -->
		<!-- blog-area-start -->
		 <section class="AboutSection">
  <div class="image-wrapper">
    <img src="img\balaji\f29223411f5783a2e17276e9da95c140.jpg" class="AboutwrapperImage" />
    <h1 class="aboutUs-Heading">BLOG</h1>
    <div class="AboutDivWrapper">
    <a class="AboutHome" href="index.php">HOME</a> &nbsp /  &nbsp <a class="AboutHome" href="#">BLOG</a>
    </div>
  </div>
</section>

<!-- ***************FORM******************************* -->
<div class="container mt-5 mb-5">
    <h2>Add New Blog</h2>
      <form method="post" enctype="multipart/form-data">
  <div class="card-body">
    <div class="row">
      <!-- Blog Title -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Title</label>
          <input type="text" name="title" class="form-control" placeholder=" blog-title">
        </div>
      </div>

      <!-- Blog Slug -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Page Name</label>
          <input type="text" name="slug" class="form-control" placeholder="page-name">
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-12">
        <div class="mb-3">
          <label>Blog Main Content</label>
          <textarea name="main_content" class="form-control" rows="4"></textarea>
        </div>
      </div>

      <!-- Sub Content -->
      <div class="col-md-12">
        <div class="mb-3">
          <label>Blog Sub Content</label>
          <textarea name="sub_content" class="form-control" rows="2"></textarea>
        </div>
      </div>

      <!-- Meta Title -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Meta Title</label>
          <input type="text" name="meta_title" class="form-control">
        </div>
      </div>

      <!-- Meta Description -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Meta Description</label>
          <input type="text" name="meta_description" class="form-control">
        </div>
      </div>

      <!-- OG Title -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>OG Title</label>
          <input type="text" name="og_title" class="form-control">
        </div>
      </div>

      <!-- OG Description -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>OG Description</label>
          <input type="text" name="og_description" class="form-control">
        </div>
      </div>

      <!-- Schema -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Schema</label>
          <input type="text" name="schema_data" class="form-control">
        </div>
      </div>

      <!-- Keywords -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Keywords</label>
          <input type="text" name="keywords" class="form-control" placeholder="e.g. blog, seo, tech">
        </div>
      </div>

      <!-- Rating -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Rating</label>
          <input type="number" name="rating" step="0.1" max="5" class="form-control">
        </div>
      </div>

      <!-- Main Image (single) -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Main Image</label>
          <input type="file" name="main_images" class="form-control">
        </div>
      </div>

      <!-- Sub Images (multiple) -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Sub Images</label>
          <input type="file" name="sub_images[]" multiple class="form-control">
        </div>
      </div>

      <div class="col-12">
        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>
</div>

<!-- *****************************...******************************************* -->





		<div class="shop-2-area">
			<div class="container">
				<!-- <div class="breadcrumb">
					<a href="index.php" title="Return to Home">
						<i class="icon-home"></i>
					</a>
					<span class="navigation-pipe">></span>
					<span class="navigation-page">
						Blog
					</span>
				</div> -->
				<div class="row">
					<div class="col-md-12">
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-md-12">
							<div class="blog-heading">
							</div>
							</div>
							<div class="col-sm-4">
								<div class="single-blog blog-margin">
									<div class="blog-img">
										<a href="#">
											<img src="img/latest-blog/1.jpg" alt="" />
										</a>
									</div>
									<div class="blog-content">
										<h4 class="blog-title">
											<a href="#">Share the Love 1.6</a>
										</h4>
										<p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
										Lorem Ipsum has been...
										</p>
										<span class="blog-date">2016-03-09 13:40:04</span>
										<a class="blog-read-more" href="blog-details1.php">
											<span>Read More</span>
										</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="single-blog blog-margin">
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
										<a class="blog-read-more" href="blog-details1.php">
											<span>Read More</span>
										</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="single-blog blog-margin">
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
										<a class="blog-read-more" href="blog-details1.php">
											<span>Read More</span>
										</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="single-blog blog-margin">
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
										<a class="blog-read-more" href="blog-details1.php">
											<span>Read More</span>
										</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="single-blog blog-margin">
									<div class="blog-img">
										<a href="#">
											<img src="img/latest-blog/1.jpg" alt="" />
										</a>
									</div>
									<div class="blog-content">
										<h4 class="blog-title">
											<a href="#">Share the Love 1.6</a>
										</h4>
										<p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
										Lorem Ipsum has been...
										</p>
										<span class="blog-date">2016-03-09 13:40:04</span>
										<a class="blog-read-more" href="blog-details1.php">
											<span>Read More</span>
										</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="single-blog blog-margin">
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
										<a class="blog-read-more" href="blog-details1.php">
											<span>Read More</span>
										</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="single-blog blog-margin">
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
										<a class="blog-read-more" href="blog-details1.php">
											<span>Read More</span>
										</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="single-blog blog-margin">
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
										<a class="blog-read-more" href="blog-details1.php">
											<span>Read More</span>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="blog-pagination">
							<div class="row">
								<div class="col-md-6 col-xs-6">
									<div class="product-count">
										Showing 1 - 12 of 13 items
									</div>
									<ul class="pagination">
										<li class="pagination-previous-bottom">
											<a href="#">
												<i class="fa fa-angle-left"></i>
											</a>
										</li>
										<li class="active current">
											<a href="#">
												1
											</a>
										</li>
										<li>
											<a href="#">
												2
											</a>
										</li>
										<li class="pagination-next-bottom">
											<a href="#">
												<i class="fa fa-angle-right"></i>
											</a>
										</li>
									</ul>
								</div>
								<div class="col-md-6 col-xs-6">
									<div class="compare">
										<a href="#"> compare (0) </a>
									</div>
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
    </body>


</html>