<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id']) && isset($_POST['quantity'])) {
    $user_id = $_SESSION['user_id'];
    $ids = $_POST['id'];
    $quantities = $_POST['quantity'];

    // Debug output
    error_log("Received POST data: " . print_r($_POST, true));
    
    $items = [];
    $total = 0;
    $debug_info = [];

    foreach ($ids as $index => $product_id) {
        $product_id = intval($product_id);
        $quantity = intval($quantities[$index]);

        // Debug before any modification
        $debug_info[] = "Processing product $product_id with requested quantity $quantity";

        // Validate quantity (minimum 1, maximum 1000)
        $quantity = max(1, min(1000, $quantity));

        // Check product availability
        $product_stmt = $conn->prepare("SELECT id, price, stock FROM products WHERE id = ?");
        $product_stmt->bind_param("i", $product_id);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();
        $product = $product_result->fetch_assoc();

        if (!$product) {
            // Try home_daily_deal if not found in products
            $deal_stmt = $conn->prepare("SELECT id, price, stock FROM home_daily_deal WHERE id = ?");
            $deal_stmt->bind_param("i", $product_id);
            $deal_stmt->execute();
            $deal_result = $deal_stmt->get_result();
            $product = $deal_result->fetch_assoc();
        }

        if ($product) {
            // Adjust quantity if exceeds available stock
           if (isset($product['stock']) && $quantity > $product['stock']) {
    $debug_info[] = "Reducing quantity from $quantity to {$product['stock']} due to stock limit";
    $quantity = $product['stock'];
}

            // Update cart
            $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $update_stmt->bind_param("iii", $quantity, $user_id, $product_id);
            $update_result = $update_stmt->execute();
            
            if (!$update_result) {
                $debug_info[] = "Update failed for product $product_id: " . $update_stmt->error;
            } else {
                $debug_info[] = "Successfully updated product $product_id to quantity $quantity";
            }

            // Calculate pricing
            $price = $product['price'];
            $subtotal = $price * $quantity;
            $total += $subtotal;

            $items[] = [
                'id' => $product_id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal
            ];
        } else {
            $debug_info[] = "Product $product_id not found";
        }
    }

    $flat_rate = $total * 0.18;
    $coupon_discount = $_SESSION['coupon_discount'] ?? 0;
    $coins_applied = $_SESSION['coins_applied'] ?? 0;
    $grand_total = max(0, $total + $flat_rate - $coupon_discount - $coins_applied);

    echo json_encode([
        'status' => 'success',
        'items' => $items,
        'total' => $total,
        'flat_rate' => $flat_rate,
        'grand_total' => $grand_total,
        'debug' => $debug_info // Include debug info in response
    ]);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>