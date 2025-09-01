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
    $use_case = $_POST['use_case'];

    $tag_number = $_POST['tag_number'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $corporate_discount = $_POST['corporate_discount'];
    $tax = $_POST['tax'];

    // Always use the same upload directory
    $uploadDir = "uploads/products/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Fetch existing product
    $sql = "SELECT images FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    $currentImages = json_decode($product['images'], true) ?: [];
    $imgs = isset($_POST['existing_images']) ? $_POST['existing_images'] : [];

    // 1. Remove images (by path)
    if (!empty($_POST['remove_image'])) {
        foreach ($_POST['remove_image'] as $remove_path) {
            $imgs = array_filter($imgs, function($img) use ($remove_path) {
                return $img !== $remove_path;
            });
        }
        $imgs = array_values($imgs);
    }

    // 2. Replace images
    if (!empty($_FILES['replace_image']['name'])) {
        foreach ($_FILES['replace_image']['name'] as $index => $fileName) {
            if ($fileName && isset($imgs[$index])) {
                $target_file = $uploadDir . time() . "_" . basename($fileName);
                if (move_uploaded_file($_FILES['replace_image']['tmp_name'][$index], $target_file)) {
                    $imgs[$index] = $target_file;
                }
            }
        }
    }

    // 3. Reorder images (by path)
    if (!empty($_POST['image_order'])) {
        $order = explode(',', $_POST['image_order']); // contains file paths
        $new_imgs = [];
        foreach ($order as $path) {
            if (in_array($path, $imgs)) {
                $new_imgs[] = $path;
            }
        }
        // Add any missing ones
        foreach ($imgs as $img) {
            if (!in_array($img, $new_imgs)) {
                $new_imgs[] = $img;
            }
        }
        $imgs = $new_imgs;
    }

    // 4. Add/replace all images
    if (isset($_POST['image_option'])) {
        if ($_POST['image_option'] == 'replace') {
            $imgs = [];
        }
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $fileName) {
                if ($fileName) {
                    $target_file = $uploadDir . time() . "_" . basename($fileName);
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                        $imgs[] = $target_file;
                    }
                }
            }
        }
    }

    $imagesJSON = json_encode($imgs);

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
        short_description = ?, description = ?,use_case =?, tag_number = ?, stock = ?, tags = ?,
        price = ?, discount = ?, corporate_discount = ?, tax = ?, images = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssssssssssssssi",
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
        $use_case,
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