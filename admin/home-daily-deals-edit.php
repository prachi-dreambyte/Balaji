<?php
include "./db_connect.php";

if (!isset($_GET['id'])) {
    die("No product ID provided.");
}

$id = intval($_GET['id']);

// Fetch existing product data
$stmt = $conn->prepare("SELECT * FROM home_daily_deal WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $old_price = $_POST['old_price'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;

    // Image handling
    $image = $product['images']; 
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = '../uploads/' . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            // Delete old image if exists
            if (!empty($product['images']) && file_exists('../uploads/' . $product['images'])) {
                unlink('../uploads/' . $product['images']);
            }
            $image = $image_name;
        }
    }

    // Update product
    $stmt = $conn->prepare("UPDATE home_daily_deal SET product_name = ?, description = ?, price = ?, old_price = ?, deal_start = ?, deal_end = ?, images = ? WHERE id = ?");
    $stmt->bind_param("ssddsssi", $name, $description, $price, $old_price, $start_time, $end_time, $image, $id);
    
    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Product updated successfully',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href='home-daily-deals.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Error updating product: " . addslashes($conn->error) . "'
            });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Daily Deal</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --success: #4cc9f0;
            --danger: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .card-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #495057;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: var(--border-radius);
            transition: var(--transition);
            background-color: var(--light);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
            outline: none;
            background-color: white;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .img-preview {
            max-width: 200px;
            height: auto;
            border-radius: var(--border-radius);
            margin-top: 1rem;
            box-shadow: var(--box-shadow);
            display: block;
            border: 2px dashed #dee2e6;
            padding: 0.5rem;
        }
        
        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-upload-btn {
            width: 100%;
            padding: 0.75rem;
            background: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .file-upload-btn:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        
        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .price-container {
            display: flex;
            gap: 1rem;
        }
        
        .price-container .form-group {
            flex: 1;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .card-header h2 {
                font-size: 1.5rem;
            }
            
            .price-container {
                flex-direction: column;
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-edit me-2"></i>Edit Daily Deal Product</h2>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="editProductForm">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag me-2"></i>Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($product['product_name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left me-2"></i>Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    
                    <div class="price-container">
                        <div class="form-group">
                            <label for="price"><i class="fas fa-tag me-2"></i>Current Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                   value="<?= htmlspecialchars($product['price']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="old_price"><i class="fas fa-tag me-2"></i>Original Price</label>
                            <input type="number" step="0.01" class="form-control" id="old_price" name="old_price" 
                                   value="<?= htmlspecialchars($product['old_price']) ?>">
                        </div>
                    </div>
                    
                    <div class="price-container">
                        <div class="form-group">
                            <label for="start_time"><i class="far fa-clock me-2"></i>Deal Start Time</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" 
                                   value="<?= date('Y-m-d\TH:i', strtotime($product['deal_start'])) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="end_time"><i class="far fa-clock me-2"></i>Deal End Time</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time" 
                                   value="<?= date('Y-m-d\TH:i', strtotime($product['deal_end'])) ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-image me-2"></i>Product Image</label>
                        <div class="file-upload">
                            <div class="file-upload-btn">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                Choose or drag & drop an image
                                <input type="file" class="file-upload-input" name="image" id="image" accept="image/*">
                            </div>
                        </div>
                        <?php if (!empty($product['images'])): ?>
                            <div class="mt-3">
                                <p class="mb-2">Current Image:</p>
                                <img src="../uploads/<?= htmlspecialchars($product['images']) ?>" 
                                     alt="Current Product Image" 
                                     class="img-preview"
                                     id="currentImage">
                            </div>
                        <?php endif; ?>
                        <div id="imagePreview" class="mt-3" style="display:none;">
                            <p class="mb-2">New Image Preview:</p>
                            <img id="previewImage" class="img-preview">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save me-2"></i>Update Product
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('previewImage').src = event.target.result;
                    
                    // Hide current image if exists
                    const currentImage = document.getElementById('currentImage');
                    if (currentImage) {
                        currentImage.style.display = 'none';
                    }
                }
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            const price = parseFloat(document.getElementById('price').value);
            const oldPrice = parseFloat(document.getElementById('old_price').value) || 0;
            
            if (oldPrice > 0 && price >= oldPrice) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Prices',
                    text: 'Current price must be less than original price for a deal',
                });
            }
            
            const startTime = new Date(document.getElementById('start_time').value);
            const endTime = new Date(document.getElementById('end_time').value);
            
            if (startTime && endTime && startTime >= endTime) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time Range',
                    text: 'End time must be after start time',
                });
            }
        });
    </script>
</body>
</html>