<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $id = intval($_POST['id']);
    $coupon_name = trim($_POST['coupon-name']);
    $discount = floatval($_POST['coupon-discount']);
    $coupon_code = trim($_POST['coupon-code']);
    $expiration_date = $_POST['expiration_date'];
    $usage_limit = intval($_POST['usage_limit']);
    $status = trim($_POST['status']);

    // Basic validation
    if (empty($coupon_name) || empty($coupon_code) || $discount <= 0 || $usage_limit < 0 || empty($expiration_date) || !in_array($status, ['active', 'inactive'])) {
        die("Invalid input. Please check your data.");
    }

    // Prepare and execute the update statement
    $sql = "UPDATE coupons SET coupon_name = ?,coupon_code = ?, discount = ?,  expiration_date = ?, usage_limit = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsisi", $coupon_name, $coupon_code, $discount,  $expiration_date, $usage_limit, $status, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Coupon updated successfully!'); window.location.href='coupon-list.php';</script>";
    } else {
        echo "<script>alert('Error updating coupon.'); history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
