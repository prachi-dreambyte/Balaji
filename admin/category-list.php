<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id'])) {
     header("Location: auth-signin.php");
     exit;
}

// Handle Update
if (isset($_POST['update_category'])) {
     $id = intval($_POST['edit_id']);
     $name = mysqli_real_escape_string($conn, $_POST['category_name']);
     $main = mysqli_real_escape_string($conn, $_POST['main_category_name']);

     // Existing values (to keep if no new file uploaded)
     $old_image = $_POST['old_category_image'];
     $old_banner = $_POST['old_banner_image'];

     // Handle category image upload
     if (!empty($_FILES['category_image']['name'])) {
          $imgName = time() . "_" . basename($_FILES['category_image']['name']);
          $target = "uploads/" . $imgName;
          move_uploaded_file($_FILES['category_image']['tmp_name'], $target);
          $category_image = $target;
     } else {
          $category_image = $old_image;
     }

     // Handle banner image upload
     if (!empty($_FILES['banner_image']['name'])) {
          $bannerName = time() . "_" . basename($_FILES['banner_image']['name']);
          $target2 = "uploads/" . $bannerName;
          move_uploaded_file($_FILES['banner_image']['tmp_name'], $target2);
          $banner_image = $target2;
     } else {
          $banner_image = $old_banner;
     }

     $order = intval($_POST['display_order']); 

$sql = "UPDATE categories 
        SET category_name=?, Main_Category_name=?, category_image=?, banner_image=?, display_order=? 
        WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $name, $main, $category_image, $banner_image, $order, $id);

     $stmt->execute();

     echo "<script>alert('Category updated successfully!'); window.location='category-list.php';</script>";
     exit;
}

// Fetch categories
$sql = 'SELECT * FROM categories ORDER BY id DESC';
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="utf-8" />
     <title>Categories List | Larkon</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="shortcut icon" href="assets/images/favicon.ico">
     <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
     <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
     <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
     <script src="assets/js/config.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
     <div class="wrapper">
          <?php include 'header.php'; ?>

          <div class="page-content">
               <div class="container-xxl">
                    <div class="row">
                         <?php while ($row = $result->fetch_assoc()) { ?>
                              <div class="col-md-6 col-xl-3">
                                   <div class="card">
                                        <div class="card-body text-center">
                                             <div
                                                  class="rounded bg-secondary-subtle d-flex align-items-center justify-content-center mx-auto">
                                                  <img src="<?php echo $row['category_image']; ?>" alt="" class="avatar-xl">
                                             </div>
                                             <h4 class="mt-3 mb-0"><?php echo htmlspecialchars($row['category_name']); ?>
                                             </h4>
                                             <small class="text-muted">Main:
                                                  <?php echo htmlspecialchars($row['Main_Category_name']); ?></small>

                                             <!-- Edit Button -->
                                             <button class="btn btn-primary mt-2" onclick="openEditModal(
                                             <?php echo $row['id']; ?>,
                                           '<?php echo htmlspecialchars($row['category_name'], ENT_QUOTES); ?>',
                                         '<?php echo htmlspecialchars($row['Main_Category_name'], ENT_QUOTES); ?>',
                                         '<?php echo htmlspecialchars($row['category_image'], ENT_QUOTES); ?>',
                                           '<?php echo htmlspecialchars($row['banner_image'], ENT_QUOTES); ?>',
                                             <?php echo (int)$row['display_order']; ?>
                                        )">
                                             Edit
                                        </button>


                                             <!-- Delete Button -->
                                             <button class="btn btn-danger mt-2"
                                                  onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo $row['category_image']; ?>')">Delete</button>
                                        </div>
                                   </div>
                              </div>
                         <?php } ?>
                    </div>
               </div>

               <footer class="footer">
                    <div class="container-fluid">
                         <div class="row">
                              <div class="col-12 text-center">
                                   <script>document.write(new Date().getFullYear())</script> &copy; Larkon.
                              </div>
                         </div>
                    </div>
               </footer>
          </div>
     </div>

     <!-- Modal for Editing -->
     <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
               <div class="modal-content">
                    <form method="POST" action="" enctype="multipart/form-data">
                         <div class="modal-header">
                              <h5 class="modal-title">Edit Category</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                         </div>
                         <div class="modal-body">
                              <input type="hidden" name="edit_id" id="edit_id">
                              <input type="hidden" name="old_category_image" id="old_category_image">
                              <input type="hidden" name="old_banner_image" id="old_banner_image">

                              <div class="mb-3">
                                   <label class="form-label">Category Name</label>
                                   <input type="text" name="category_name" id="edit_name" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">Main Category Name</label>
                                   <input type="text" name="main_category_name" id="edit_main" class="form-control"
                                        required>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">Category Image</label><br>
                                   <img id="preview_image" src="" alt="Current Image" class="mb-2"
                                        style="max-width: 100px;"><br>
                                   <input type="file" name="category_image" class="form-control">
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">Banner Image</label><br>
                                   <img id="preview_banner" src="" alt="Current Banner" class="mb-2"
                                        style="max-width: 100px;"><br>
                                   <input type="file" name="banner_image" class="form-control">
                              </div>
                              <div class="mb-3">
                                 <label class="form-label">Display Order</label>
                                 <input type="number" name="display_order" id="edit_order" class="form-control" required>
                                 <small class="text-muted">Smaller number will show first in menu.</small>
                              </div>

                         </div>
                         <div class="modal-footer">
                              <button type="submit" name="update_category" class="btn btn-success">Update</button>
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                         </div>
                    </form>
               </div>
          </div>
     </div>

     <script>
         function openEditModal(id, name, main, image, banner, order) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_main').value = main;
    document.getElementById('old_category_image').value = image;
    document.getElementById('old_banner_image').value = banner;
    document.getElementById('preview_image').src = image;
    document.getElementById('preview_banner').src = banner;
    document.getElementById('edit_order').value = order;
    var myModal = new bootstrap.Modal(document.getElementById('editModal'));
    myModal.show();
}


          function confirmDelete(categoryId, categoryImage) {
               Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
               }).then((result) => {
                    if (result.isConfirmed) {
                         window.location.href = "delete_category.php?id=" + categoryId + "&image=" + categoryImage;
                    }
               });
          }
     </script>

     <script src="assets/js/vendor.js"></script>
     <script src="assets/js/app.js"></script>
</body>

</html>