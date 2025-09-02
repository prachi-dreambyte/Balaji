<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id']))  {
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

// Fetch products for variants
try {
    $product_stmt = $conn->prepare('SELECT id, product_name FROM products');
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
} catch (Exception $e) {
    error_log("Products not found: " . $e->getMessage());
}
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
                    <div class="col-xl-12 col-lg-12 mx-auto">
                        <form action="update_product.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $product['id']; ?>">
                            
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Product Photo</h4>
                                </div>
                                <div class="card-body">
                                    <!-- Current Images with Replace/Remove -->
                                  <div class="mb-3">
    <label class="form-label">Manage Product Images</label><br>
    <div class="d-flex flex-wrap">
        <?php
                                        $imgs = [];
                                        if (!empty($product['images'])) {
                                            $imgs = json_decode($product['images'], true);
                                            if (is_array($imgs)) {
                                                foreach ($imgs as $index => $img) { ?>
                                                    <div style="margin:10px;text-align:center;">
                                                        <!-- Preview -->
                                                        <img src="<?= htmlspecialchars($img); ?>" alt="" width="100"
                                                            style="border:1px solid #ccc;border-radius:5px;"><br>
                                
                                                        <!-- Replace image -->
                                                        <input type="file" name="replace_image[<?= $index; ?>]" class="form-control mt-1">
                                
                                                        <!-- Remove checkbox (✅ now sends image path instead of index) -->
                                                        <div class="form-check mt-1">
                                                            <input class="form-check-input" type="checkbox" name="remove_image[]"
                                                                value="<?= htmlspecialchars($img); ?>" id="remove_<?= $index; ?>">
                                                            <label class="form-check-label" for="remove_<?= $index; ?>">Remove</label>
                                                        </div>
                                
                                                        <!-- Hidden field for existing image -->
                                                        <input type="hidden" name="existing_images[]" value="<?= htmlspecialchars($img); ?>">
                                                    </div>
                                                <?php }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>


                                    <!-- Image Reorder (Drag & Drop) -->
                                    <div class="mb-3">
                                        <label class="form-label">Reorder Images</label>
                                        <ul id="sortable-images" class="list-unstyled d-flex flex-wrap">
                                            <?php if (!empty($imgs)) {
                                                foreach ($imgs as $index => $img) { ?>
                                                    <li class="sortable-item me-2 mb-2" data-index="<?= $index; ?>" style="cursor:move;">
                                                        <img src="<?= $img; ?>" width="80" style="border:1px solid #aaa;border-radius:5px;">
                                                    </li>
                                            <?php }
                                            } ?>
                                        </ul>
                                        <input type="hidden" name="image_order" id="image_order">
                                    </div>
                                    
                                    <!-- Image Upload Options -->
                                    <div class="mb-3">
                                        <label class="form-label">Image Upload Options</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="image_option" id="add_images" value="add" checked>
                                            <label class="form-check-label" for="add_images">
                                                Add to existing images
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="image_option" id="replace_images" value="replace">
                                            <label class="form-check-label" for="replace_images">
                                                Replace all existing images
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- File Upload -->
                                    <div class="mb-3">
                                        <label class="form-label">Upload Images</label>
                                        <input type="file" name="images[]" class="form-control" multiple>
                                        <small class="text-muted">Select images to upload. Choose option above to add or replace existing images.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Product Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="product-name" class="form-label">Product Name</label>
                                                <input type="text" name="product_name" id="product-name" class="form-control" 
                                                    value="<?= htmlspecialchars($product['product_name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="product-categories" class="form-label">Product Categories</label>
                                            <select class="form-control" name="category" id="product-categories" required>
                                                <option value="">Choose a category</option>
                                                <?php while ($row = $result1->fetch_assoc()) { ?>
                                                        <option value="<?= htmlspecialchars($row['category_name']); ?>"
                                                            <?= ($row['category_name'] == $product['category']) ? 'selected' : ''; ?>>
                                                            <?= htmlspecialchars($row['category_name']); ?>
                                                        </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="product-brand" class="form-label">Brand</label>
                                                <input type="text" id="product-brand" name="brand" class="form-control" 
                                                    value="<?= htmlspecialchars($product['brand']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="product-weight" class="form-label">Product Weight</label>
                                                <input type="text" id="product-weight" name="product_weight" class="form-control" 
                                                    value="<?= htmlspecialchars($product['product_weight']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="product_variants" class="form-label">Variants</label>
                                                <select class="form-control" name="variants" id="product_variants">
                                                    <option value="">Choose a variant</option>
                                                    <?php
                                                    if ($product_result && $product_result->num_rows > 0) {
                                                        while ($row = $product_result->fetch_assoc()) {
                                                            $selected = ($row['id'] == $product['variants']) ? 'selected' : '';
                                                            ?>
                                                                    <option value="<?= htmlspecialchars($row['id']); ?>" <?= $selected; ?>>
                                                                        <?= htmlspecialchars($row['product_name']); ?>
                                                                    </option>
                                                            <?php }
                                                    } else { ?>
                                                            <option value="">No variants found</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="product-colour" class="form-label">Product Colour</label>
                                                <input type="text" id="product-colour" name="colour" class="form-control" 
                                                    value="<?= htmlspecialchars($product['colour']); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="hashtags" class="form-label">Hashtags</label>
                                                <input type="text" id="hashtags" name="hashtags" class="form-control" 
                                                    value="<?= htmlspecialchars($product['hashtags']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Add all the other product specification fields -->
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="size" class="form-label">Size</label>
                                                <input type="text" id="size" name="size" class="form-control" 
                                                    value="<?= htmlspecialchars($product['size']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="total-height" class="form-label">Total Height</label>
                                                <input type="text" id="total-height" name="total_height" class="form-control" 
                                                    value="<?= htmlspecialchars($product['total_height']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="total-width" class="form-label">Total Width</label>
                                                <input type="text" id="total-width" name="total_width" class="form-control" 
                                                    value="<?= htmlspecialchars($product['total_width']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Continue adding all other fields from your database table -->
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="material" class="form-label">Material</label>
                                                <input type="text" id="material" name="material" class="form-control" 
                                                    value="<?= htmlspecialchars($product['material']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="seat-height" class="form-label">Seat Height</label>
                                                <input type="text" id="seat-height" name="seat_height" class="form-control" 
                                                    value="<?= htmlspecialchars($product['seat_height']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="seat-thickness" class="form-label">Seat Thickness</label>
                                                <input type="text" id="seat-thickness" name="seat_thickness" class="form-control" 
                                                    value="<?= htmlspecialchars($product['seat_thickness']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Add more rows for all the remaining fields -->
                                    <!-- Continue this pattern for all fields in your database table -->
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="short_description" class="form-label">Short Description</label>
                                                <textarea class="form-control" name="short_description" id="short_description" rows="3"><?= htmlspecialchars($product['short_description']); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Full Description</label>
                                                <textarea class="form-control" name="description" id="description" rows="5"><?php echo isset($product['description']) ? $product['description'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
    <div class="col-lg-12">
        <div class="mb-3">
            <label for="use_case" class="form-label">Use Cases (One per line)</label>
            <textarea 
                class="form-control" 
                name="use_case" 
                id="use_case" 
                rows="5" 
                placeholder="Enter each use case on a new line"><?= htmlspecialchars($product['use_case']); ?></textarea>
            <small class="text-muted">Example: Enter one point per line. On frontend, it will auto arrange into columns.</small>
        </div>
    </div>
</div>

                                    
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="tag-number" class="form-label">Tag Number</label>
                                                <input type="text" name="tag_number" id="tag-number" class="form-control" 
                                                    value="<?= htmlspecialchars($product['tag_number']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="product-stock" class="form-label">Stock</label>
                                                <input type="number" name="stock" id="product-stock" class="form-control" 
                                                    value="<?= htmlspecialchars($product['stock']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="product-tags" class="form-label">Tags</label>
                                                <?php
                                                $currentTags = explode(',', $product['tags']);
                                                $tagOptions = ['FEATURED PRODUCTS', 'NEW ARRIVAL', 'ONSALE', 'BESTSELLER'];
                                                ?>
                                                <select class="form-control" name="tags[]" id="product-tags" multiple>
                                                    <?php foreach ($tagOptions as $tag) {
                                                        $selected = in_array($tag, $currentTags) ? 'selected' : '';
                                                        ?>
                                                            <option value="<?= $tag; ?>" <?= $selected; ?>><?= $tag; ?></option>
                                                    <?php } ?>
                                                    <option value="NO TAG" <?= (in_array('NO TAG', $currentTags) || empty($product['tags'])) ? 'selected' : ''; ?>>No Tag</option>
                                                </select>
                                                <small class="text-muted">Select "No Tag" if you don't want any tags for this product. You can select multiple tags or just "No Tag".</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Pricing Details</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label for="product-price" class="form-label">Price</label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" step="0.01" id="product-price" name="price" class="form-control" 
                                                    value="<?= $product['price']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="product-discount" class="form-label">Discount (%)</label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">%</span>
                                                <input type="number" step="0.01" id="product-discount" class="form-control" name="discount" 
                                                    value="<?= $product['discount']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="corporate-discount" class="form-label">Corporate Discount (%)</label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" step="0.01" id="corporate-discount" class="form-control" name="corporate_discount" 
                                                    value="<?= $product['corporate_discount']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="product-tax" class="form-label">Tax (₹)</label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" step="0.01" name="tax" id="product-tax" class="form-control" 
                                                    value="<?= $product['tax']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-3 bg-light mb-3 rounded">
                                <div class="row justify-content-end g-2">
                                    <div class="col-lg-2">
                                        <button type="submit" name="update" class="btn btn-primary w-100">Update Product</button>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="product-list.php" class="btn btn-outline-secondary w-100">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
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
    
    <script>
        // Handle image upload options
        document.addEventListener('DOMContentLoaded', function() {
            const addImagesRadio = document.getElementById('add_images');
            const replaceImagesRadio = document.getElementById('replace_images');
            const fileInput = document.querySelector('input[name="images[]"]');
            const fileInputContainer = fileInput.parentElement;
            const form = document.querySelector('form');
            
            function updateFileInputLabel() {
                const smallText = fileInputContainer.querySelector('small');
                if (addImagesRadio.checked) {
                    smallText.textContent = 'Select images to upload. Images will be added to existing ones.';
                } else {
                    smallText.textContent = 'Select images to upload. All existing images will be replaced.';
                }
            }
            
            addImagesRadio.addEventListener('change', updateFileInputLabel);
            replaceImagesRadio.addEventListener('change', updateFileInputLabel);
            
            // Form validation
            form.addEventListener('submit', function(e) {
                if (replaceImagesRadio.checked && (!fileInput.files || fileInput.files.length === 0)) {
                    e.preventDefault();
                    alert('Please select at least one image when choosing to replace existing images.');
                    return false;
                }
            });
            
            // Handle tags selection
            const tagsSelect = document.getElementById('product-tags');
            tagsSelect.addEventListener('change', function() {
                const selectedOptions = Array.from(this.selectedOptions).map(option => option.value);
                const noTagOption = this.querySelector('option[value="NO TAG"]');
                
                if (selectedOptions.includes('NO TAG')) {
                    // If "No Tag" is selected, unselect all other options
                    Array.from(this.options).forEach(option => {
                        if (option.value !== 'NO TAG') {
                            option.selected = false;
                        }
                    });
                } else if (selectedOptions.length > 0) {
                    // If other tags are selected, unselect "No Tag"
                    noTagOption.selected = false;
                }
            });
            
            // Initialize label
            updateFileInputLabel();
        });
    </script>
</body>

</html>