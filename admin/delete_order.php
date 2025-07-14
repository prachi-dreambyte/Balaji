<?php
// Include database connection
include "./db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $orderId = intval($_POST['id']);

        // Prepare and execute delete query
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $orderId);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        $stmt->close();
    } else {
        echo "error";
    }
} else {
    echo "error";
}

$conn->close();
?>
