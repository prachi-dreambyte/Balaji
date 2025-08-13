<?php
session_start();
require __DIR__ . '/admin/db_connect.php';

header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);

// Validate input
if ($id <= 0) {
    echo json_encode(['error' => 'Invalid variant ID']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM variants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$variant = $stmt->get_result()->fetch_assoc();

if (!$variant) {
    echo json_encode(['error' => 'Variant not found']);
    exit;
}

// Calculate final price
$price = floatval($variant['price']);
$discount = floatval($variant['discount']);
$corporate_discount = floatval($variant['corporate_discount']);
$final_price = $price - $discount;

if (!empty($_SESSION['account_type']) && $_SESSION['account_type'] === 'commercial') {
    $final_price -= $corporate_discount;
}
$final_price = max($final_price, 0);

// Return JSON
echo json_encode([
    'id' => $variant['id'],
    'image' => $variant['image'],
    'color' => $variant['color'],
    'stock' => $variant['stock'],
    'old_price' => number_format($price, 2),
    'final_price' => number_format($final_price, 2)
]);
