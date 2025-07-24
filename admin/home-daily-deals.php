<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'balaji';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Daily Deal</title>
    <link href="assets/css/vendor.min.css" rel="stylesheet">
    <link href="assets/css/icons.min.css" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet">
    <script src="assets/js/config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="wrapper">
    <!-- ========== Topbar Start ========== -->
    <?php include 'header.php'; ?>

    <!-- ========== Page Content Start ========== -->
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-12">
                    <form method="post" enctype="multipart/form-data">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Product Photo</h4>
                            </div>
                            <div class="card-body">
                                <!-- File Upload Dropzone -->
                                <div class="dropzone" id="imageDropzone" data-plugin="dropzone">
                                    <div class="fallback">
                                        <input name="image" type="file" />
                                    </div>
                                    <div class="dz-message needsclick">
                                        <i class="bx bx-cloud-upload fs-48 text-primary"></i>
                                        <h3 class="mt-4">Drop your image here, or <span class="text-primary">click to browse</span></h3>
                                        <span class="text-muted fs-13">
                                            1600 x 1200 (4:3) recommended. PNG, JPG and GIF files are allowed
                                        </span>
                                    </div>
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
                                            <input type="text" name="name" id="product-name" class="form-control" placeholder="Enter product name" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="product-description" class="form-label">Description</label>
                                            <textarea name="description" id="product-description" class="form-control" rows="2" placeholder="Product description"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="product-price" class="form-label">Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" step="0.01" name="price" id="product-price" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="product-old-price" class="form-label">Old Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" step="0.01" name="old_price" id="product-old-price" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="start-time" class="form-label">Start Time</label>
                                            <input type="datetime-local" name="start_time" id="start-time" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="end-time" class="form-label">End Time</label>
                                            <input type="datetime-local" name="end_time" id="end-time" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light mb-3 rounded">
                            <div class="row justify-content-end g-2">
                                <div class="col-lg-2">
                                    <button type="submit" name="submit" class="btn btn-primary w-100">Create Deal</button>
                                </div>
                                <div class="col-lg-2">
                                    <a href="home-daily-deals.php" class="btn btn-outline-secondary w-100">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== Footer Start ========== -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <script>document.write(new Date().getFullYear())</script> &copy; Your Brand Name.
                </div>
            </div>
        </div>
    </footer>

    <!-- Vendor Javascript -->
    <script src="assets/js/vendor.js"></script>

    <!-- App Javascript -->
    <script src="assets/js/app.js"></script>

    <!-- Initialize Dropzone -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Dropzone
        Dropzone.autoDiscover = false;
        const myDropzone = new Dropzone("#imageDropzone", {
            url: "/file/post",
            maxFiles: 1,
            maxFilesize: 5, // MB
            acceptedFiles: "image/jpeg,image/png,image/gif",
            addRemoveLinks: true,
            dictDefaultMessage: "Drop image here to upload",
            dictFileTooBig: "File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.",
            dictInvalidFileType: "Invalid file type. Only JPG, PNG, GIF allowed.",
            init: function() {
                this.on("addedfile", function(file) {
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                });
            }
        });
    });
    </script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize inputs
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $_POST['price'] ?? null;
    $old_price = $_POST['old_price'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;

    $image_name = null;

    // Handle file upload
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = 'deal_' . uniqid() . '.' . $image_ext;
        $target_file = $upload_dir . $image_name;
        
        // Validate image
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($image_ext), $allowed_types)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Only JPG, PNG, and GIF files are allowed.',
                    confirmButtonText: 'OK'
                });
            </script>";
            exit();
        }
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: 'There was an error uploading your image.',
                    confirmButtonText: 'OK'
                });
            </script>";
            exit();
        }
    }

    // Insert into database
    $sql = "INSERT INTO `home_daily_deal` (name, description, image, price, old_price, is_daily_deal, start_time, end_time)
            VALUES (?, ?, ?, ?, ?, 1, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssddss", $name, $description, $image_name, $price, $old_price, $start_time, $end_time);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Daily deal added successfully.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'home-daily-deals.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to add daily deal: " . addslashes($stmt->error) . "',
                confirmButtonText: 'OK'
            });
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>