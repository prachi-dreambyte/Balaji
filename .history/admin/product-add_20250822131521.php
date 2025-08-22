<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
}
$stmt = $conn->prepare('SELECT * FROM categories');
$stmt->execute();
$result = $stmt->get_result();

try {
     $product_stmt = $conn->prepare('SELECT id, product_name FROM products');
     $product_stmt->execute();
     $product_result = $product_stmt->get_result();
} catch (Exception $e) {
     error_log("product not found" . $e->getMessage());
}

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
          <?php include 'header.php'; ?>

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
                                             <h4 class="card-title">Add Product Photo</h4>
                                        </div>
                                        <div class="card-body">
                                             <!-- File Upload -->
                                             <div class="dropzone" id="myAwesomeDropzone" data-plugin="dropzone" data-previews-container="#file-previews" data-upload-preview-template="#uploadPreviewTemplate">
                                                  <div class="fallback">
                                                       <input name="file[]" type="file" multiple />
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
                                             <h4 class="card-title">Product Information</h4>
                                        </div>
                                        <div class="card-body">
                                             <div class="row">
                                                  <div class="col-lg-6">
                                                       <div class="mb-3">
                                                            <label for="product-name" class="form-label">Product Name</label>
                                                            <input type="text" name="name" id="product-name" class="form-control" placeholder="Items Name">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-6">
                                                       <label for="product-categories" class="form-label">Product Categories</label>
                                                       <select class="form-control" name="category" id="product-categories" data-choices data-choices-groups data-placeholder="Select Categories">
                                                            <option value="">Choose a category</option>
                                                            <?php while ($row = $result->fetch_assoc()) { ?>
                                                                      <option value="<?php echo htmlspecialchars($row['category_name']); ?>">
                                                                           <?php echo htmlspecialchars($row['category_name']); ?>
                                                                      </option>
                                                            <?php } ?>
                                                       </select>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="product-brand" class="form-label">Brand</label>
                                                            <input type="text" id="product-brand" name="brand" class="form-control" placeholder="Brand Name">
                                                       </div>
                                                  </div>
                                                  <!-- <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="product-weight" class="form-label">Weight</label>
                                                            <input type="text" id="product-weight" name="weight" class="form-control" placeholder="In gm & kg">
                                                       </div>
                                                  </div> -->
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="product-weight" class="form-label">Product Weight</label>
                                                            <input type="text" id="product-weight" name="product_weight" class="form-control" placeholder="Product weight">
                                                       </div>
                                                  </div>
                                                    <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="product_variants" class="form-label">Variants</label>
                                                            <select class="form-control"
                                                                 name="variants"
                                                                 id="product_variants"
                                                                 data-choices
                                                                 data-choices-groups
                                                                 data-placeholder="Select variants">
                                                                 <option value="">Choose a variant</option>
                                                                 <?php if ($product_result && $product_result->num_rows > 0) {
                                                                      while ($row = $product_result->fetch_assoc()) { ?>
                                                                                     <option value="<?php echo htmlspecialchars($row['id']); ?>">
                                                                                          <?php echo htmlspecialchars($row['product_name']); ?>
                                                                                     </option>
                                                                           <?php }
                                                                 } else { ?>
                                                                           <option value="">No variants found</option>
                                                                 <?php } ?>
                                                            </select>
                                                       </div>
                                                      
                                                  </div>
                                                   <div class="col-lg-4">

                                                            <div class="mb-3">
                                                                 <label for="product-colour" class="form-label">Product Colour</label>
                                                                 <input type="text" id="product-colour" name="colour" class="form-control" placeholder="Product Colour">
                                                            </div>
                                                              <div class="mb-3">
                                                                 <label for="hashtags" class="form-label">Hashtags</label>
                                                                 <input type="text" id="hashtags" name="hashtags" class="form-control" placeholder="hashtags">
                                                            </div>
                                                       </div>

                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="total-height" class="form-label">Total Height</label>
                                                            <input type="text" id="total-height" name="total_height" class="form-control" placeholder="Total height">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="total-width" class="form-label">Total Width</label>
                                                            <input type="text" id="total-width" name="total_width" class="form-control" placeholder="Total width">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="material" class="form-label">Material</label>
                                                            <input type="text" id="material" name="material" class="form-control" placeholder="Material">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="seat-height" class="form-label">Seat Height</label>
                                                            <input type="text" id="seat-height" name="seat_height" class="form-control" placeholder="Seat height">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="seat-thickness" class="form-label">Seat Thickness</label>
                                                            <input type="text" id="seat-thickness" name="seat_thickness" class="form-control" placeholder="Seat thickness">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="seat-depth" class="form-label">Seat Depth</label>
                                                            <input type="text" id="seat-depth" name="seat_depth" class="form-control" placeholder="Seat depth">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="seat-material-type" class="form-label">Seat Material Type</label>
                                                            <input type="text" id="seat-material-type" name="seat_material_type" class="form-control" placeholder="Seat material type">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="backrest-height" class="form-label">Backrest Height From Seat</label>
                                                            <input type="text" id="backrest-height" name="backrest_height_from_seat" class="form-control" placeholder="Backrest height from seat">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="backrest-material-type" class="form-label">Backrest Material Type</label>
                                                            <input type="text" id="backrest-material-type" name="backrest_material_type" class="form-control" placeholder="Backrest material type">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="pedestal-base" class="form-label">Pedestal Base</label>
                                                            <input type="text" id="pedestal-base" name="pedestal_base" class="form-control" placeholder="Pedestal base">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="seat-adjusting-range" class="form-label">Seat Height Adjusting Range</label>
                                                            <input type="text" id="seat-adjusting-range" name="seat_height_adjusting_range" class="form-control" placeholder="Seat height adjusting range">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="handle-type" class="form-label">Handle Type</label>
                                                            <input type="text" id="handle-type" name="handle_type" class="form-control" placeholder="Handle type">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="wheel-type" class="form-label">Wheel Type</label>
                                                            <input type="text" id="wheel-type" name="wheel_type" class="form-control" placeholder="Wheel type">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="mechanical-system" class="form-label">Mechanical System Type</label>
                                                            <input type="text" id="mechanical-system" name="mechanical_system_type" class="form-control" placeholder="Mechanical system type">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="color-available" class="form-label">Color Available</label>
                                                            <input type="text" id="color-available" name="color_available" class="form-control" placeholder="Color available">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="backrest-size" class="form-label">Backrest Size</label>
                                                            <input type="text" id="backrest-size" name="backrest_size" class="form-control" placeholder="Backrest size">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="adjuster-size" class="form-label">Adjuster Size</label>
                                                            <input type="text" id="adjuster-size" name="adjuster_size" class="form-control" placeholder="Adjuster size">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="guarantee" class="form-label">Guarantee</label>
                                                            <input type="text" id="guarantee" name="guarantee" class="form-control" placeholder="Guarantee">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="chair-arms" class="form-label">Chair Arms</label>
                                                            <input type="text" id="chair-arms" name="chair_arms" class="form-control" placeholder="Chair arms">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="table-top-size" class="form-label">Table Top Size</label>
                                                            <input type="text" id="table-top-size" name="table_top_size" class="form-control" placeholder="Table top size">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="sitting-capacity" class="form-label">Sitting Capacity</label>
                                                            <input type="number" id="sitting-capacity" name="sitting_capacity" class="form-control" placeholder="Sitting capacity">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="no-of-top" class="form-label">No. of Top</label>
                                                            <input type="number" id="no-of-top" name="no_of_top" class="form-control" placeholder="No. of top">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="table-type" class="form-label">Table Type</label>
                                                            <input type="text" id="table-type" name="table_type" class="form-control" placeholder="Table type">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="shape" class="form-label">Shape</label>
                                                            <input type="text" id="shape" name="shape" class="form-control" placeholder="Shape">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="wheels" class="form-label">Wheels</label>
                                                            <input type="text" id="wheels" name="wheels" class="form-control" placeholder="Wheels">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="size" class="form-label">Size</label>
                                                            <input type="text" id="size" name="size" class="form-control" placeholder="Size">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row mb-4">
                                                  <div class="col-lg-4">
                                                       <div class="mt-3">
                                                            <h5 class="text-dark fw-medium">Size :</h5>
                                                            <div class="d-flex flex-wrap gap-2" role="group" aria-label="Basic checkbox toggle button group">
                                                                 <input type="checkbox" class="btn-check" id="size-xs1">
                                                                 <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-xs1">XS</label>

                                                                 <input type="checkbox" class="btn-check" id="size-s1">
                                                                 <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-s1">S</label>

                                                                 <input type="checkbox" class="btn-check" id="size-m1">
                                                                 <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-m1">M</label>

                                                                 <input type="checkbox" class="btn-check" id="size-xl1">
                                                                 <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-xl1">Xl</label>

                                                                 <input type="checkbox" class="btn-check" id="size-xxl1">
                                                                 <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-xxl1">XXL</label>
                                                                 <input type="checkbox" class="btn-check" id="size-3xl1">
                                                                 <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-3xl1">3XL</label>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-12">
                                                       <div class="mb-3">
                                                            <label for="short_description" class="form-label">Short Description(30-40Words)</label>
                                                            <textarea class="form-control bg-light-subtle" name="short_description" id="short_description" rows="7" placeholder="Short description about the product"></textarea>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-12">
                                                       <div class="mb-3">
                                                            <label for="description" class="form-label">More Info.</label>
                                                            <textarea class="form-control bg-light-subtle" name="description" id="description" rows="7" placeholder="More Info. about the product"></textarea>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="product-id" class="form-label">Tag Number</label>
                                                            <input type="number" name="tagnumber" id="product-id" class="form-control" placeholder="#******">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <div class="mb-3">
                                                            <label for="product-stock" class="form-label">Stock</label>
                                                            <input type="number" name="stock" id="product-stock" class="form-control" placeholder="Quantity">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <label for="product-stock" class="form-label">Tag</label>
                                                       <select class="form-control" name="tag[]" id="choices-multiple-remove-button" data-choices data-choices-removeItem name="choices-multiple-remove-button" multiple>
                                                            <option value="FEATURED PRODUCTS" selected>FEATURED PRODUCTS</option>
                                                            <option value="NEW ARRIVAL">NEW ARRIVAL</option>
                                                            <option value="ONSALE">ONSALE</option>
                                                            <option value="BESTSELLER">BESTSELLER</option>
                                                       </select>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="card">
                                        <div class="card-header">
                                             <h4 class="card-title">Pricing Details</h4>
                                        </div>
                                        <div class="card-body">
                                             <div class="row">
                                                  <div class="col-lg-4">
                                                       <label for="product-price" class="form-label">Price</label>
                                                       <div class="input-group mb-3">
                                                            <span class="input-group-text fs-20"><i class='bx bx-dollar'></i></span>
                                                            <input type="number" id="product-price" name="price" class="form-control" placeholder="000">
                                                       </div>
                                                  </div>
                                                  <div class="col-lg-4">
                                                       <label for="product-discount" class="form-label">Discount</label>
                                                       <div class="input-group mb-3">
                                                            <span class="input-group-text fs-20"><i class='bx bxs-discount'></i></span>
                                                            <input type="number" id="product-discount" class="form-control" name="discount" placeholder="000">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-4">

                                                       <label for="product-discount" class="form-label">Corporate Discount	</label>
                                                       <div class="input-group mb-3">
                                                            <span class="input-group-text fs-20"><i class='bx bxs-discount'></i></span>
                                                            <input type="number" id="product-discount" class="form-control" name="corporate_discount" placeholder="000">
                                                       </div>

                                                  </div>
                                                  <div class="col-lg-4">
                                                       <label for="product-tex" class="form-label">Tax</label>
                                                       <div class="input-group mb-3">
                                                            <span class="input-group-text fs-20"><i class='bx bxs-file-txt'></i></span>
                                                            <input type="number" name="tex" id="product-tex" class="form-control" placeholder="000">
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

