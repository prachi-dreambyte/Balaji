<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 } 
// Check if 'id' is passed in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the delete query
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to product list after deletion
        header("Location: product-list.php?deleted=1");
        exit();
    } else {
        echo "Error deleting product: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid product ID.";
}

$conn->close();
?>
