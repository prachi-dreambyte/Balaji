<?php
session_start();

// Accept product_id from GET or POST
$product_id = $_POST['product_id'] ?? $_GET['product_id'] ?? null;

if (!$product_id) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is missing']);
    exit;
}

if (!isset($_SESSION['compare_list'])) {
    $_SESSION['compare_list'] = [];
}

if (!in_array($product_id, $_SESSION['compare_list'])) {
    $_SESSION['compare_list'][] = $product_id;
}

echo json_encode(['status' => 'success', 'count' => count($_SESSION['compare_list'])]);
?>
