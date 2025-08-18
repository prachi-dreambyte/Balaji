<?php
header('Content-Type: application/json');
session_start();
include "db_connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get period from query string (default 1 year)
    $period = isset($_GET['period']) ? $_GET['period'] : '1y';

    // Determine interval for SQL
    $intervalMap = [
        '1M' => '1 MONTH',
        '3M' => '3 MONTH',
        '6M' => '6 MONTH',
        '1y' => '1 YEAR',
        '2y' => '2 YEAR'
    ];
    $interval = $intervalMap[$period] ?? '1 YEAR';

    // Get revenue data
    $query = "
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS month,
            SUM(amount) AS revenue
        FROM orders
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL $interval)
          AND status = 'Delivered'
        GROUP BY month
        ORDER BY month
    ";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }

    $categories = [];
    $revenueData = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['month'];
        $revenueData[] = (float) $row['revenue'];
    }

    // Fill missing months with 0 revenue
    $allMonths = [];
    // Start month based on selected interval
    $start = new DateTime();
    $start->modify("-" . str_replace(['M', 'y'], [' month', ' year'], strtolower($period)));
    $start->modify('first day of this month');

    $end = new DateTime('first day of next month');
    $intervalObj = new DateInterval('P1M');
    $periodObj = new DatePeriod($start, $intervalObj, $end);

    $finalCategories = [];
    $finalRevenue = [];

    foreach ($periodObj as $dt) {
        $monthKey = $dt->format("Y-m");
        $finalCategories[] = $monthKey;

        $index = array_search($monthKey, $categories);
        $finalRevenue[] = ($index !== false) ? $revenueData[$index] : 0;
    }

    echo json_encode([
        'success' => true,
        'categories' => $finalCategories,
        'revenueData' => $finalRevenue
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} finally {
    if ($conn)
        mysqli_close($conn);
}