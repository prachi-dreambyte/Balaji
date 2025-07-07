<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }

// Pagination settings
$limit = 10; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch products from the database
$sql = "SELECT * FROM products LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Count total products for pagination
$countSql = "SELECT COUNT(*) AS total FROM products";
$countResult = $conn->query($countSql);
$totalProducts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $limit);
?>

<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from techzaa.in/larkon/admin/product-add.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 20 Mar 2025 09:18:41 GMT -->

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
     <div class="wrapper">
          <?php include 'header.php'; ?>

          <div class="page-content">
               <div class="container-xxl">
                    <div class="row">
                         <div class="col-xl-12 col-lg-12">
                              <div class="card">
                                   <div class="card-header">
                                        <h4 class="card-title">Product List</h4>
                                   </div>
                                   <div class="card-body">
                                        <table class="table table-striped">
                                             <thead>
                                                  <tr>
                                                       <th>ID</th>
                                                       <th>Image</th>
                                                       <th>Product Name</th>
                                                       <th>Category</th>
                                                       <th>Brand</th>
                                                       <th>Stock</th>
                                                       <th>Price</th>
                                                       <th>Actions</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  <?php while ($row = $result->fetch_assoc()) { ?>
                                                     
                                                       <tr>
                                                            <td><?= $row['id']; ?></td>
                                                            <td>
                                                                 <?php
                                                                 $images = json_decode($row['images'], true); // Decode JSON string into an array
                                                                 $firstImage = $images[0] ?? 'default.jpg'; 
                                                                 ?>
                                                                 <img src="<?= $firstImage; ?>" alt="<?= $firstImage; ?>" width="50">
                                                            </td>
                                                            <td><?= $row['product_name']; ?></td>
                                                            <td><?= $row['category']; ?></td>
                                                            <td><?= $row['brand']; ?></td>
                                                            <td><?= $row['stock']; ?></td>
                                                            <td>₹<?= number_format($row['price'], 2); ?></td>
                                                            <td>
                                                                 <a href="product-edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                                 <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id']; ?>">Delete</button>
                                                            </td>
                                                       </tr>
                                                  <?php } ?>
                                             </tbody>
                                        </table>

                                        <!-- Pagination -->
                                        <nav aria-label="Product Pagination">
                                             <ul class="pagination justify-content-center">
                                                  <?php if ($page > 1) { ?>
                                                       <li class="page-item">
                                                            <a class="page-link" href="?page=<?= $page - 1; ?>">Previous</a>
                                                       </li>
                                                  <?php } ?>

                                                  <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                                       <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                                            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                                       </li>
                                                  <?php } ?>

                                                  <?php if ($page < $totalPages) { ?>
                                                       <li class="page-item">
                                                            <a class="page-link" href="?page=<?= $page + 1; ?>">Next</a>
                                                       </li>
                                                  <?php } ?>
                                             </ul>
                                        </nav>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>

               <footer class="footer">
                    <div class="container-fluid">
                         <div class="row">
                              <div class="col-12 text-center">
                                   <script>
                                        document.write(new Date().getFullYear())
                                   </script> &copy; Larkon. Crafted by <a href="https://1.envato.market/techzaa" target="_blank">Techzaa</a>
                              </div>
                         </div>
                    </div>
               </footer>
          </div>
     </div>

     <script src="assets/js/vendor.js"></script>
     <script src="assets/js/app.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function() {
                let productId = this.getAttribute("data-id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This product will be deleted permanently!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "delete_product.php?id=" + productId;
                    }
                });
            });
        });
    });
</script>
</body>

</html>

<?php
$conn->close();
?>