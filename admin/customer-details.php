<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
    header("Location: auth-signin.php");
    exit;
}

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch customers
$sql = "SELECT * FROM signup ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Count total customers
$countSql = "SELECT COUNT(*) AS total FROM signup";
$countResult = $conn->query($countSql);
$totalCustomers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalCustomers / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Customer Details | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/config.js"></script>
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
                                <h4 class="card-title">Customer List</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Company</th>
                                            <th>Account Type</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?= $row['id']; ?></td>
                                                <td><?= htmlspecialchars($row['name']); ?></td>
                                                <td><?= htmlspecialchars($row['email']); ?></td>
                                                <td><?= htmlspecialchars($row['phone']); ?></td>
                                                <td><?= htmlspecialchars($row['company_name']); ?></td>
                                                <td><?= htmlspecialchars($row['account_type']); ?></td>
                                                <td><?= $row['created_at']; ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning view-btn"
                                                        data-id="<?= $row['id']; ?>" data-bs-toggle="modal"
                                                        data-bs-target="#customerDetailsModal">
                                                        <span class="iconify" data-icon="mdi:eye-outline"></span>
                                                    </button>
                                                    <!-- <button class="btn btn-sm btn-danger delete-btn"
                                                        data-id="<?= $row['id']; ?>">
                                                        <span class="iconify" data-icon="mdi:trash-can-outline"></span>
                                                    </button> -->
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <!-- Pagination -->
                                <nav aria-label="Customers Pagination">
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

            <!-- Modal -->
            <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="customerDetailsModalLabel">Customer Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="customer-details-content">
                            <div class="text-center">
                                <p>Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center">
                            <script>document.write(new Date().getFullYear())</script> &copy; Your Company Name
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $(".delete-btn").click(function () {
                let id = $(this).data("id");
                Swal.fire({
                    title: "Are you sure?",
                    text: "This customer will be deleted permanently!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("delete_customer.php", { id: id }, function (data) {
                            if (data === "success") {
                                Swal.fire("Deleted!", "The customer has been deleted.", "success");
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                Swal.fire("Error!", "Failed to delete customer.", "error");
                            }
                        });
                    }
                });
            });

            $(".view-btn").click(function () {
                let id = $(this).data("id");
                $("#customer-details-content").html('<div class="text-center"><p>Loading...</p></div>');
                $.post("customer-details-modal.php", { id: id }, function (data) {
                    $("#customer-details-content").html(data);
                });
            });
        });
    </script>

</body>

</html>

<?php $conn->close(); ?>