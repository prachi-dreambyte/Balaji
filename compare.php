<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['compare_list'])) {
    $_SESSION['compare_list'] = [];
}

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    if (!in_array($product_id, $_SESSION['compare_list'])) {
        $_SESSION['compare_list'][] = $product_id;
    }

    $response = [
        'status' => 'success',
        'message' => '✅ Product added to compare!',
        'count' => count($_SESSION['compare_list']),
    ];
}

echo json_encode($response);