// Create products table if not exists with all columns
$sql = "CREATE TABLE IF NOT EXISTS products (
     id INT AUTO_INCREMENT PRIMARY KEY,
     product_name VARCHAR(255),
     category VARCHAR(255),
     brand VARCHAR(255),
     weight VARCHAR(50),
     size VARCHAR(255),
     total_height VARCHAR(50),
     total_width VARCHAR(50),
     material VARCHAR(100),
     seat_height VARCHAR(50),
     seat_thickness VARCHAR(50),
     seat_depth VARCHAR(50),
     seat_material_type VARCHAR(100),
     backrest_height_from_seat VARCHAR(50),
     backrest_material_type VARCHAR(100),
     pedestal_base VARCHAR(100),
     seat_height_adjusting_range VARCHAR(50),
     handle_type VARCHAR(100),
     wheel_type VARCHAR(100),
     mechanical_system_type VARCHAR(100),
     color_available VARCHAR(255),
     product_weight VARCHAR(50),
     backrest_size VARCHAR(50),
     adjuster_size VARCHAR(50),
     guarantee VARCHAR(50),
     chair_arms VARCHAR(50),
     table_top_size VARCHAR(50),
     sitting_capacity INT(11),
     no_of_top INT(11),
     table_type VARCHAR(100),
     shape VARCHAR(50),
     wheels VARCHAR(50),
     short_description VARCHAR(200),
     description TEXT,
     tag_number VARCHAR(50) UNIQUE,
     stock INT(11),
     tags TEXT,
     price DECIMAL(10,2),
     discount DECIMAL(10,2),
     corporate_discount DECIMAL(10,2),	
     tax DECIMAL(10,2),
     images TEXT,  
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 )";

