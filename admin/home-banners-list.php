<?php
session_start();
include 'db_connect.php';

// Check authentication
if (!isset($_SESSION['admin_id']))  {
    header("Location: auth-signin.php");
    exit;
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle banner deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Security token mismatch";
        header("Location: home-banners.php");
        exit;
    }

    $bannerId = (int)$_POST['delete_id'];
    $imagePath = isset($_POST['image']) ? $_POST['image'] : null;

    try {
        // Validate image path
        $basePath = realpath('uploads') . DIRECTORY_SEPARATOR;
        $fullPath = realpath($imagePath);
        
        if ($fullPath && strpos($fullPath, $basePath) === 0) {
            // Start transaction
            $conn->begin_transaction();
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM home_banners WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Database error: " . $conn->error);
            }
            $stmt->bind_param("i", $bannerId);
            $stmt->execute();
            
            // Delete image file
            if (file_exists($fullPath)) {
                if (!unlink($fullPath)) {
                    throw new Exception("Could not delete image file");
                }
            }
            
            $conn->commit();
            $_SESSION['success_message'] = "Banner deleted successfully";
        } else {
            $_SESSION['error_message'] = "Invalid image path specified";
        }
    } catch (Exception $e) {
        if (isset($conn) && method_exists($conn, 'rollback')) {
            $conn->rollback();
        }
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    
    header("Location: home-banners.php");
    exit;
}

// Fetch banners
$banners = [];
$sql = "SELECT * FROM home_banners";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $banners[] = $row;
    }
    $result->free();
} else {
    $_SESSION['error_message'] = "Error fetching banners: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Home Banners | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/css/vendor.min.css" rel="stylesheet">
    <link href="assets/css/icons.min.css" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include 'header.php'; ?>

        <div class="page-content">
            <div class="container-xxl">
                <!-- Display messages -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success mb-3"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger mb-3"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <div class="row">
                    <?php foreach ($banners as $banner): ?>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="banner-image-container mb-3">
                                        <img src="<?= htmlspecialchars($banner['image']) ?>" 
                                             alt="<?= htmlspecialchars($banner['caption']) ?>" 
                                             class="img-fluid rounded">
                                    </div>
                                    <h5 class="card-title"><?= htmlspecialchars($banner['caption']) ?></h5>
                                    
                                    <!-- Delete Form -->
                                    <!-- <form method="POST" action="home-banners.php" class="delete-form"> -->
                                        <!-- <input type="hidden" name="delete_id" value="<?= $banner['id'] ?>">
                                        <input type="hidden" name="image" value="<?= htmlspecialchars($banner['image']) ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"> -->
                                        <button type="button" onclick="confirmDelete(<?php echo $banner['id']; ?>, '<?php echo $banner['image']; ?>')" class="btn btn-danger mt-2 delete-btn">
                                            Delete
                                        </button>
                                    <!-- </form> -->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

       
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
function confirmDelete(id, image) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "delete_banners.php?id=" +id + "&image=" + image;
        }
    });
}
</script>
    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>