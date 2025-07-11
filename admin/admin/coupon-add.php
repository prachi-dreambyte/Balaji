<?php
include 'db_connect.php';
$stmt = $conn->prepare('SELECT * FROM categories');
$stmt->execute();
$result = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="en">


<head>
     <!-- Title Meta -->
     <meta charset="utf-8" />
     <title>Create Product | Larkon - Responsive Admin Dashboard Template</title>
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
          include 'header.php';
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
                                             <h4 class="card-title">Add Coupon Photo</h4>
                                        </div>
                                        <div class="card-body">
                                             <!-- File Upload -->
                                             <div class="dropzone" id="myAwesomeDropzone" data-plugin="dropzone" data-previews-container="#file-previews" data-upload-preview-template="#uploadPreviewTemplate">
                                                  <div class="fallback">
                                                       <input name="file" type="file"  />
                                                  </div>
                                                  <div class="dz-message needsclick">
                                                       <i class="bx bx-cloud-upload fs-48 text-primary"></i>
                                                       <h3 class="mt-4">Drop your images here, or <span class="text-primary">click to browse</span></h3>
                                                       <span class="text-muted fs-13">
                                                            1600 x 1200 (4:3) recommended. PNG, JPG and GIF files are allowed
                                                       </span>
                                                  </div>
                                             </div>

                                        </div>
                                   </div>
                                   <div class="card">
                                        <div class="card-header">
                                             <h4 class="card-title">Coupon Information</h4>
                                        </div>
                                        <div class="card-body">
                                             <div class="row">
                                                  <div class="col-lg-6">

                                                       <div class="mb-3">
                                                            <label for="coupon-name" class="form-label">Coupon Name</label>
                                                            <input type="text" name="coupon-name" id="coupon-name" class="form-control" placeholder="Coupon Code">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-6">

                                                       <div class="mb-3">
                                                            <label for="coupon-discount" class="form-label">Discount</label>
                                                            <input type="number" name="coupon-discount" id="coupon-discount" class="form-control" placeholder="Enter Discount">
                                                       </div>

                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="coupon-code" class="form-label">Coupon Code</label>
                                                            <input type="text" id="coupon-code" name="coupon-code" class="form-control" placeholder="coupon Code">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="expiration_date" class="form-label">Expiration Date</label>
                                                            <input type="date" id="expiration_date" name="expiration_date" class="form-control" placeholder="Expiration Date">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="usage_limit" class="form-label">Usage Limit</label>
                                                            <input type="text" id="usage_limit" name="usage_limit" class="form-control" placeholder="Usage Limit">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="status">Status</label>
                                                            <select class="form-control" name="status" id="status" data-choices data-choices-groups data-placeholder="Select Status">
                                                            <option value="">Choose Status </option>
                                                            <option value="active">Active</option>
                                                            <option value="inactive">Inactive</option>
                                                            </select>
                                                       </div>

                                                  </div>

                                             </div>



                                        </div>
                                   </div>

                                   <div class="p-3 bg-light mb-3 rounded">

                                        <div class="row justify-content-end g-2">
                                             <div class="col-lg-2">
                                                  <button type="submit" name="add" class="btn btn-outline-secondary w-100">Create Product</button>
                                             </div>
                                             <div class="col-lg-2">
                                                  <a href="#!" class="btn btn-primary w-100">Cancel</a>
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
                                   <script>
                                        document.write(new Date().getFullYear())
                                   </script> &copy; Larkon. Crafted by <iconify-icon icon="iconamoon:heart-duotone" class="fs-18 align-middle text-danger"></iconify-icon> <a href="https://1.envato.market/techzaa" class="fw-bold footer-text" target="_blank">Techzaa</a>
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
     <script>
          tinymce.init({
               selector: '#description',
               plugins: 'lists link image code',
               toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
               menubar: false
          });
     </script>

</body>

</html>

<?php
include 'db_connect.php';

echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

// Create products table if not exists
$sql = "CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_name VARCHAR(255) NOT NULL,
    coupon_code VARCHAR(50) NOT NULL UNIQUE,
    discount DECIMAL(5,2) NOT NULL,
    expiration_date DATE NOT NULL,
    usage_limit INT NOT NULL,
    status ENUM('active', 'inactive') NOT NULL,
    image_filename TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

 )";

$conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     // Sanitize and validate input
     $coupon_name = trim($_POST['coupon-name']);
     $coupon_code = trim($_POST['coupon-code']);
     $discount = floatval($_POST['coupon-discount']);
     $expiration_date = $_POST['expiration_date'];
     $usage_limit = intval($_POST['usage_limit']);
     $status = $_POST['status'];
 
     // Handle the image upload
     if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
         $image_tmp_name = $_FILES['file']['tmp_name'];
         $image_name = basename($_FILES['file']['name']);
         $image_size = $_FILES['file']['size'];
         $image_type = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
 
         // Validate image file type
         $allowed_types = ['jpg', 'jpeg', 'png', 'gif','png','webp'];
         if (!in_array($image_type, $allowed_types)) {
             echo "<script>alert('Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF, WEBP.');</script>";
             exit();
         }
 
         // Validate image file size (e.g., max 2MB)
         if ($image_size > 2 * 1024 * 1024) {
             echo "<script>alert('Image size exceeds 2MB limit.');</script>";
             exit();
         }
 
         // Set the target directory for uploads
         $upload_dir = 'uploads/';
         if (!is_dir($upload_dir)) {
             mkdir($upload_dir, 0777, true);
         }
 
         // Generate a unique filename to prevent overwriting
         $new_image_name = uniqid('coupon_', true) . '.' . $image_type;
         $target_file = $upload_dir . $new_image_name;
 
         // Move the uploaded file to the target directory
         if (move_uploaded_file($image_tmp_name, $target_file)) {
             // Image uploaded successfully
         } else {
             echo "<script>alert('Error uploading the image.');</script>";
             exit();
         }
     } else {
         $new_image_name = null; // No image uploaded
     }
 
     // Check for duplicate coupon code
     $stmt = $conn->prepare("SELECT id FROM coupons WHERE coupon_code = ?");
     $stmt->bind_param("s", $coupon_code);
     $stmt->execute();
     $stmt->store_result();
 
     if ($stmt->num_rows > 0) {
         echo "<script>alert('A coupon with this code already exists.');</script>";
     } else {
         // Insert new coupon into the database
         $stmt = $conn->prepare("INSERT INTO coupons (coupon_name, coupon_code, discount, expiration_date, usage_limit, status, image_filename) VALUES (?, ?, ?, ?, ?, ?, ?)");
         $stmt->bind_param("ssdsiss", $coupon_name, $coupon_code, $discount, $expiration_date, $usage_limit, $status, $new_image_name);
 
         if ($stmt->execute()) {
             echo "<script>alert('Coupon added successfully.'); window.location.href = 'coupon-add.php';</script>";
         } else {
             echo "<script>alert('Error adding coupon. Please try again.');</script>";
         }
     }
     $stmt->close();
 }
 $conn->close();
?> 