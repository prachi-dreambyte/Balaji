<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id']) && isset($_POST['quantity'])) {
    $user_id = $_SESSION['user_id'];
    $ids = $_POST['id'];
    $quantities = $_POST['quantity'];

    foreach ($ids as $index => $product_id) {
        $quantity = intval($quantities[$index]);

        // Minimum quantity is 1
        if ($quantity < 1) $quantity = 1;

        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
    }
    

    header("Location: shopping-cart.php?updated=1");
    exit;
}
?>

