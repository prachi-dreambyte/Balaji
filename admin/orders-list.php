<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 } // Include database connection file

// Pagination settings
$limit = 10; // Number of orders per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch orders from the database
$sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Count total orders for pagination
$countSql = "SELECT COUNT(*) AS total FROM orders";
$countResult = $conn->query($countSql);
$totalOrders = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalOrders / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="utf-8" />
     <title>Orders List | Admin Panel</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="shortcut icon" href="assets/images/favicon.ico">

     <!-- Vendor css (Require in all Page) -->
     <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

     <!-- Icons css (Require in all Page) -->
     <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

     <!-- App css (Require in all Page) -->
     <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

     <!-- Theme Config js (Require in all Page) -->
     <script src="assets/js/config.js"></script> <!-- Iconify -->
     <script src="https://code.iconify.design/2/2.2.1/iconify.min.js"></script>
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
                                        <h4 class="card-title">Order List</h4>
                                   </div>
                                   <div class="card-body">
                                        <table class="table table-striped">
                                             <thead>
                                                  <tr>
                                                       <th>Order ID</th>
                                                       <th>User ID</th>
                                                       <th>Total Price</th>
                                                       <th>Discount</th>
                                                       <th>Final Price</th>
                                                       <th>Status</th>
                                                       <th>Actions</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  <?php while ($row = $result->fetch_assoc()) { ?>

                                                       <tr>
                                                            <td><?= $row['order_id']; ?></td>
                                                            <td><?= $row['user_id']; ?></td>
                                                            <td>₹<?= number_format($row['amount'], 2); ?></td>
                                                            <td>₹<?= number_format($row['discount'], 2); ?></td>
                                                             <td>₹<?= number_format($row['discount'] + $row['amount'], 2); ?></td>
                                                            <td><?= ucfirst($row['status']); ?></td>
                                                            <td>
                                                                 <!-- Edit Button -->
                                                                 <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $row['order_id']; ?>" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">
                                                                      <span class="iconify" data-icon="mdi:pencil-outline"></span>
                                                                 </button>
                                                                 <!-- Delete Button with AJAX -->
                                                                 <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['order_id']; ?>">
                                                                      <span class="iconify" data-icon="mdi:trash-can-outline"></span>
                                                                 </button>
                                                            </td>
                                                       </tr>
                                                  <?php } ?>
                                             </tbody>
                                        </table>

                                        <!-- Pagination -->
                                        <nav aria-label="Orders Pagination">
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


               <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                         <div class="modal-content">
                              <div class="modal-header">
                                   <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                   <div id="order-details-content">
                                        <!-- Order details will be loaded here dynamically -->
                                        <div class="text-center">
                                             <p>Loading...</p>
                                        </div>
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
                                   </script> &copy; Your Company Name
                              </div>
                         </div>
                    </div>
               </footer>
          </div>
     </div>

     <script src="assets/js/vendor.js"></script>
     <script src="assets/js/app.js"></script>

     <script>
          document.addEventListener("DOMContentLoaded", function() {
               document.querySelectorAll(".delete-btn").forEach(button => {
                    button.addEventListener("click", function() {
                         let orderId = this.getAttribute("data-id");

                         Swal.fire({
                              title: "Are you sure?",
                              text: "This order will be deleted permanently!",
                              icon: "warning",
                              showCancelButton: true,
                              confirmButtonColor: "#d33",
                              cancelButtonColor: "#3085d6",
                              confirmButtonText: "Yes, delete it!"
                         }).then((result) => {
                              if (result.isConfirmed) {
                                   fetch("delete_order.php", {
                                             method: "POST",
                                             headers: {
                                                  "Content-Type": "application/x-www-form-urlencoded"
                                             },
                                             body: "id=" + orderId
                                        })
                                        .then(response => response.text())
                                        .then(data => {
                                             if (data === "success") {
                                                  Swal.fire("Deleted!", "The order has been deleted.", "success");
                                                  setTimeout(() => {
                                                       location.reload();
                                                  }, 1000);
                                             } else {
                                                  Swal.fire("Error!", "Failed to delete the order.", "error");
                                             }
                                        })
                                        .catch(error => {
                                             Swal.fire("Error!", "Something went wrong.", "error");
                                        });
                              }
                         });
                    });
               });
          });
     </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $(".edit-btn").on("click", function(e) {
      e.preventDefault(); 

      var orderId = $(this).data("id");

      if (!orderId) {
        console.error("Order ID not found in data-id attribute.");
        return;
      }

      $("#order-details-content").html('<div class="text-center"><p>Loading...</p></div>');

      $.ajax({
        url: "order-details.php",
        method: "POST",
        data: { id: orderId },
        success: function(response) {
          $("#order-details-content").html(response);
        },
        error: function(xhr, status, error) {
          $("#order-details-content").html("<p class='text-danger'>Error loading order details.</p>");
          console.error("AJAX Error: ", status, error);
        }
      });
    });
  });
</script>



     

</body>

</html>

<?php
$conn->close();
?>