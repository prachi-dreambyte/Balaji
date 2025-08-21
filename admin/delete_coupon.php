<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 } 

if (isset($_GET['id'])) {
    $coupon_id = intval($_GET['id']);

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $coupon_id);

    if ($stmt->execute()) {
        // Redirect back to the coupon list with a success message
        header("Location: coupon-list.php?message=Coupon deleted successfully");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: coupon-list.php?error=Error deleting coupon");
        exit();
    }

    $stmt->close();
} else {
    // Redirect if no ID is provided
    header("Location: coupon-list.php?error=No coupon ID provided");
    exit();
}

$conn->close();
?>
