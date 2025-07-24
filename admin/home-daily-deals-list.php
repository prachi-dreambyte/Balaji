<?php
session_start();

// Direct DB connection for this page
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'balaji';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: auth-signin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Daily Deal Products | Larkon - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/css/vendor.min.css" rel="stylesheet">
    <link href="assets/css/icons.min.css" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet">
    <script src="assets/js/config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="wrapper">
        <?php include 'header.php'; ?>

        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Daily Deal Products</h4>
                                <a href="home-daily-deals.php" class="btn btn-primary btn-sm float-end">
                                    <i class="mdi mdi-plus"></i> Add New Deal
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Product Name</th>
                                                <th>Price</th>
                                                <th>Old Price</th>
                                                <!-- <th>Status</th> -->
                                                <th>Deal Period</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM `home_daily_deal` WHERE is_daily_deal = 1 ORDER BY created_at DESC";
                                            $result = $conn->query($sql);
                                            $count = 1;

                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $current_time = time();
                                                    $start_time = strtotime($row['start_time']);
                                                    $end_time = strtotime($row['end_time']);
                                                    
                                                    // $status = "";
                                                    // if ($current_time < $start_time) {
                                                    //     $status = "<span class='badge bg-warning'>Upcoming</span>";
                                                    // } elseif ($current_time > $end_time) {
                                                    //     $status = "<span class='badge bg-secondary'>Expired</span>";
                                                    // } else {
                                                    //     $status = "<span class='badge bg-success'>Active</span>";
                                                    // }
                                                    
                                                    echo "<tr>";
                                                    echo "<td>" . $count++ . "</td>";
                                                    echo "<td><img src='uploads/" . htmlspecialchars($row['image']) . "' class='img-thumbnail' width='80'></td>";
                                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                                    echo "<td>₹" . number_format($row['price'], 2) . "</td>";
                                                    echo "<td>₹" . number_format($row['old_price'], 2) . "</td>";
                                                    // echo "<td>" . $status . "</td>";
                                                    echo "<td>" . date('d M Y', $start_time) . " - " . date('d M Y', $end_time) . "</td>";
                                                   echo "<td>
                                                       <a href='home-daily-deals-edit.php?id={$row['id']}' class='btn btn-primary btn-sm me-1 rounded-pill d-inline-flex align-items-center'>
                                                             <i class='bi bi-pencil-fill me-1'></i> Edit
                                                        </a>
                                                      <button class='btn btn-danger btn-sm rounded-pill d-inline-flex align-items-center delete-btn' data-id='{$row['id']}'>
                                                             <i class='bi bi-trash3-fill me-1'></i> Delete
                                                      </button>
                                                       </td>";

                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='8' class='text-center'>No daily deal products found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
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
                            <script>document.write(new Date().getFullYear())</script> &copy; Larkon.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'delete-daily-deal.php?id=' + productId;
                    }
                });
            });
        });
    });
    </script>
</body>
</html>
