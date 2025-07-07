<?php
session_start();
include "./db_connect.php";
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }

?>

<!DOCTYPE html>



<html lang="en"> 
<head>
     <!-- Title Meta -->
     <meta charset="utf-8" />
     <title>Create Category | Larkon - Responsive Admin Dashboard Template</title>
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
                         <div class="col-xl-12 col-lg-12 ">
                              <div class="card">
                                   <div class="card-header">
                                        <h4 class="card-title">Add Thumbnail Photo</h4>
                                   </div>
                                   <div class="card-body">
                                        <!-- File Upload -->
                                        <div class="dropzone"  data-plugin="dropzone" data-previews-container="#file-previews" data-upload-preview-template="#uploadPreviewTemplate">
                                             <div class="fallback">
                                                  <input name="category_image" type="file" />
                                             </div>
                                             <!-- <div class="dz-message needsclick">
                                                  <i class="bx bx-cloud-upload fs-48 text-primary"></i>
                                                  <h3 class="mt-4">Drop your images here, or <span class="text-primary">click to browse</span></h3>
                                                  <span class="text-muted fs-13">
                                                       1600 x 1200 (4:3) recommended. PNG, JPG and GIF files are allowed
                                                  </span>
                                             </div> -->
                                        </div>
                                   </div>
                              </div>
                              <div class="card">
                                   
                                   <div class="card-body">
                                        <div class="row">
                                             <div class="col-lg-6">
                                                 
                                                       <div class="mb-3">
                                                            <label for="category-title" class="form-label">Category Title</label>
                                                            <input type="text" id="category-title" name="category" class="form-control" placeholder="Enter Title">
                                                       </div>
                                                  
                                             </div>
                                             <div>
                                                  <button type="submit" name="submit" class="btn btn-primary">submit</button>
                                             </div>

                                            
                                   
                                        </div>
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
          <!-- ==================================================== -->
          <!-- End Page Content -->
          <!-- ==================================================== -->


     </div>
     <!-- END Wrapper -->

     <!-- Vendor Javascript (Require in all Page) -->
     <script src="assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="assets/js/app.js"></script>

</body>


<!-- Mirrored from techzaa.in/larkon/admin/category-add.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 20 Mar 2025 09:19:50 GMT -->
</html>



<?php

$tableQuery = "CREATE TABLE IF NOT EXISTS categories (
     id INT AUTO_INCREMENT PRIMARY KEY,
     category_name VARCHAR(255) NOT NULL UNIQUE,
     category_image TEXT,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 )";
 $conn->query($tableQuery);

 if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $category = trim($_POST['category']);
     $imageName = $_FILES['category_image']['tmp_name'];
     echo $imageName;
 
     // Image upload handling
     if (!empty($_FILES['category_image']['name'])) {
         $targetDir = "uploads/";
         $imageName = basename($_FILES['category_image']['name']);
         $targetFile = $targetDir . $imageName;
         $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
 
         // Validate file type
         $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
         if (!in_array($imageFileType, $allowedTypes)) {
             echo "<script>
                 Swal.fire({
                     icon: 'error',
                     title: 'Invalid File Type!',
                     text: 'Only JPG, PNG, and GIF files are allowed.',
                     confirmButtonText: 'OK'
                 });
             </script>";
             exit();
         }
 
         // Move uploaded file
         if (!move_uploaded_file($_FILES['category_image']['tmp_name'], $targetFile)) {
             echo "<script>
                 Swal.fire({
                     icon: 'error',
                     title: 'Upload Failed!',
                     text: 'There was an error uploading the file.',
                     confirmButtonText: 'OK'
                 });
             </script>";
             exit();
         }
     }
 
     if (!empty($category)) {
         // Insert category with image into database
         $stmt = $conn->prepare("INSERT INTO categories (category_name, category_image) VALUES (?, ?)");
         $stmt->bind_param("ss", $category, $imageName);
 
         if ($stmt->execute()) {
             echo "<script>
                 Swal.fire({
                     icon: 'success',
                     title: 'Category Added!',
                     text: 'Your category has been successfully added.',
                     confirmButtonText: 'OK'
                 }).then(() => {
                     window.location.href = 'category-add.php';
                 });
             </script>";
         } else {
             echo "<script>
                 Swal.fire({
                     icon: 'error',
                     title: 'Error!',
                     text: 'Category could not be added. It might already exist.',
                     confirmButtonText: 'Try Again'
                 });
             </script>";
         }
         $stmt->close();
     } else {
         echo "<script>
             Swal.fire({
                 icon: 'warning',
                 title: 'Empty Field!',
                 text: 'Please enter a category name.',
                 confirmButtonText: 'OK'
             });
         </script>";
     }
 }


?>