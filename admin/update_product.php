<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id']))  {
    header("Location: auth-signin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $product_id = intval($_POST['id']);

    // Collect all form data
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $product_weight = $_POST['product_weight'];
    $variants = $_POST['variants'];
    $colour = $_POST['colour'];
    $hashtags = $_POST['hashtags'];
    $size = $_POST['size'];
    $total_height = $_POST['total_height'];
    $total_width = $_POST['total_width'];
    $material = $_POST['material'];
    $seat_height = $_POST['seat_height'];
    $seat_thickness = $_POST['seat_thickness'];
    // Add all other fields here...
    $short_description = $_POST['short_description'];
    $description = $_POST['description'];
    $tag_number = $_POST['tag_number'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $corporate_discount = $_POST['corporate_discount'];
    $tax = $_POST['tax'];

    // Handle image upload
    $uploadDir = "uploads/";
    // Fetch existing product first
    $sql = "SELECT images FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    $currentImages = json_decode($product['images'], true) ?: [];
    $imageOption = $_POST['image_option'] ?? 'add';

    // Handle image replacement or addition
    if (!empty($_FILES['images']['name'][0])) {
        // If replacing images, delete old images from server
        if ($imageOption === 'replace') {
            foreach ($currentImages as $oldImage) {
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
            $currentImages = []; // Clear the array for replacement
        }
        
        // Upload new images
        foreach ($_FILES['images']['name'] as $key => $imageName) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $imageTmp = $_FILES['images']['tmp_name'][$key];
                $imagePath = $uploadDir . time() . "_" . basename($imageName);

                if (move_uploaded_file($imageTmp, $imagePath)) {
                    $currentImages[] = $imagePath;
                }
            }
        }
    }

    $imagesJSON = json_encode($currentImages);

    // Handle tags - if "NO TAG" is selected, clear all tags
    $tags = '';
    if (isset($_POST['tags']) && !empty($_POST['tags'])) {
        if (in_array('NO TAG', $_POST['tags'])) {
            $tags = ''; // No tags
        } else {
            $tags = implode(',', $_POST['tags']);
        }
    }

    // Update query with all fields
    $sql = "UPDATE products SET 
        product_name = ?, category = ?, brand = ?, product_weight = ?, variants = ?, colour = ?,hashtags=? ,
        size = ?, total_height = ?, total_width = ?, material = ?, seat_height = ?, seat_thickness = ?,
        short_description = ?, description = ?, tag_number = ?, stock = ?, tags = ?,
        price = ?, discount = ?, corporate_discount = ?, tax = ?, images = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssssssssssssssssi",
        $product_name,
        $category,
        $brand,
        $product_weight,
        $variants,
        $colour,
        $hashtags,
        $size,
        $total_height,
        $total_width,
        $material,
        $seat_height,
        $seat_thickness,
        $short_description,
        $description,
        $tag_number,
        $stock,
        $tags,
        $price,
        $discount,
        $corporate_discount,
        $tax,
        $imagesJSON,
        $product_id
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Product updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating product: " . $conn->error;
    }

    // Log the image operation for debugging
    if (!empty($_FILES['images']['name'][0])) {
        if ($imageOption === 'replace') {
            error_log("Product ID $product_id: Images replaced successfully");
        } else {
            error_log("Product ID $product_id: Images added successfully");
        }
    }

    $stmt->close();
    $conn->close();

    header("Location: product-list.php");
    exit;
}
?>