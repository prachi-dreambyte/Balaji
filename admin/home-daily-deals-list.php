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

// Fetch categories for dropdown
$categories = $conn->query("SELECT * FROM categories");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_deal'])) {
    // Basic validation
    $errors = [];
    
    if (empty($_POST['name'])) {
        $errors[] = "Product name is required";
    }
    
    if (empty($_POST['price'])) {
        $errors[] = "Price is required";
    }
    
    if (empty($_POST['old_price'])) {
        $errors[] = "Old price is required";
    }
    
    if (empty($_POST['deal_start']) || empty($_POST['deal_end'])) {
        $errors[] = "Deal period is required";
    }
    
    // Image upload handling
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . uniqid() . "_" . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $errors[] = "File is not an image.";
        }
        
        // Check file size (5MB max)
        if ($_FILES["image"]["size"] > 5000000) {
            $errors[] = "Sorry, your file is too large (max 5MB).";
        }
        
        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    } else {
        $errors[] = "Product image is required";
    }
    
    if (empty($errors)) {
        // Move uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO home_daily_deal (
                name, 
                category_id,
                brand,
                weight,
                description,
                price, 
                old_price, 
                image, 
                start_time, 
                end_time,
                is_daily_deal,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");
            
            $stmt->bind_param(
                "sisssddsss",
                $_POST['name'],
                $_POST['category'],
                $_POST['brand'],
                $_POST['weight'],
                $_POST['description'],
                $_POST['price'],
                $_POST['old_price'],
                $targetFile,
                $_POST['deal_start'],
                $_POST['deal_end']
            );
            
            if ($stmt->execute()) {
                $success = "Daily deal product added successfully!";
            } else {
                $errors[] = "Error adding product: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Sorry, there was an error uploading your file.";
        }
    }
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
                <?php if (isset($_GET['action']) && $_GET['action'] == 'add'): ?>
                <!-- Add New Deal Form -->
                <div class="row">
                    <form method="post" enctype="multipart/form-data">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add Daily Deal Product</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger">
                                        <ul>
                                            <?php foreach ($errors as $error): ?>
                                            <li><?php echo $error; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($success)): ?>
                                    <div class="alert alert-success">
                                        <?php echo $success; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="product-name" class="form-label">Product Name</label>
                                                <input type="text" name="name" id="product-name" class="form-control" placeholder="Product Name" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="product-categories" class="form-label">Product Category</label>
                                            <select class="form-control" name="category" id="product-categories" required>
                                                <option value="">Choose a category</option>
                                                <?php while ($row = $categories->fetch_assoc()): ?>
                                                    <option value="<?php echo htmlspecialchars($row['id']); ?>">
                                                        <?php echo htmlspecialchars($row['category_name']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="product-brand" class="form-label">Brand</label>
                                                <input type="text" id="product-brand" name="brand" class="form-control" placeholder="Brand Name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="product-weight" class="form-label">Weight</label>
                                                <input type="text" id="product-weight" name="weight" class="form-control" placeholder="In gm & kg">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control bg-light-subtle" name="description" id="description" rows="7" placeholder="Product description"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="product-price" class="form-label">Deal Price</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text fs-20"><i class='bx bx-dollar'></i></span>
                                                    <input type="number" id="product-price" name="price" class="form-control" placeholder="000" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="product-old-price" class="form-label">Original Price</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text fs-20"><i class='bx bx-dollar'></i></span>
                                                    <input type="number" id="product-old-price" name="old_price" class="form-control" placeholder="000" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="start-date" class="form-label">Deal Start Date</label>
                                                <input type="datetime-local" id="start-date" name="deal_start" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="end-date" class="form-label">Deal End Date</label>
                                                <input type="datetime-local" id="end-date" name="deal_end" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="product-image" class="form-label">Product Image</label>
                                                <input type="file" id="product-image" name="image" class="form-control" accept="image/*" required>
                                                <small class="text-muted">Recommended size: 800x800px. JPG, PNG or GIF.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-3 bg-light mb-3 rounded">
                                <div class="row justify-content-end g-2">
                                    <div class="col-lg-2">
                                        <button type="submit" name="add_deal" class="btn btn-primary w-100">Save Deal</button>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="home-daily-deals.php" class="btn btn-outline-secondary w-100">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <!-- Daily Deals Listing -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Daily Deal Products</h4>
                                <a href="home-daily-deals.php?action=add" class="btn btn-primary btn-sm float-end">
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
                                                <th>Discount</th>
                                                <th>Deal Period</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM `home_daily_deal` ORDER BY created_at DESC";
                                            $result = $conn->query($sql);
                                            $count = 1;

                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $current_time = time();
                                                    $start_time = strtotime($row['deal_start']);
                                                    $end_time = strtotime($row['deal_end']);
                                                    
                                                    $status = "";
                                                    if ($current_time < $start_time) {
                                                        $status = "<span class='badge bg-warning'>Upcoming</span>";
                                                    } elseif ($current_time > $end_time) {
                                                        $status = "<span class='badge bg-secondary'>Expired</span>";
                                                    } else {
                                                        $status = "<span class='badge bg-success'>Active</span>";
                                                    }
                                                    
                                                    // Calculate discount percentage
                                                    $discount = 0;
                                                    if ($row['old_price'] > 0) {
                                                        $discount = round((($row['old_price'] - $row['price']) / $row['old_price']) * 100);
                                                    }
                                                    
                                                    echo "<tr>";
                                                    echo "<td>" . $count++ . "</td>";
                                                   
                                                    $firstImage = json_decode($row['images'], true);
                                                    $first = $firstImage[0];
                                                    
                                                    echo "<td><img src='" . htmlspecialchars($first) . "' class='img-thumbnail' width='80'></td>";
                                                    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                                                    echo "<td>₹" . number_format($row['price'], 2) . "</td>";
                                                    echo "<td>₹" . number_format($row['old_price'], 2) . "</td>";
                                                    echo "<td>" . $discount . "%</td>";
                                                    echo "<td>" . date('d M Y H:i', $start_time) . " - " . date('d M Y H:i', $end_time) . "</td>";
                                                    echo "<td>" . $status . "</td>";
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
                                                echo "<tr><td colspan='9' class='text-center'>No daily deal products found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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
        // Delete confirmation
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
        
        // Set default dates for deal period
        const now = new Date();
        const startDate = new Date(now);
        startDate.setHours(now.getHours() + 1);
        
        const endDate = new Date(now);
        endDate.setDate(now.getDate() + 7);
        
        document.getElementById('start-date').value = startDate.toISOString().slice(0, 16);
        document.getElementById('end-date').value = endDate.toISOString().slice(0, 16);
    });
    </script>
</body>
</html>