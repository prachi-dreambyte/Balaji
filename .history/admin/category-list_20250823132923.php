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
     $img = mysqli_real_escape_string($conn, $_POST['category_image']);

     $sql = "UPDATE categories SET category_name=?, category_image=? WHERE id=?";
     $stmt = $conn->prepare($sql);
     $stmt->bind_param("ssi", $name, $img, $id);
     $stmt->execute();

     echo "<script>alert('Category updated successfully!'); window.location='category-list.php';</script>";
     exit;
}

// Fetch categories
$sql = 'SELECT * FROM categories';
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="utf-8" />
     <title>Categories List | Larkon - Responsive Admin Dashboard</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta name="description" content="A fully responsive premium admin dashboard template" />
     <meta name="author" content="Techzaa" />
     <meta http-equiv="X-UA-Compatible" content="IE=edge" />

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

                                             <!-- Edit Button -->
                                             <button class="btn btn-primary mt-2"
                                                  onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['category_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['category_image'], ENT_QUOTES); ?>')">
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
                    <form method="POST" action="">
                         <div class="modal-header">
                              <h5 class="modal-title">Edit Category</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                         </div>
                         <div class="modal-body">
                              <input type="hidden" name="edit_id" id="edit_id">
                              <div class="mb-3">
                                   <label class="form-label">Category Name</label>
                                   <input type="text" name="category_name" id="edit_name" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">Category Image (URL)</label>
                                   <input type="text" name="category_image" id="edit_image" class="form-control">
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

     <!-- JS -->
     <script>
          function openEditModal(id, name, image) {
               document.getElementById('edit_id').value = id;
               document.getElementById('edit_name').value = name;
               document.getElementById('edit_image').value = image;
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