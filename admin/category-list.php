<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
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
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
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
                                        <div class="rounded bg-secondary-subtle d-flex align-items-center justify-content-center mx-auto">
                                             <img src="uploads/<?php echo $row['category_image']; ?>" alt="" class="avatar-xl">
                                        </div>
                                        <h4 class="mt-3 mb-0"><?php echo htmlspecialchars($row['category_name']); ?></h4>
                                        
                                        <!-- Delete Button -->
                                        <button class="btn btn-danger mt-2" onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo $row['category_image']; ?>')">Delete</button>
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

<!-- JavaScript for Delete Confirmation -->
<script>
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
            window.location.href = "delete_category.php?id=" +categoryId + "&image=" + categoryImage;
        }
    });
}
</script>

<script src="assets/js/vendor.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
