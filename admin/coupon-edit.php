<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 }

// Get product ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid product ID.");
}
$product_id = intval($_GET['id']);

// Fetch product details
$sql = "SELECT * FROM coupons WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Coupons not found.");
}

// Close statement
$stmt->close();
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
                         <form action="update_coupon.php" method="post" enctype="multipart/form-data">
                         <input type="hidden" name="id" value="<?= $product['id']; ?>">
                              <div class="col-xl-12 col-lg-12 ">
                                   <div class="card">
                                        <div class="card-header">
                                             <h4 class="card-title">Update Coupon Photo</h4>
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
                                                            <input type="text" name="coupon-name" id="coupon-name" value="<?= $product['coupon_name']; ?>" class="form-control" placeholder="Coupon Code">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-6">

                                                       <div class="mb-3">
                                                            <label for="coupon-discount" class="form-label">Discount</label>
                                                            <input type="number" name="coupon-discount" value="<?= $product['discount']; ?>"  id="coupon-discount" class="form-control" placeholder="Enter Discount">
                                                       </div>

                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="coupon-code" class="form-label">Coupon Code</label>
                                                            <input type="text" id="coupon-code" name="coupon-code" value="<?= $product['coupon_code']; ?>" class="form-control" placeholder="coupon Code">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="expiration_date" class="form-label">Expiration Date</label>
                                                            <input type="date" id="expiration_date" name="expiration_date" class="form-control" value="<?= $product['expiration_date']; ?>"placeholder="Expiration Date">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="usage_limit" class="form-label">Usage Limit</label>
                                                            <input type="text" id="usage_limit" name="usage_limit" value="<?= $product['usage_limit']; ?>" class="form-control" placeholder="Usage Limit">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="status">Status</label>
                                                            <select class="form-control" name="status" id="status" value="<?= $product['status']; ?>" data-choices data-choices-groups data-placeholder="Select Status">
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