if ($conn->query($sql) === FALSE) {
     echo "<script>
          Swal.fire({
               icon: 'error',
               title: 'Database Error!',
               text: 'Error creating products table: " . $conn->error . "',
               confirmButtonText: 'OK'
          });
     </script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     echo "dsfdfggdf";
     // Collect form data for all fields
     $product_name = $_POST['name'];
     $category = $_POST['category'];
     $brand = $_POST['brand'];
     // $weight = $_POST['weight'];
     $size = $_POST['size'];
     $total_height = $_POST['total_height'];
     $total_width = $_POST['total_width'];
     $material = $_POST['material'];
     $seat_height = $_POST['seat_height'];
     $seat_thickness = $_POST['seat_thickness'];
     $seat_depth = $_POST['seat_depth'];
     $seat_material_type = $_POST['seat_material_type'];
     $backrest_height_from_seat = $_POST['backrest_height_from_seat'];
     $backrest_material_type = $_POST['backrest_material_type'];
     $pedestal_base = $_POST['pedestal_base'];
     $seat_height_adjusting_range = $_POST['seat_height_adjusting_range'];
     $handle_type = $_POST['handle_type'];
     $wheel_type = $_POST['wheel_type'];
     $mechanical_system_type = $_POST['mechanical_system_type'];
     $color_available = $_POST['color_available'];
     $product_weight = $_POST['product_weight'];
     $backrest_size = $_POST['backrest_size'];
     $adjuster_size = $_POST['adjuster_size'];
     $guarantee = $_POST['guarantee'];
     $chair_arms = $_POST['chair_arms'];
     $table_top_size = $_POST['table_top_size'];
     $sitting_capacity = $_POST['sitting_capacity'];
     $no_of_top = $_POST['no_of_top'];
     $table_type = $_POST['table_type'];
     $shape = $_POST['shape'];
     $wheels = $_POST['wheels'];
     $short_description = $_POST['short_description'];
     $description = $_POST['description'];
     $tag_number = $_POST['tagnumber'];
     $stock = $_POST['stock'];
     $tags = isset($_POST['tag']) ? (is_array($_POST['tag']) ? implode(',', $_POST['tag']) : $_POST['tag']) : '';
     $price = $_POST['price'];
     $discount = $_POST['discount'];
     $corporate_discount = $_POST['corporate_discount'];
     $tax = $_POST['tex'];
     $variants = !empty($_POST['variants']) ? $_POST['variants'] : null; // ✅ If empty, will handle later
     $colour = $_POST['colour'];

     // Image upload handling
     $imageArray = [];
     $uploadDir = "uploads/"; // Folder where images will be stored

     if (!empty($_FILES['file']['name'][0])) {
          foreach ($_FILES['file']['name'] as $key => $imageName) {
               $imageTmp = $_FILES['file']['tmp_name'][$key];
               $imagePath = $uploadDir . time() . "_" . basename($imageName);

               if (move_uploaded_file($imageTmp, $imagePath)) {
                    $imageArray[] = $imagePath; // Store image path in array
               }
          }
     }

     $imagesJSON = json_encode($imageArray); // Convert array to JSON for database storage

     // Check if product already exists
     $stmt = $conn->prepare("SELECT id FROM products WHERE tag_number = ?");
     $stmt->bind_param("s", $tag_number);
     $stmt->execute();
     $stmt->store_result();

     if ($stmt->num_rows > 0) {
          echo "<script>
               Swal.fire({
                    icon: 'warning',
                    title: 'Duplicate Entry!',
                    text: 'A product with this Tag Number already exists!',
                    confirmButtonText: 'OK'
               });
          </script>";
     } else {
          // Insert data into database with all columns
          $sql = "INSERT INTO products 
                (product_name, category, brand, size, total_height, total_width, material, 
                seat_height, seat_thickness, seat_depth, seat_material_type, backrest_height_from_seat, 
                backrest_material_type, pedestal_base, seat_height_adjusting_range, handle_type, 
                wheel_type, mechanical_system_type, color_available, product_weight, backrest_size, 
                adjuster_size, guarantee, chair_arms, table_top_size, sitting_capacity, no_of_top, 
                table_type, shape, wheels, short_description, description, tag_number, stock, tags, 
                price, discount, corporate_discount,tax, images, variants, colour) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?,?,?, ?, ?)";

          $stmt = $conn->prepare($sql);
          $stmt->bind_param(
               "ssssssssssssssssssssssssssssssssssssssssss",
               $product_name,
               $category,
               $brand,
               $size,
               $total_height,
               $total_width,
               $material,
               $seat_height,
               $seat_thickness,
               $seat_depth,
               $seat_material_type,
               $backrest_height_from_seat,
               $backrest_material_type,
               $pedestal_base,
               $seat_height_adjusting_range,
               $handle_type,
               $wheel_type,
               $mechanical_system_type,
               $color_available,
               $product_weight,
               $backrest_size,
               $adjuster_size,
               $guarantee,
               $chair_arms,
               $table_top_size,
               $sitting_capacity,
               $no_of_top,
               $table_type,
               $shape,
               $wheels,
               $short_description,
               $description,
               $tag_number,
               $stock,
               $tags,
               $price,
               $discount,
               $corporate_discount,
               $tax,
               $imagesJSON,
               $variants,
               $colour
               $hashtags
          );

          if ($stmt->execute()) {
               $newProductId = $conn->insert_id;

               // ✅ If no variant was provided, set variant = self id
               if (empty($variants)) {
                    $update = $conn->prepare("UPDATE products SET variants = ? WHERE id = ?");
                    $update->bind_param("ii", $newProductId, $newProductId);
                    $update->execute();
                    $update->close();
               }
               echo "<script>
                    Swal.fire({
                         icon: 'success',
                         title: 'Product Added!',
                         text: 'The product has been successfully added.',
                         confirmButtonText: 'OK'
                    }).then(() => {
                         window.location.href = 'product-list.php';
                    });
               </script>";
          } else {
               echo "<script>
                    Swal.fire({
                         icon: 'error',
                         title: 'Error!',
                         text: 'Product could not be added. Please try again!',
                         confirmButtonText: 'OK'
                    });
               </script>";
          }
     }

     $stmt->close();
     $conn->close();
}
?>