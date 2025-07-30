<?php
session_start();
include "./db_connect.php";
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }

//   $sql= "SELECT * FROM `blog`";
//     $result =mysqli_query($conn, $sql);

//     while ($row = mysqli_fetch_assoc( $result)) {
//     echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
//     echo "<p>" . nl2br(htmlspecialchars($row['slug'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['main_content'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['sub_content'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['meta_title'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['meta_description'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['main_images'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['sub_images'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['og_title'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['og_description'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['schema_data'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['keywords'])) . "</p>";
// 	  echo "<p>" . nl2br(htmlspecialchars($row['rating'])) . "</p>";

    
//     if (!empty($row['main_images'])) {
//         echo "<p><strong>Main Image:</strong></p>";
//         echo "<img src='./admin/uploads/{$row['main_images']}' width='300'><br>";
//     }

//     // Sub Images
//     if (!empty($row['sub_images'])) {
//         echo "<p><strong>Sub Images:</strong></p>";
//         $sub_images = json_decode($row['sub_images'], true);

//         foreach ($sub_images as $img) {
//             echo "<img src='./admin/uploads/{$img}' width='150' style='margin: 5px;' />";
//         }
//     }

//     echo "<hr>";
// }

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
     <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>

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
          <textarea name="main_content" class="form-control" rows="2"></textarea>
        </div>
      </div>
      <!-- Sub Content -->
      <div class="col-md-12">
        <div class="mb-3">
          <label for="description" class="form-label">Blog Sub Content</label>
          <textarea class="form-control bg-light-subtle" name="sub_content" id="description" rows="7" placeholder="Short description about the product"></textarea>
        </div>
      </div>

      <!-- Sub Description -->
      <div class="col-md-12">
        <div class="mb-3">
          <label for="description" class="form-label">Blog Sub Description</label>
          <textarea class="form-control bg-light-subtle" name="sub_description" id="sub_description" rows="7" placeholder="Short description about the product"></textarea>
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
     <script>
       ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', '|',
                'bulletedList', 'numberedList', '|',
                'link', 'blockQuote', '|',
                'undo', 'redo'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' }
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
 <script>
    ClassicEditor
        .create(document.querySelector('#sub_description'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', '|',
                'bulletedList', 'numberedList', '|',
                'link', 'blockQuote', '|',
                'undo', 'redo'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' }
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>

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
  sub_description TEXT,
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
 
$sql= "SELECT * FROM `blog`";
    $result =mysqli_query($conn, $sql);

 if ($_SERVER['REQUEST_METHOD'] === 'POST') 
  {
    // Get form data
    $title = $_POST['title'];
   $slug = trim($_POST['slug']);
   $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $slug));
   $slug = trim($slug, '-');

    $main_content = $_POST['main_content'];
    $sub_content = $_POST['sub_content'];
    $sub_description = $_POST['sub_description'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $og_title = $_POST['og_title'];
    $og_description = $_POST['og_description'];
    $schema = $_POST['schema'];
    $keywords = $_POST['keywords'];
    $rating = $_POST['rating'];
    $main_images= $_FILES['main_images'];
	// $sub_images= $_FILES['sub_images'];

    if (isset($_FILES['main_images']) && $_FILES['main_images']['name'] !== '')
		{
           $main_images = time() . '_' . basename($_FILES['main_images']['name']);
           $targetPath = "./uploads/" . $main_images;
           move_uploaded_file($_FILES['main_images']['tmp_name'], $targetPath);
        }
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

   $main_images_json = $main_images;
    $sub_images_json = json_encode($sub_images);
    $stmt = $conn->prepare("INSERT INTO blog 
        (title, slug, main_content, sub_content, sub_description, meta_title, meta_description, og_title, og_description, schema_data, keywords, rating, main_images, sub_images) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?)");
    $stmt->bind_param("ssssssssssssss", $title, $slug, $main_content, $sub_content, $sub_description, $meta_title, $meta_description, $og_title, $og_description, $schema, $keywords, $rating, $main_images_json, $sub_images_json);

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