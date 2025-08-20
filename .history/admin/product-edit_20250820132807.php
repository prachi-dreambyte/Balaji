<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth-signin.php");
    exit;
}

// Fetch categories
$stmt = $conn->prepare('SELECT * FROM categories');
$stmt->execute();
$result1 = $stmt->get_result();
$stmt->close();

// Get product ID
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
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Edit Product | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Vendor CSS -->
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/config.js"></script>
</head>

<body>
    <div class="wrapper">
        <?php include 'header.php'; ?>

        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-xl-10 col-lg-12 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Product</h4>
                            </div>
                            <div class="card-body">
                                <form action="update_product.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= $product['id']; ?>">

                                    <!-- Basic Info -->
                                    <div class="mb-3">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" name="product_name" class="form-control"
                                            value="<?= htmlspecialchars($product['product_name']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="product-categories" class="form-label">Category</label>
                                        <select class="form-control" name="category" id="product-categories">
                                            <option value="">Choose a category</option>
                                            <?php while ($row = $result1->fetch_assoc()) { ?>
                                                <option value="<?= htmlspecialchars($row['category_name']); ?>"
                                                    <?= ($row['category_name'] == $product['category']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($row['category_name']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Brand</label>
                                        <input type="text" name="brand" class="form-control"
                                            value="<?= htmlspecialchars($product['brand']); ?>">
                                    </div>

                                    <!-- Pricing -->
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Price</label>
                                            <input type="number" step="0.01" name="price" class="form-control"
                                                value="<?= $product['price']; ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Discount (%)</label>
                                            <input type="number" step="0.01" name="discount" class="form-control"
                                                value="<?= $product['discount']; ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Corporate Discount</label>
                                            <input type="text" name="corporate_discount" class="form-control"
                                                value="<?= htmlspecialchars($product['corporate_discount']); ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Tax (%)</label>
                                            <input type="number" step="0.01" name="tax" class="form-control"
                                                value="<?= $product['tax']; ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Stock</label>
                                            <input type="number" name="stock" class="form-control"
                                                value="<?= $product['stock']; ?>">
                                        </div>
                                    </div>

                                    <!-- Descriptions -->
                                    <div class="mb-3">
                                        <label class="form-label">Short Description</label>
                                        <input type="text" name="short_description" class="form-control"
                                            value="<?= htmlspecialchars($product['short_description']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Full Description</label>
                                        <textarea name="description" class="form-control"
                                            rows="5"><?= htmlspecialchars($product['description']); ?></textarea>
                                    </div>

                                    <!-- Tags -->
                                    <div class="mb-3">
                                        <label class="form-label">Tags</label>
                                        <textarea name="tags" class="form-control"
                                            rows="2"><?= htmlspecialchars($product['tags']); ?></textarea>
                                    </div>

                                    <!-- Images -->
                                    <div class="mb-3">
                                        <label class="form-label">Product Images</label><br>
                                        <?php if (!empty($product['images'])): ?>
                                            <?php
                                            $imgs = explode(',', $product['images']);
                                            foreach ($imgs as $img) {
                                                echo "<img src='uploads/$img' alt='' width='80' style='margin:5px;border:1px solid #ccc;border-radius:5px;'>";
                                            }
                                            ?>
                                        <?php endif; ?>
                                        <input type="file" name="images[]" class="form-control" multiple>
                                        <small class="text-muted">Upload new images to replace/add.</small>
                                    </div>

                                    <!-- Color Options -->
                                    <div class="mb-3">
                                        <label class="form-label">Available Colours</label>
                                        <textarea name="colour" class="form-control"
                                            rows="2"><?= htmlspecialchars($product['colour']); ?></textarea>
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
                            <script>document.write(new Date().getFullYear())</script> &copy; Admin Panel
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