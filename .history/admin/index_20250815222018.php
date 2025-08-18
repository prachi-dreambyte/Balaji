<?php
session_start();
include "./db_connect.php";
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
}


// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "balaji");
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
}

// Fetch 5 most recent orders
$sql = "SELECT id, order_id, user_id, address_line, city, state, zipcode, contact_no, payment_method, status, created_at
        FROM orders
        ORDER BY created_at DESC
        LIMIT 5";

$result = $conn->query($sql);

// $result = $conn->query($sql);

// Fetch total orders
$sqlTotal = "SELECT COUNT(*) AS total_orders FROM orders";
$resultTotal = $conn->query($sqlTotal);
$totalOrders = ($resultTotal && $row = $resultTotal->fetch_assoc()) ? $row['total_orders'] : 0;

// Fetch orders for last week
$sqlLastWeek = "SELECT COUNT(*) AS last_week_orders 
                FROM orders 
                WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)";
$resultLastWeek = $conn->query($sqlLastWeek);
$lastWeekOrders = ($resultLastWeek && $row = $resultLastWeek->fetch_assoc()) ? $row['last_week_orders'] : 0;

// Fetch orders for week before last

$sqlWeekBefore = "SELECT COUNT(*) AS prev_week_orders 
                  FROM orders 
                  WHERE YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE() - INTERVAL 2 WEEK, 1)";
$resultWeekBefore = $conn->query($sqlWeekBefore);
$prevWeekOrders = ($resultWeekBefore && $row = $resultWeekBefore->fetch_assoc()) ? $row['prev_week_orders'] : 0;

// Calculate percentage change
$percentageChange = 0;
if ($prevWeekOrders > 0) {
     $percentageChange = (($lastWeekOrders - $prevWeekOrders) / $prevWeekOrders) * 100;
}

// Close the database connection of Total Orders

//start db of total products
// Fetch total products
$sqlTotalProducts = "SELECT COUNT(*) AS total_products FROM products";
$resultTotalProducts = $conn->query($sqlTotalProducts);
$totalProducts = ($resultTotalProducts && $row = $resultTotalProducts->fetch_assoc()) ? $row['total_products'] : 0;

// Fetch last month's products
$sqlLastMonth = "SELECT COUNT(*) AS last_month_products 
                 FROM products 
                 WHERE YEAR(`created_at`) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                 AND MONTH(`created_at`) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
$resultLastMonth = $conn->query($sqlLastMonth);
$lastMonthProducts = ($resultLastMonth && $row = $resultLastMonth->fetch_assoc()) ? $row['last_month_products'] : 0;

// Fetch previous month's products
$sqlPrevMonth = "SELECT COUNT(*) AS prev_month_products 
                 FROM products 
                 WHERE YEAR(`created_at`) = YEAR(CURRENT_DATE - INTERVAL 2 MONTH)
                 AND MONTH(`created_at`) = MONTH(CURRENT_DATE - INTERVAL 2 MONTH)";
$resultPrevMonth = $conn->query($sqlPrevMonth);
$prevMonthProducts = ($resultPrevMonth && $row = $resultPrevMonth->fetch_assoc()) ? $row['prev_month_products'] : 0;

// Calculate percentage change
$productPercentageChange = 0;
if ($prevMonthProducts > 0) {
     $productPercentageChange = (($lastMonthProducts - $prevMonthProducts) / $prevMonthProducts) * 100;
}


// Close the database connection of Total products

//start db of total user
// Total users
$sqlTotalUsers = "SELECT COUNT(*) AS total_users FROM signup";
$resultTotalUsers = $conn->query($sqlTotalUsers);
$totalUsers = ($resultTotalUsers && $row = $resultTotalUsers->fetch_assoc()) ? $row['total_users'] : 0;

// Last month users
$sqlLastMonthUsers = "SELECT COUNT(*) AS last_month_users 
                      FROM signup 
                      WHERE YEAR(`created_at`) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                      AND MONTH(`created_at`) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
