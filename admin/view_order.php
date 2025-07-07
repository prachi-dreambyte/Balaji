<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id <= 0) {
    echo "<p class='text-danger'>Invalid Order ID.</p>";
    exit;
}

// Fetch order details
$sql = "SELECT o.id, o.user_id, o.discount, o.final_price, o.total_price, o.status, o.order_date, oi.product_id, oi.quantity, oi.price
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order = null;
$items = [];

while ($row = $result->fetch_assoc()) {
    if (!$order) {
        $order = [
            "id" => $row["id"],
            "user_id" => $row["user_id"],
            "total_price" => $row["total_price"],
            "discount" => $row["discount"],
            "final_price" => $row["final_price"],
            "status" => $row["status"],
            "order_date" => $row["order_date"],
            "items" => []
        ];
    }
    
    // Add items
    $items[] = [
        "product_id" => $row["product_id"],
        "quantity" => $row["quantity"],
        "price" => $row["price"] * $row["quantity"],
    ];
}

$stmt->close();
$conn->close();

// Display order details
if ($order):
?>
    <table class="table table-bordered">
        <tr><th>Order ID</th><td>#<?= htmlspecialchars($order['id']) ?></td></tr>
        <tr><th>User ID</th><td><?= htmlspecialchars($order['user_id']) ?></td></tr>
        <tr><th>Total Price</th><td>₹<?= htmlspecialchars(number_format($order['total_price'], 2)) ?></td></tr>
        <tr><th>Discount</th><td>₹<?= htmlspecialchars(number_format($order['discount'], 2)) ?></td></tr>
        <tr><th>Final Price</th><td>₹<?= htmlspecialchars(number_format($order['final_price'], 2)) ?></td></tr>
    
        <tr><th>Status</th><td><?= htmlspecialchars($order['status']) ?></td></tr>
        <tr><th>Order Date</th><td><?= htmlspecialchars($order['order_date']) ?></td></tr>
        <tr>
    <th>Update Status</th>
    <td>
        <select id="orderStatus" class="form-select" data-id="<?= $order['id'] ?>">
            <option value="Pending" <?= ($order['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
            <option value="Confirmed" <?= ($order['status'] == 'Confirmed') ? 'selected' : '' ?>>Confirmed</option>
            <option value="Shipped" <?= ($order['status'] == 'Shipped') ? 'selected' : '' ?>>Shipped</option>
            <option value="Delivered" <?= ($order['status'] == 'Delivered') ? 'selected' : '' ?>>Delivered</option>
            
        </select>
        <p id="statusMessage" class="mt-2 text-success"></p>
    </td>
</tr>

    </table>

    <h5 class="mt-3">Products in Order</h5>
    <table class="table table-hover">
        <thead><tr><th>Product ID</th><th>Quantity</th><th>Price</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_id']) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td>₹<?= htmlspecialchars(number_format($item['price'], 2)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-danger">Order not found.</p>
<?php endif; ?>



<script>
$(document).ready(function() {
    $("#orderStatus").change(function() {
        var orderId = $(this).data("id"); // Order ID
        var newStatus = $(this).val(); // Selected Status
        
        $.ajax({
            url: "update_status.php",
            type: "POST",
            data: { id: orderId, status: newStatus },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $("#statusMessage").text(response.message).removeClass("text-danger").addClass("text-success");
                } else {
                    $("#statusMessage").text(response.message).removeClass("text-success").addClass("text-danger");
                }
            },
            error: function() {
                $("#statusMessage").text("Error updating status.").removeClass("text-success").addClass("text-danger");
            }
        });
    });
});
</script>


