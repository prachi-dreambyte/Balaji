<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $id = intval($_POST['id']);
    $product_name = trim($_POST['product_name']);
    $category = trim($_POST['category']);
    $brand = trim($_POST['brand']);
    $stock = intval($_POST['stock']);
    $price = floatval($_POST['price']);

    // Validate input
    if (empty($product_name) || empty($category) || empty($brand) || $stock < 0 || $price < 0) {
        die("Invalid input. Please check your data.");
    }

    // Update query
    $sql = "UPDATE products SET product_name = ?, category = ?, brand = ?, stock = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssidi", $product_name, $category, $brand, $stock, $price, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='product-list.php';</script>";
    } else {
        echo "<script>alert('Error updating product.'); history.back();</script>";
    }

    // Close connection
    $stmt->close();
}

$conn->close();
?>