$resultLastMonthUsers = $conn->query($sqlLastMonthUsers);
$lastMonthUsers = ($resultLastMonthUsers && $row = $resultLastMonthUsers->fetch_assoc()) ? $row['last_month_users'] : 0;

// Previous month users
$sqlPrevMonthUsers = "SELECT COUNT(*) AS prev_month_users 
                      FROM signup 
                      WHERE YEAR(`created_at`) = YEAR(CURRENT_DATE - INTERVAL 2 MONTH)
                      AND MONTH(`created_at`) = MONTH(CURRENT_DATE - INTERVAL 2 MONTH)";
$resultPrevMonthUsers = $conn->query($sqlPrevMonthUsers);
$prevMonthUsers = ($resultPrevMonthUsers && $row = $resultPrevMonthUsers->fetch_assoc()) ? $row['prev_month_users'] : 0;

// Percentage change
$userPercentageChange = 0;
if ($prevMonthUsers > 0) {
     $userPercentageChange = (($lastMonthUsers - $prevMonthUsers) / $prevMonthUsers) * 100;
}
// Close the database connection of Total user

//start db of total revenue
// Assuming your DB connection is $conn
$sqlRevenue = "SELECT SUM(amount) AS total_revenue FROM orders WHERE payment_status = 'paid'";
$resRevenue = $conn->query($sqlRevenue);
$rowRevenue = $resRevenue->fetch_assoc();
$totalRevenue = $rowRevenue['total_revenue'] ?? 0;

// Format with currency
$formattedRevenue = 'Rs.' . number_format($totalRevenue);
?>

<!DOCTYPE html>
<html lang="en">
<!-- Mirrored from techzaa.in/larkon/admin/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 20 Mar 2025 09:19:37 GMT -->

