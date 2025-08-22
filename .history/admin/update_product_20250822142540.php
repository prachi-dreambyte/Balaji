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
    $tags = isset($_POST['tags']) ? implode(',', $_POST['tags']) : '';
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


    if (!empty($_FILES['images']['name'][0])) {
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

    // Update query with all fields
    $sql = "UPDATE products SET 
        product_name = ?, category = ?, brand = ?, product_weight = ?, variants = ?, colour = ?,hashtags=? ,
        size = ?, total_height = ?, total_width = ?, material = ?, seat_height = ?, seat_thickness = ?,
        short_description = ?, description = ?, tag_number = ?, stock = ?, tags = ?,
        price = ?, discount = ?, corporate_discount = ?, tax = ?, images = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssssssssssssi",
        $product_name,
        $category,
        $brand,
        $product_weight,
        $variants,
        $colour,
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

    $stmt->close();
    $conn->close();

    header("Location: product-list.php");
    exit;
}
?>