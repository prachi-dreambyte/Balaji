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

    // Start transaction to ensure data consistency
    $conn->begin_transaction();
    
    try {
        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        
        // If status is 'Confirmed', reduce product stock
        if ($status === 'Confirmed') {
            // Get order_id string from orders table
            $order_stmt = $conn->prepare("SELECT order_id FROM orders WHERE id = ?");
            $order_stmt->bind_param("i", $order_id);
            $order_stmt->execute();
            $order_result = $order_stmt->get_result();
            
            if ($order_row = $order_result->fetch_assoc()) {
                $order_id_string = $order_row['order_id'];
                
                // Get items from order_items table
                $items_stmt = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
                $items_stmt->bind_param("s", $order_id_string);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                
                while ($item = $items_result->fetch_assoc()) {
                    $product_id = $item['product_id'];
                    $quantity = $item['quantity'];
                    
                    // Try to update stock in products table
                    $update_stmt = $conn->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");
                    $update_stmt->bind_param("ii", $quantity, $product_id);
                    $update_stmt->execute();
                    
                    // Also try to update stock in home_daily_deal table if product exists there
                    $update_deal_stmt = $conn->prepare("UPDATE home_daily_deal SET stock = GREATEST(0, stock - ?) WHERE id = ?");
                    $update_deal_stmt->bind_param("ii", $quantity, $product_id);
                    $update_deal_stmt->execute();
                }
            }
        }
        
        // Commit the transaction
        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        error_log("Error updating stock: " . $e->getMessage());
        echo "error";
    }
    
    $conn->close();
} else {
    die("invalid_request");
}
?>