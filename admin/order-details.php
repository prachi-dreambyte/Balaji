<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 } // Database connection

// Get order ID from URL
$order_id = isset($_POST['id']) ? intval($_POST['id']) :"";

// if ($order_id <= 0) {
//     die("Invalid Order ID.");
// }

// Fetch order details and items
$sql = "SELECT 
            o.id AS order_id, 
            o.user_id,  
            o.status, 
            o.payment_status,  
            o.status AS order_status, 
            o.created_at,
            o.discount,
            o.amount,
            oi.product_id, 
            oi.quantity, 
            oi.price
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
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
            "id" => $row["order_id"],
            "customer_name" => $row["user_id"],
            "total_price" => $row["amount"],
            "payment_status" => $row["payment_status"],
            "order_status" => $row["status"],
            "created_at" => $row["created_at"],
            "discount" => $row["discount"],
            
            "items" => []
        ];
    }

    if (!empty($row["product_id"])) {
        $items[] = [
            "product_id" => $row["product_id"],
            "quantity" => $row["quantity"],
            "price" => $row["price"]
        ];
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Order Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.iconify.design/2/2.2.1/iconify.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <div class="container-xxl">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Order Details</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($order): ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Order ID</th>
                                        <td>#<?= htmlspecialchars($order['id']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Customer</th>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Total Price</th>
                                        <td>₹<?= htmlspecialchars(number_format($order['total_price'], 2)) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Discount</th>
                                        <td>₹<?= htmlspecialchars(number_format($order['discount'], 2)) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Final Price</th>
                                        <td>₹<?= htmlspecialchars(number_format($order['total_price'] + $order['discount'] , 2)) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Order Status</th>
                                        <td><?= htmlspecialchars($order['order_status']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Status</th>
                                        <td>
                                            <span class="badge <?= ($order['payment_status'] == 'Paid') ? 'bg-success text-light' : 'bg-danger text-light' ?>">
                                                <?= htmlspecialchars($order['payment_status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th> Update Order Status</th>
                                        <td>
                                            <select class="form-select order-status-select" data-order-id="<?= $order['id'] ?>">
                                                <option value="Pending" <?= ($order['order_status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                                <option value="Confirmed" <?= ($order['order_status'] == 'Confirmed') ? 'selected' : '' ?>>Confirmed</option>
                                                <option value="Shipped" <?= ($order['order_status'] == 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                                                <option value="Delivered" <?= ($order['order_status'] == 'Delivered') ? 'selected' : '' ?>>Delivered</option>
                                            </select>
                                            <span id="status-message-<?= $order['id'] ?>" class="text-success ms-2"></span>
                                        </td>
                                    </tr>
                                </table>

                                <h5 class="mt-4">Products in this Order:</h5>
                                <?php if (!empty($items)): ?>
                                    <table class="table table-hover">
                                        <thead class="bg-light-subtle">
                                            <tr>
                                                <th>Product ID</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
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
                                    <p>No products found for this order.</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-danger">Order not found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
  
<script>
  $(document).ready(function(){
    $(".order-status-select").on("change", function(){
      var newStatus = $(this).val();
      var orderId = $(this).data("order-id");
      
      console.log("Updating Order ID:", orderId, "New Status:", newStatus);
      
      $.post("update_status.php", { id: orderId, status: newStatus }, function(data){
        console.log("Server Response:", data);
        if(data.trim() === "success"){
          Swal.fire("Success!", "Order status updated successfully!", "success")
            .then(function(){
              location.reload();
            });
        } else {
          Swal.fire("Error!", "Failed to update order status.", "error");
        }
      })
      .fail(function(error){
        console.error("Error updating status:", error);
        Swal.fire("Error!", "Something went wrong.", "error");
      });
    });
  });
</script>

    
</body>

</html>
