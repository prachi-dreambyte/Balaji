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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --light-bg: #f8f9fc;
            --dark-bg: #2e59d9;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(90deg, var(--dark-bg) 0%, var(--primary-color) 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
            border: none;
        }
        
        .card-header {
            background: linear-gradient(180deg, #fff 0%, #f8f9fc 100%);
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
            padding: 15px 20px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #3a5fc8;
            border-color: #3a5fc8;
        }
        
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .image-preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        footer {
            background-color: #fff;
            padding: 15px 0;
            margin-top: 30px;
            border-top: 1px solid #e3e6f0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-couch me-2"></i>Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-home me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-box me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-tags me-1"></i> Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user me-1"></i> Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 mb-0">Edit Product</h2>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>

        <form id="editProductForm">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Information Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="productName" value="Ergonomic Office Chair" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productCategory" class="form-label">Category</label>
                                    <select class="form-select" id="productCategory">
                                        <option value="">Select Category</option>
                                        <option value="chairs" selected>Office Chairs</option>
                                        <option value="desks">Desks</option>
                                        <option value="accessories">Accessories</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productBrand" class="form-label">Brand</label>
                                    <input type="text" class="form-control" id="productBrand" value="ComfortSeat">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productWeight" class="form-label">Weight (kg)</label>
                                    <input type="number" step="0.1" class="form-control" id="productWeight" value="15.5">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="shortDescription" class="form-label">Short Description</label>
                                <textarea class="form-control" id="shortDescription" rows="2">Premium ergonomic office chair with lumbar support</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="fullDescription" class="form-label">Full Description</label>
                                <textarea class="form-control" id="fullDescription" rows="4">This premium ergonomic office chair is designed for maximum comfort during long working hours. Features adjustable lumbar support, headrest, and armrests. Made with high-quality materials for durability.</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Pricing & Inventory</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="productPrice" class="form-label">Price ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="productPrice" value="299.99">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="productDiscount" class="form-label">Discount (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="productDiscount" value="10">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="corporateDiscount" class="form-label">Corporate Discount</label>
                                    <input type="text" class="form-control" id="corporateDiscount" value="15% for 5+ items">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="productTax" class="form-label">Tax (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="productTax" value="8.5">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="productStock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="productStock" value="45">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="productTags" class="form-label">Tags</label>
                                    <input type="text" class="form-control" id="productTags" value="ergonomic, office, chair, comfortable">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Images Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Product Images</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Current Images</label>
                                <div class="image-preview">
                                    <div class="image-preview-item">
                                        <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&q=80" alt="Product image">
                                        <div class="remove-image">
                                            <i class="fas fa-times text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="image-preview-item">
                                        <img src="https://images.unsplash.com/photo-1598301257982-0cf01499abb2?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&q=80" alt="Product image">
                                        <div class="remove-image">
                                            <i class="fas fa-times text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="imageUpload" class="form-label">Add New Images</label>
                                <input class="form-control" type="file" id="imageUpload" multiple accept="image/*">
                                <div class="form-text">Select new images to add to the existing ones.</div>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="keepExistingImages" checked>
                                <label class="form-check-label" for="keepExistingImages">
                                    Keep existing images when updating
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Colors Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Available Colors</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <textarea class="form-control" id="productColors" rows="3">Black, Gray, Blue, Red</textarea>
                                <div class="form-text">Enter available colors separated by commas.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save me-1"></i> Update Product
                            </button>
                            <a href="#" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; 2023 Admin Panel. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editProductForm = document.getElementById('editProductForm');
            
            editProductForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Simulate form submission
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
                submitBtn.disabled = true;
                
                // Simulate API call
                setTimeout(() => {
                    alert('Product updated successfully!');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 1500);
            });
            
            // Add functionality to remove image buttons
            document.querySelectorAll('.remove-image').forEach(button => {
                button.addEventListener('click', function() {
                    const confirmRemove = confirm('Are you sure you want to remove this image?');
                    if (confirmRemove) {
                        this.closest('.image-preview-item').remove();
                    }
                });
            });
            
            // Show alert when trying to navigate away without saving
            window.addEventListener('beforeunload', function(e) {
                // This would normally check if form has been modified
                const message = 'You have unsaved changes. Are you sure you want to leave?';
                e.returnValue = message;
                return message;
            });
        });
    </script>
</body>
</html>