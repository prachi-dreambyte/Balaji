<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth-signin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']);
    $Main_Product_Colour = trim($_POST['Main_Product_Colour']);
    $color = trim($_POST['color']);
    $stock = intval($_POST['stock']);
    $price = floatval($_POST['price']);
    $discount = floatval($_POST['discount']);
    $corporate_discount = floatval($_POST['corporate_discount']);
    $tax = floatval($_POST['tax']);

    // Handle image upload
    $image_path = '';
    if (!empty($_FILES['variant_image']['name'])) {
        $upload_dir = 'uploads/variants/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['variant_image']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['variant_image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO variants (product_id, Main_Product_Colour, color, image, price, discount, corporate_discount, tax, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssddddi", $product_id,$Main_Product_Colour, $color, $image_path, $price, $discount, $corporate_discount, $tax, $stock);

    if ($stmt->execute()) {
        header("Location: product-list.php?success=Variant added successfully");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>