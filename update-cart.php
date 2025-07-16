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

    $items = [];
    $total = 0;

    foreach ($ids as $index => $product_id) {
        $product_id = intval($product_id);
        $quantity = intval($quantities[$index]);

        if ($quantity < 1) $quantity = 1;

        // Update quantity in cart
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();

        // Get updated product price
        $product_stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $product_stmt->bind_param("i", $product_id);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();
        $product = $product_result->fetch_assoc();

        if ($product) {
            $price = $product['price'];
            $subtotal = $price * $quantity;
            $total += $subtotal;

            $items[] = [
                'id' => $product_id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'items' => $items,
        'total' => $total
    ]);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>
