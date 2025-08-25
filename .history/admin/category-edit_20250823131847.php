<?php
session_start();
include "./db_connect.php";
if (!isset($_SESSION['admin_id'])) {
     header("Location: auth-signin.php");
     exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
     header("Location: category-list.php");
     exit;
}

$category_id = intval($_GET['id']);

// Fetch existing category
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
     echo "<script>alert('Category not found'); window.location.href='category-list.php';</script>";
     exit;
}

// Function to handle image upload
function uploadImage($fileKey, $oldImage = '', $uploadDir = "uploads/")
{
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
               if (!empty($oldImage) && file_exists($oldImage)) {
                    unlink($oldImage); // delete old file
               }
               return $targetFile;
          }
     }
     return $oldImage; // keep old image if not replaced
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $mainCategory = trim($_POST['main_category']);
     $subCategory = trim($_POST['category']);

     $thumbnailPath = uploadImage('category_image', $category['category_image']);
     $bannerPath = uploadImage('banner_image', $category['banner_image']);

     if (!empty($mainCategory) && !empty($subCategory)) {
          $stmt = $conn->prepare("UPDATE categories 
            SET Main_Category_name=?, category_name=?, category_image=?, banner_image=? 
            WHERE id=?");
          $stmt->bind_param("ssssi", $mainCategory, $subCategory, $thumbnailPath, $bannerPath, $category_id);

          if ($stmt->execute()) {
               echo "<script>
                Swal.fire('Updated!', 'Category Updated Successfully.', 'success')
                .then(() => { window.location.href='category-list.php'; });
            </script>";
          } else {
               echo "<script>Swal.fire('Error!', 'Update failed. Try again.', 'error');</script>";
          }
          $stmt->close();
     } else {
          echo "<script>Swal.fire('Warning!', 'Please enter both main and sub category.', 'warning');</script>";
     }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="utf-8" />
     <title>Edit Category | Larkon</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
     <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
     <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
     <script src="assets/js/config.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
     <div class="wrapper">
          <?php include 'header.php'; ?>

          <div class="page-content">
               <div class="container-xxl">
                    <form method="post" enctype="multipart/form-data">
                         <div class="row">
                              <div class="col-lg-12">

                                   <!-- Thumbnail -->
                                   <div class="card mb-3">
                                        <div class="card-header"><h4 class="card-title">Thumbnail</h4></div>
                                        <div class="card-body">
                                             <img src="<?= htmlspecialchars($category['category_image']) ?>" alt="Thumbnail" class="img-thumbnail mb-2" style="max-width:150px;">
                                             <input type="file" name="category_image" class="form-control">
                                        </div>
                                   </div>

                                   <!-- Banner -->
                                   <div class="card mb-3">
                                        <div class="card-header"><h4 class="card-title">Banner</h4></div>
                                        <div class="card-body">
                                             <img src="<?= htmlspecialchars($category['banner_image']) ?>" alt="Banner" class="img-thumbnail mb-2" style="max-width:150px;">
                                             <input type="file" name="banner_image" class="form-control">
                                        </div>
                                   </div>

                                   <!-- Category Info -->
                                   <div class="card">
                                        <div class="card-header"><h4 class="card-title">Category Info</h4></div>
                                        <div class="card-body row">

                                             <div class="col-lg-6 mb-3">
                                                  <label class="form-label">Main Category</label>
                                                  <input list="mainCategories" name="main_category" class="form-control"
                                                         value="<?= htmlspecialchars($category['Main_Category_name']) ?>">
                                                  <datalist id="mainCategories">
                                                       <?php
                                                       $result = $conn->query("SELECT DISTINCT Main_Category_name FROM categories WHERE Main_Category_name IS NOT NULL AND Main_Category_name!=''");
                                                       while ($row = $result->fetch_assoc()) {
                                                            echo "<option value='" . htmlspecialchars($row['Main_Category_name']) . "'>";
                                                       }
                                                       ?>
                                                  </datalist>
                                             </div>

                                             <div class="col-lg-6 mb-3">
                                                  <label class="form-label">Sub Category</label>
                                                  <input type="text" name="category" class="form-control"
                                                         value="<?= htmlspecialchars($category['category_name']) ?>">
                                             </div>

                                             <div class="col-lg-12">
                                                  <button type="submit" class="btn btn-primary">Update</button>
                                                  <a href="category-list.php" class="btn btn-secondary">Cancel</a>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </form>
               </div>

               <footer class="footer">
                    <div class="container-fluid">
                         <div class="row">
                              <div class="col-12 text-center">
                                   <script>document.write(new Date().getFullYear())</script> &copy; Larkon
                              </div>
                         </div>
                    </div>
               </footer>
          </div>
     </div>

     <script src="assets/js/vendor.js"></script>
     <script src="assets/js/app.js"></script>
</body>
</html>
