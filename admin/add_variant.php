<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id']))  {
    header("Location: auth-signin.php");
    exit;
}

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
if ($product_id <= 0) {
    die("Invalid product ID");
}

// Fetch product name
$product = $conn->query("SELECT product_name FROM products WHERE id = $product_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Variant - <?= htmlspecialchars($product['product_name']) ?></title>
    <link href="assets/css/vendor.min.css" rel="stylesheet" />
    <link href="assets/css/icons.min.css" rel="stylesheet" />
    <link href="assets/css/app.min.css" rel="stylesheet" />
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
                                <h4>Add Variant for: <?= htmlspecialchars($product['product_name']) ?></h4>
                            </div>
                            <div class="card-body">
                                <form action="save_variant.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Set Main Product Colour</label>
                                        <input type="text" name="Main_Product_Colour" class="form-control"
                                            placeholder="e.g., Red, Blue" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Variant Colour</label>
                                        <input type="text" name="color" class="form-control"
                                            placeholder="e.g., Red, Blue" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Variant Image</label>
                                        <input type="file" name="variant_image" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Stock</label>
                                        <input type="number" name="stock" class="form-control" min="0" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Price (₹)</label>
                                        <input type="number" step="0.01" name="price" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Discount Amount (₹)</label>
                                        <input type="number" step="0.01" name="discount" class="form-control" value="0">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Corporate Discount Amount (₹)</label>
                                        <input type="number" step="0.01" name="corporate_discount" class="form-control"
                                            value="0">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tax Amount (₹)</label>
                                        <input type="number" step="0.01" name="tax" class="form-control" value="0">
                                    </div>

                                    <button type="submit" class="btn btn-success">Save Variant</button>
                                    <a href="product-list.php" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           
        </div>
    </div>

    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>