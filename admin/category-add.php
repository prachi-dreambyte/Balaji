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
         <?php include 'header.php' ?>

          <!-- ==================================================== -->
          <!-- Start right Content here -->
          <!-- ==================================================== -->
          <div class="page-content">
               <div class="container-xxl">

                    <div class="row">
                    <form method="post" enctype="multipart/form-data">
                         <div class="col-xl-12 col-lg-12 ">

                              <!-- Thumbnail -->
                              <div class="card">
                                   <div class="card-header">
                                        <h4 class="card-title">Add Thumbnail Photo</h4>
                                   </div>
                                   <div class="card-body">
                                        <div class="dropzone">
                                             <div class="fallback">
                                                  <input name="category_image" type="file" />
                                             </div>
                                        </div>
                                   </div>
                              </div>

                              <!-- Banner -->
                              <div class="card">
                                   <div class="card-header">
                                        <h4 class="card-title">Add Category Banner</h4>
                                   </div>
                                   <div class="card-body">
                                        <div class="dropzone">
                                             <div class="fallback">
                                                  <input name="banner_image" type="file" />
                                             </div>
                                        </div>
                                   </div>
                              </div>

                              <!-- Category Form -->
                              <div class="card">
                                   <div class="card-body">
                                        <div class="row">
                                             <div class="col-lg-6">
                                                  <!-- Main Category -->
                                                  <div class="mb-3">
                                                       <label for="main-category" class="form-label">Main Category</label>
                                                       <input list="mainCategories" id="main-category" name="main_category" class="form-control" placeholder="Enter or Select Main Category">
                                                       <datalist id="mainCategories">
                                                            <?php
                                                            $result = $conn->query("SELECT DISTINCT Main_Category_name FROM categories WHERE Main_Category_name IS NOT NULL AND Main_Category_name != ''");
                                                            while ($row = $result->fetch_assoc()) {
                                                                 echo "<option value='".htmlspecialchars($row['Main_Category_name'])."'>";
                                                            }
                                                            ?>
                                                       </datalist>
                                                  </div>

                                                  <!-- Sub Category -->
                                                  <div class="mb-3">
                                                       <label for="category-title" class="form-label">Category Title</label>
                                                       <input type="text" id="category-title" name="category" class="form-control" placeholder="Enter Title">
                                                  </div>

                                 <!-- Display Order -->

                                      <div class="mb-3">
                                      <label for="display_order" class="form-label">Display Order</label>
                                     <input type="number" id="display_order" name="display_order" class="form-control" placeholder="Enter display order (e.g. 1, 2, 3)">
                                     <small class="text-muted">Smaller number will show first in the menu.</small>
                                      </div>

                                             </div>

                                             <div>
                                                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </form>
                    </div>
               </div>

               <!-- Footer -->
               <footer class="footer">
                    <div class="container-fluid">
                         <div class="row">
                              <div class="col-12 text-center">
                                   <script>document.write(new Date().getFullYear())</script> &copy; Larkon. Crafted by 
                                   <iconify-icon icon="iconamoon:heart-duotone" class="fs-18 align-middle text-danger"></iconify-icon> 
                                   <a href="https://1.envato.market/techzaa" class="fw-bold footer-text" target="_blank">Techzaa</a>
                              </div>
                         </div>
                    </div>
               </footer>
          </div>
     </div>

     <!-- Vendor Javascript -->
     <script src="assets/js/vendor.js"></script>
     <!-- App Javascript -->
     <script src="assets/js/app.js"></script>
</body>
</html>

<?php
// Create categories table if not exists
$tableQuery = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    Main_Category_name VARCHAR(255) NOT NULL,
    category_image TEXT,
    banner_image TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($tableQuery);

// Image upload function
function uploadImage($fileKey, $uploadDir = "uploads/") {
    if (!empty($_FILES[$fileKey]['name'])) {
        $fileName = time() . "_" . basename($_FILES[$fileKey]['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileType, $allowed)) {
            echo "<script>Swal.fire('Invalid File!', 'Only JPG, PNG, GIF, WEBP allowed.', 'error');</script>";
            exit();
        }
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
            return $targetFile;
        }
    }
    return '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = trim($_POST['category']);
    $mainCategory = trim($_POST['main_category']);
    $displayOrder = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $thumbnailPath = uploadImage('category_image');
    $bannerPath = uploadImage('banner_image');

    if (!empty($category) && !empty($mainCategory)) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name, Main_Category_name, category_image, banner_image, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $category, $mainCategory, $thumbnailPath, $bannerPath, $displayOrder);

        if ($stmt->execute()) {
            echo "<script>
                Swal.fire('Success!', 'Category Added Successfully.', 'success')
                .then(() => { window.location.href='category-add.php'; });
            </script>";
        } else {
            echo "<script>Swal.fire('Error!', 'Category already exists or database issue.', 'error');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>Swal.fire('Warning!', 'Please enter both main category and category name.', 'warning');</script>";
    }
}

?>
