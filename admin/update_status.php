<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["id"]) || !isset($_POST["status"])) {
        die("invalid_request");
    }

    $order_id = intval($_POST["id"]);
    $status = trim($_POST["status"]);

    error_log("Received Order ID: " . $order_id);
    error_log("Received Status: " . $status);

    if ($order_id <= 0 || empty($status)) {
        die("invalid_data");
    }

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        error_log("SQL Error: " . $stmt->error);
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    die("invalid_request");
}
?>