<head>
     <!-- Title Meta -->
     <meta charset="utf-8" />
     <title>Dashboard | Larkon - Responsive Admin Dashboard Template</title>
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
          <?php
          include 'header.php';
          ?>
          <!-- ==================================================== -->
          <!-- Start right Content here -->
          <!-- ==================================================== -->
          <div class="page-content">

               <!-- Start Container Fluid -->
               <div class="container-fluid">

                    <!-- Start here.... -->
                    <div class="row">
                         <div class="col-xxl-5">
                              <div class="row">


                                   <div class="col-md-6">
                                        <div class="card overflow-hidden">
                                             <div class="card-body">
                                                  <div class="row">
                                                       <div class="col-6">
                                                            <div class="avatar-md bg-soft-primary rounded">
                                                                 <iconify-icon icon="solar:cart-5-bold-duotone"
                                                                      class="avatar-title fs-32 text-primary"></iconify-icon>
                                                            </div>
                                                       </div> <!-- end col -->
                                                       <div class="col-6 text-end">
                                                            <p class="text-muted mb-0 text-truncate">Total Orders</p>
                                                            <h3 class="text-dark mt-1 mb-0">
                                                                 <?= number_format($totalOrders) ?>
                                                            </h3>
                                                       </div> <!-- end col -->
                                                  </div> <!-- end row-->
                                             </div> <!-- end card body -->
                                             <div class="card-footer py-2 bg-light bg-opacity-50">
                                                  <div class="d-flex align-items-center justify-content-between">
                                                       <div>
                                                            <span
                                                                 class="<?= ($percentageChange >= 0) ? 'text-success' : 'text-danger' ?>">
                                                                 <i
                                                                      class="bx <?= ($percentageChange >= 0) ? 'bxs-up-arrow' : 'bxs-down-arrow' ?> fs-12"></i>
                                                                 <?= number_format($percentageChange, 1) ?>%
                                                            </span>
                                                            <span class="text-muted ms-1 fs-12">Last Week</span>
                                                       </div>
                                                       <a href="orders-list.php"
                                                            class="text-reset fw-semibold fs-12">View More</a>
                                                  </div>
                                             </div> <!-- end card body -->
                                        </div> <!-- end card -->
                                   </div> <!-- end col -->
                                   <div class="col-md-6">
                                        <div class="card overflow-hidden">
                                             <div class="card-body">
                                                  <div class="row">
                                                       <div class="col-6">
                                                            <div class="avatar-md bg-soft-primary rounded">
                                                                 <i
                                                                      class="bx bx-award avatar-title fs-24 text-primary"></i>
                                                            </div>
                                                       </div> <!-- end col -->
                                                       <div class="col-6 text-end">
                                                            <p class="text-muted mb-0 text-truncate">Total Products</p>
                                                            <h3 class="text-dark mt-1 mb-0">
                                                                 <?= number_format($totalProducts) ?>
                                                            </h3>
                                                       </div> <!-- end col -->
                                                  </div> <!-- end row-->
                                             </div> <!-- end card body -->
                                             <div class="card-footer py-2 bg-light bg-opacity-50">
                                                  <div class="d-flex align-items-center justify-content-between">
                                                       <div>
                                                            <span
                                                                 class="<?= ($productPercentageChange >= 0) ? 'text-success' : 'text-danger' ?>">
                                                                 <i
                                                                      class="bx <?= ($productPercentageChange >= 0) ? 'bxs-up-arrow' : 'bxs-down-arrow' ?> fs-12"></i>
                                                                 <?= number_format($productPercentageChange, 1) ?>%
                                                            </span>
                                                            <span class="text-muted ms-1 fs-12">Last Month</span>
                                                       </div>
                                                       <a href="product-list.php"
                                                            class="text-reset fw-semibold fs-12">View More</a>
                                                  </div>
                                             </div> <!-- end card body -->
                                        </div> <!-- end card -->
                                   </div> <!-- end col -->
                                   <div class="col-md-6">
                                        <div class="card overflow-hidden">
                                             <div class="card-body">
                                                  <div class="row">
                                                       <div class="col-6">
                                                            <div class="avatar-md bg-soft-primary rounded">
                                                                 <i
                                                                      class="bx bxs-backpack avatar-title fs-24 text-primary"></i>
                                                            </div>
                                                       </div> <!-- end col -->
                                                       <div class="col-6 text-end">
                                                            <p class="text-muted mb-0 text-truncate">Total User</p>
                                                            <h3 class="text-dark mt-1 mb-0">
                                                                 <?= number_format($totalUsers) ?>
                                                            </h3>
                                                       </div> <!-- end col -->
                                                  </div> <!-- end row-->
                                             </div> <!-- end card body -->
                                             <div class="card-footer py-2 bg-light bg-opacity-50">
                                                  <div class="d-flex align-items-center justify-content-between">
                                                       <div>
                                                            <span
                                                                 class="<?= ($userPercentageChange >= 0) ? 'text-success' : 'text-danger' ?>">
                                                                 <i
                                                                      class="bx <?= ($userPercentageChange >= 0) ? 'bxs-up-arrow' : 'bxs-down-arrow' ?> fs-12"></i>
                                                                 <?= number_format($userPercentageChange, 1) ?>%
                                                            </span>
                                                            <span class="text-muted ms-1 fs-12">Last Month</span>
                                                       </div>
                                                       <a href="customer-details.php"
                                                            class="text-reset fw-semibold fs-12">View More</a>
                                                  </div>
                                             </div> <!-- end card body -->
                                        </div> <!-- end card -->
                                   </div> <!-- end col -->
                                   <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="card overflow-hidden h-100">
                                             <div class="card-body">
                                                  <div class="row align-items-center">
                                                       <div class="col-4 col-sm-3">
                                                            <div
                                                                 class="avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                                                                 <i class="bx bx-dollar-circle text-primary fs-24"></i>
                                                            </div>
                                                       </div>
                                                       <div class="col-8 col-sm-9 text-end text-sm-end text-center">
                                                            <p class="text-muted mb-0 text-truncate">Total Revenue</p>
                                                            <h3 class="text-dark mt-1 mb-0"><?= $formattedRevenue; ?>
                                                            </h3>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="card-footer py-2 bg-light bg-opacity-50">
                                                  <div
                                                       class="d-flex flex-wrap align-items-center justify-content-between">
                                                       <div class="mb-2 mb-sm-0">
                                                            <span class="text-danger"><i
                                                                      class="bx bxs-down-arrow fs-12"></i> 10.6%</span>
                                                            <span class="text-muted ms-1 fs-12">Last Month</span>
                                                       </div>
                                                       <a href="orders-list.php"
                                                            class="text-reset fw-semibold fs-12">View More</a>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>

                              </div> <!-- end row -->
                         </div> <!-- end col -->

                         <div class="col-xxl-7">
                              <div class="card">
                                   <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                             <h4 class="card-title">Performance</h4>
                                             <div>
                                                  <button class="btn btn-sm btn-outline-light"
                                                       data-period="1y">ALL</button>
                                                  <button class="btn btn-sm btn-outline-light"
                                                       data-period="1M">1M</button>
                                                  <button class="btn btn-sm btn-outline-light"
                                                       data-period="3M">3M</button>
                                                  <button class="btn btn-sm btn-outline-light active"
                                                       data-period="6M">6M</button>
                                             </div>
                                        </div> <!-- end card-title-->

                                        <div dir="ltr">
                                             <div id="dash-performance-chart" class="apex-charts"></div>
                                        </div>
                                   </div> <!-- end card body -->
                              </div> <!-- end card -->
                         </div> <!-- end col -->
                    </div> <!-- end row -->



                    <div class="card">
                         <div class="card-body">
                              <div class="d-flex align-items-center justify-content-between">
                                   <h4 class="card-title">Recent Orders</h4>

                              </div>
                         </div>

                         <div class="table-responsive table-centered">
                              <table class="table mb-0">
                                   <thead class="bg-light bg-opacity-50">
                                        <tr>
                                             <th class="ps-3">Order ID</th>
                                             <th>Date</th>
                                             <th>Customer ID</th>
                                             <th>Phone No.</th>
                                             <th>Address</th>
                                             <th>Payment Type</th>
                                             <th>Status</th>
                                        </tr>
                                   </thead>
                                   <tbody>
                                        <?php if ($result && $result->num_rows > 0) {
                                             while ($row = $result->fetch_assoc()) { ?>
                                                  <tr>
                                                       <td class="ps-3">
                                                            <a
                                                                 href="order-details.php?id=<?= $row['id']; ?>"><?= htmlspecialchars($row['order_id']); ?></a>
                                                       </td>
                                                       <td><?= date("d M Y", strtotime($row['created_at'])); ?></td>
                                                       <td><?= htmlspecialchars($row['user_id']); ?></td>
                                                       <td><?= htmlspecialchars($row['contact_no']); ?></td>
                                                       <td>
                                                            <?= htmlspecialchars($row['address_line']); ?>,
                                                            <?= htmlspecialchars($row['city']); ?>,
                                                            <?= htmlspecialchars($row['state']); ?> -
                                                            <?= htmlspecialchars($row['zipcode']); ?>
                                                       </td>
                                                       <td><?= htmlspecialchars($row['payment_method']); ?></td>
                                                       <td>
                                                            <?php
                                                            $statusClass = ($row['status'] == 'completed') ? 'text-success' : 'text-primary';
                                                            echo "<i class='bx bxs-circle {$statusClass} me-1'></i>" . ucfirst($row['status']);
                                                            ?>
                                                       </td>
                                                  </tr>
                                             <?php }
                                        } else { ?>
                                             <tr>
                                                  <td colspan="7" class="text-center">No recent orders found.</td>
                                             </tr>
                                        <?php } ?>
                                   </tbody>
                              </table>
                         </div>

                         <div class="card-footer border-top d-flex justify-content-between align-items-center">
                              <div class="text-muted">
                                   Showing <?= $result ? $result->num_rows : 0; ?> recent orders
                              </div>
                              <a href="orders-list.php" class="btn btn-sm btn-primary">
                                   View More Orders
                              </a>

                         </div>
                    </div>

               </div>
               <!-- End Container Fluid -->

               <!-- ========== Footer Start ========== -->
               <footer class="footer">
                    <div class="container-fluid">
                         <div class="row">
                              <div class="col-12 text-center">
                                   <script>document.write(new Date().getFullYear())</script> &copy; Larkon. Crafted by
                                   <iconify-icon icon="iconamoon:heart-duotone"
                                        class="fs-18 align-middle text-danger"></iconify-icon> <a
                                        href="https://1.envato.market/techzaa" class="fw-bold footer-text"
                                        target="_blank">Saksham</a>
                              </div>
                         </div>
                    </div>
               </footer>
               <!-- ========== Footer End ========== -->

          </div>
          <!-- ==================================================== -->
          <!-- End Page Content -->
          <!-- ==================================================== -->

     </div>
     <!-- END Wrapper -->

     <!-- Vendor Javascript (Require in all Page) -->
     <script src="assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="assets/js/app.js"></script>

     <!-- Vector Map Js -->
     <script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
     <script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
     <script src="assets/vendor/jsvectormap/maps/world.js"></script>

     <!-- Dashboard Js -->
     <script src="assets/js/pages/dashboard.js"></script>
     <script>
          document.addEventListener('DOMContentLoaded', function () {

               const chartElement = document.getElementById('dash-performance-chart');
               if (!chartElement) {
                    console.error('Chart container not found!');
                    return;
               }

               let chart; // Store the chart instance so we can update it later

               // Function to fetch and render chart
               function loadChart(period = '1y') {
                    fetch(`get_order_revenue.php?period=${period}`)
                         .then(response => {
                              if (!response.ok) {
                                   throw new Error(`HTTP error! status: ${response.status}`);
                              }
                              return response.json();
                         })
                         .then(data => {
                              if (data.error) {
                                   throw new Error(data.error);
                              }

                              const shortMonths = data.categories.map(month => {
                                   return new Date(month + '-01').toLocaleDateString('en-US', {
                                        month: 'short'
                                   });
                              });

                              const options = {
                                   series: [{
                                        name: "Revenue",
                                        type: "bar",
                                        data: data.revenueData
                                   }],
                                   chart: {
                                        height: 350,
                                        type: 'line',
                                        toolbar: {
                                             show: false
                                        }
                                   },
                                   colors: ["#ff6c2f"],
                                   xaxis: {
                                        categories: shortMonths,
                                        labels: {
                                             style: {
                                                  colors: '#8c9097'
                                             }
                                        }
                                   },
                                   yaxis: {
                                        labels: {
                                             formatter: function (val) {
                                                  return '₹' + val.toLocaleString();
                                             },
                                             style: {
                                                  colors: '#8c9097'
                                             }
                                        }
                                   },
                                   tooltip: {
                                        y: {
                                             formatter: function (val) {
                                                  return '₹' + val.toLocaleString();
                                             }
                                        }
                                   }
                              };

                              if (chart) {
                                   chart.updateOptions(options); // Update chart instead of recreating
                              } else {
                                   chart = new ApexCharts(chartElement, options);
                                   chart.render();
                              }
                         })
                         .catch(error => {
                              console.error('Error:', error);
                              chartElement.innerHTML = `
                    <div class="alert alert-danger">
                        Failed to load chart data: ${error.message}
                    </div>
                `;
                         });
               }

               // Attach click event to period buttons
               document.querySelectorAll('[data-period]').forEach(button => {
                    button.addEventListener('click', function () {
                         const period = this.getAttribute('data-period');
                         loadChart(period);
                    });
               });

               // Initial load (default 1 year)
               loadChart('1y');
          });
     </script>

</body>


<!-- Mirrored from techzaa.in/larkon/admin/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 20 Mar 2025 09:19:41 GMT -->

</html>