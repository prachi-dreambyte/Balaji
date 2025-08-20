<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }
 $stmt = $conn->prepare('SELECT * FROM categories');
 $stmt->execute();
 $result1 = $stmt->get_result();
 $stmt->close();
// Get product ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid product ID.");
}
$product_id = intval($_GET['id']);

// Fetch product details
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// Close statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">



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
                    <div class="col-xl-8 col-lg-10 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Product</h4>
                            </div>
                            <div class="card-body">
                                <form action="update_product.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $product['id']; ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                    <label for="product-categories" class="form-label">Product Categories</label>
                                                       <select class="form-control" name="category" id="product-categories" data-choices data-choices-groups data-placeholder="Select Categories">
                                                            <option value="">Choose a category</option>
                                                            <?php while ($row = $result1->fetch_assoc()) { ?>
                                                                 <option value="<?php echo htmlspecialchars($row['category_name']); ?>">
                                                                      <?php echo htmlspecialchars($row['category_name']); ?>
                                                                 </option>
                                                            <?php } ?>
                                                       </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Brand</label>
                                        <input type="text" name="brand" class="form-control" value="<?= htmlspecialchars($product['brand']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Stock</label>
                                        <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Price</label>
                                        <input type="text" name="price" class="form-control" value="<?= $product['price']; ?>" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update Product</button>
                                    <a href="product-list.php" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center">
                            <script>document.write(new Date().getFullYear())</script> &copy; Larkon. Crafted by <a href="https://1.envato.market/techzaa" target="_blank">Techzaa</a>
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
