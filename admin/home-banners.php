<?php
session_start();
include 'db_connect.php';

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) {
    header("Location: auth-signin.php");
    exit;
}

// Check if we're editing an existing banner
$editMode = false;
$bannerData = null;

if (isset($_GET['id'])) {
    $editMode = true;
    $bannerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    $stmt = $conn->prepare("SELECT * FROM home_banners WHERE id = ?");
    $stmt->bind_param("i", $bannerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $bannerData = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    // Validate inputs
    $caption = filter_input(INPUT_POST, 'caption', FILTER_SANITIZE_STRING);
    $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_URL);
    $bannerId = isset($_POST['banner_id']) ? filter_input(INPUT_POST, 'banner_id', FILTER_VALIDATE_INT) : null;

    // Image upload handling
    $imagePath = isset($bannerData['image']) ? $bannerData['image'] : null;
    
    if (!empty($_FILES['banner_image']['name'])) {
        $uploadDir = "uploads/banners/";
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        $imageName = $_FILES['banner_image']['name'];
        $imageTmp = $_FILES['banner_image']['tmp_name'];
        $imageType = $_FILES['banner_image']['type'];
        $imageSize = $_FILES['banner_image']['size'];
        $imageError = $_FILES['banner_image']['error'];
        
        // Validate file
        if ($imageError === UPLOAD_ERR_OK) {
            if (in_array($imageType, $allowedTypes) && $imageSize <= $maxSize) {
                // Generate unique filename
                $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
                $newFilename = uniqid('banner_', true) . '.' . $imageExt;
                $targetPath = $uploadDir . $newFilename;
                
                if (move_uploaded_file($imageTmp, $targetPath)) {
                    // Delete old image if it exists
                    if ($imagePath && file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $imagePath = $targetPath;
                }
            }
        }
    }

    if (isset($_POST['add_banner'])) {
        // Insert new banner
        $stmt = $conn->prepare("INSERT INTO home_banners (image, caption, link) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $imagePath, $caption, $link);
        $successMessage = "Banner added successfully!";
    } elseif (isset($_POST['update_banner']) && $bannerId) {
        // Update existing banner
        $stmt = $conn->prepare("UPDATE home_banners SET image = ?, caption = ?, link = ? WHERE id = ?");
        $stmt->bind_param("sssi", $imagePath, $caption, $link, $bannerId);
        $successMessage = "Banner updated successfully!";
    }

    if (isset($stmt)) {
        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '$successMessage',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'home-banner-list.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Operation failed. Please try again!',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?php echo $editMode ? 'Edit' : 'Add'; ?> Home Banner | Larkon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
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
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <?php if ($editMode): ?>
                            <input type="hidden" name="banner_id" value="<?php echo $bannerData['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="col-xl-12 col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Banner Image</h4>
                                </div>
                                <div class="card-body">
                                    <div class="dropzone" id="bannerDropzone">
                                        <div class="fallback">
                                            <input name="banner_image" type="file" accept="image/*" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="bx bx-cloud-upload fs-48 text-primary"></i>
                                            <h3 class="mt-4">Drop your banner image here, or <span class="text-primary">click to browse</span></h3>
                                            <span class="text-muted fs-13">
                                                1920 x 800 recommended. JPG, PNG or WEBP files allowed (Max 2MB)
                                            </span>
                                        </div>
                                        <?php if ($editMode && !empty($bannerData['image'])): ?>
                                            <div class="mt-3">
                                                <h5>Current Image:</h5>
                                                <img src="<?php echo $bannerData['image']; ?>" class="img-thumbnail" style="max-height: 150px;">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Banner Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="banner-caption" class="form-label">Caption</label>
                                                <input type="text" name="caption" id="banner-caption" class="form-control" 
                                                    value="<?php echo $editMode ? htmlspecialchars($bannerData['caption']) : ''; ?>" 
                                                    placeholder="Enter banner caption">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="banner-link" class="form-label">Link URL</label>
                                                <input type="url" name="link" id="banner-link" class="form-control" 
                                                    value="<?php echo $editMode ? htmlspecialchars($bannerData['link']) : ''; ?>" 
                                                    placeholder="https://example.com">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- <div class="p-3 bg-light mb-3 rounded">
                                <div class="row justify-content-end g-2">
                                    <div class="col-lg-2">
                                        <?php if ($editMode): ?>
                                            <button type="submit" name="update_banner" class="btn btn-warning w-100">Update Banner</button>
                                        <?php else: ?>
                                            <button type="submit" name="add_banner" class="btn btn-outline-secondary w-100">Save Banner</button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="home-banner-list.php" class="btn btn-primary w-100">Cancel</a>
                                    </div>
                                </div>
                            </div> -->

                            <div class="p-3 bg-light mb-3 rounded">
    <div class="row justify-content-end g-2">
        <?php if (true || $editMode): // Temporary force show for testing ?>
            <div class="col-lg-2">
                <button type="submit" name="update_banner" class="btn btn-warning w-100">Update Banner</button>
            </div>
        <?php endif; ?>
        <div class="col-lg-2">
            <button type="submit" name="add_banner" class="btn btn-outline-secondary w-100">Save Banner</button>
        </div>
        <div class="col-lg-2">
            <a href="home-banner-list.php" class="btn btn-primary w-100">Cancel</a>
        </div>
    </div>
</div>



                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Direct footer HTML instead of include -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> &copy; Larkon. All rights reserved.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/dropzone@5/dist/dropzone.js"></script>
    
    <script>
        // Initialize Dropzone
        Dropzone.autoDiscover = false;
        document.addEventListener('DOMContentLoaded', function() {
            new Dropzone("#bannerDropzone", {
                url: "/file/post",
                maxFiles: 1,
                maxFilesize: 2,
                acceptedFiles: "image/jpeg,image/png,image/webp",
                addRemoveLinks: true,
                dictDefaultMessage: "Drop banner image here or click to upload",
                dictFileTooBig: "File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.",
                dictInvalidFileType: "Invalid file type. Only JPG, PNG, WEBP allowed.",
                init: function() {
                    this.on("addedfile", function(file) {
                        if (this.files.length > 1) {
                            this.removeFile(this.files[0]);
                        }
                    });
                    
                    <?php if ($editMode && !empty($bannerData['image'])): ?>
                    // Display existing image in dropzone
                    var mockFile = { name: "Existing Image", size: 0, type: "image/jpeg" };
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, "<?php echo $bannerData['image']; ?>");
                    this.emit("complete", mockFile);
                    <?php endif; ?>
                }
            });
        });
    </script>
</body>
</html>