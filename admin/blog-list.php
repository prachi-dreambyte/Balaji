<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 }

// Pagination settings
$limit = 10; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch products from the database
$sql = "SELECT * FROM blog LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Count total products for pagination
$countSql = "SELECT COUNT(*) AS total FROM blog";
$countResult = $conn->query($countSql);
$totalProducts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $limit);
?>

<!DOCTYPE html>
<html lang="en">




<head>
     <!-- Title Meta -->
     <meta charset="utf-8" />
     <title>Create Blog | Larkon - Responsive Admin Dashboard Template</title>
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
                                        <h4 class="card-title">Blog List</h4>
                                   </div>

                                   <div class="card-body">
                                        <div style="overflow-x: auto;">
                                         <table class="table table-striped">

                                             <thead>
                                                  <tr>
                                                       <th>ID</th>
                                                        <th>Title</th>
                                                         <th>slug</th>
                                                          <th>Main Content</th>
                                                           <th>Sub Content</th>
                                                           <th>Sub Description</th>
                                                            <th>Meta Title</th>
                                                             <th>Meta Description</th>
                                                              <th>OG Title</th>
                                                               <th>OG Description</th>
                                                               <th>Schema</th>
                                                               <th>Keywords</th>
                                                               <th>Rating</th>
                                                               <th>Main Images</th>
                                                               <th>Sub Images</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  <?php while ($row = $result->fetch_assoc()) { ?>
                                                     
                                                       <tr>
                                                            <td><?= $row['id']; ?></td>
                                                            <td><?= $row['title']; ?></td>
                                                            <td><?=$row['slug'];?></td>
                                                          <td><?=$row['main_content'];?></td>
                                                           <td><?=$row['sub_content'];?></td>
                                                           <td><?=$row['sub_description'];?></td>
                                                            <td><?=$row['meta_title'];?></td>
                                                             <td><?=$row['meta_description'];?></td>
                                                              <td><?=$row['og_title'];?></td>
                                                               <td><?=$row['og_description'];?></td>
                                                               <td><?=$row['schema_data'];?></td>
                                                               <td><?=$row['keywords'];?></td>
                                                               <td><?=$row['rating'];?></td>
                                                               <td>
                                                                
                                                                 <img src='./uploads/<?=$row['main_images'] ?>' alt="<?=$row['slug'];?>" height="50"/></td>
                                                            
                                                            <td>
                                                                  <?php
    $subImages = json_decode($row['sub_images'], true); // Decode JSON string to PHP array
    if (!empty($subImages)) {
        foreach ($subImages as $subImg) {
          echo "<img src='./uploads/{$subImg}' alt='{$row['slug']}' height='50' style='margin-right: 5px;' />";
        }
    } else {
        echo "No Sub Images";
    }
    ?>
                                                                 <?php
                                                                  $title = json_decode($row['title'], true); 
                                                                 $main_image = json_decode($row['main_images'], true); 
                                                                 $sub_images = json_decode($row['sub_images'], true);// Decode JSON string into an array
                                                             
                                                                 ?>
                                                            </td>                                                 
                                                            <td>
                                                                 <a href="blog-edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                                 <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id']; ?>">Delete</button>
                                                            </td>
                                                       </tr>
                                                  <?php } ?>
                                             </tbody>
                                        </table>
                                          </div>

                                        <!-- Pagination -->
                                        <nav aria-label="Blog Pagination">
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
                let blogId = this.getAttribute("data-id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This blog will be deleted permanently!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "delete_blog.php?id=" + blogId;
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