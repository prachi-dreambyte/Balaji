<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }
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
                                                  <div class="col-lg-4">

                                                       <div class="mb-3">
                                                            <label for="product-weight" class="form-label">Weight</label>
                                                            <input type="text" id="product-weight" name="weight" class="form-control" placeholder="In gm & kg">
                                                       </div>

                                                  </div>
                                                  <!-- <div class="col-lg-4">
                                                  <form method="post">
                                                       <label for="gender" class="form-label">Gender</label>
                                                       <select class="form-control" id="gender" data-choices data-choices-groups data-placeholder="Select Gender">
                                                            <option value="">Select Gender</option>
                                                            <option value="Men">Men</option>
                                                            <option value="Women">Women</option>
                                                            <option value="Other">Other</option>
                                                       </select>
                                                  </form>
                                             </div> -->
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
                                                            <label for="description" class="form-label">Description</label>
                                                            <textarea class="form-control bg-light-subtle" name="description" id="description" rows="7" placeholder="Short description about the product"></textarea>
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
                                                            <option value="Fashion" selected>Fashion</option>
                                                            <option value="Electronics">Electronics</option>
                                                            <option value="Watches">Watches</option>
                                                            <option value="Headphones">Headphones</option>
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

                                                       <label for="product-tex" class="form-label">Tex</label>
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

// Create products table if not exists
$sql = "CREATE TABLE IF NOT EXISTS products (
     id INT AUTO_INCREMENT PRIMARY KEY,
     product_name VARCHAR(255),
     category VARCHAR(255),
     brand VARCHAR(255),
     weight VARCHAR(50),
     size VARCHAR(255),
     description TEXT,
     tag_number VARCHAR(50) UNIQUE,
     stock INT,
     tags TEXT,
     price DECIMAL(10,2),
     discount DECIMAL(10,2),
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
     // Collect form data
     $product_name = $_POST['name'];
     $category = $_POST['category'];
     $brand = $_POST['brand'];
     $weight = $_POST['weight'];
     $size = isset($_POST['size']) ? implode(',', $_POST['size']) : ''; // Convert array to string
     $description = $_POST['description'];
     $tag_number = $_POST['tagnumber'];
     $stock = $_POST['stock'];
     $tags = isset($_POST['tag']) ? (is_array($_POST['tag']) ? implode(',', $_POST['tag']) : $_POST['tag']) : '';
     $price = $_POST['price'];
     $discount = $_POST['discount'];
     $tax = $_POST['tex'];

     // Image upload handling
     $imageArray = [];
     $uploadDir = "uploads/"; // Folder where images will be stored

     if (!empty($_FILES['file']['name'][0])) {
          foreach ($_FILES['file']['name'] as $key => $imageName) {
               $imageTmp = $_FILES['file']['tmp_name'][$key];
               $imagePath = str_replace("\\", "/", $uploadDir . time() . "_" . basename($imageName));


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
          // Insert data into database
          $sql = "INSERT INTO products 
                (product_name, category, brand, weight, size, description, tag_number, stock, tags, price, discount, tax, images) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

          $stmt = $conn->prepare($sql);
          $stmt->bind_param("sssssssissdds", $product_name, $category, $brand, $weight, $size, $description, $tag_number, $stock, $tags, $price, $discount, $tax, $imagesJSON);

          if ($stmt->execute()) {
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