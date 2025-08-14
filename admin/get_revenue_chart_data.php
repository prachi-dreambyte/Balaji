<?php
require 'db_connection.php'; // your DB connection file

$range = $_GET['range'] ?? '1y';
$where = "";

switch ($range) {
    case '1m':
        $where = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case '6m':
        $where = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
        break;
    case '1y':
        $where = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        break;
    case 'all':
    default:
        $where = "";
}

$sql = "SELECT DATE(created_at) AS date, SUM(amount) AS total 
        FROM orders 
        WHERE payment_status = 'paid' $where
        GROUP BY DATE(created_at)
        ORDER BY date ASC";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'date' => $row['date'],
        'total' => (float) $row['total']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
