<?php
session_start();
include 'connect.php'; // adjust path if needed

// Get product IDs from session
$compare_ids = isset($_SESSION['compare_list']) ? $_SESSION['compare_list'] : [];

// If no items, show message
if (empty($compare_ids)) {
    echo "<div style='text-align:center; padding:50px;'>
            <h3>🛒 No items in compare list!</h3>
            <a href='shop.php' class='btn btn-primary mt-3'>Continue Shopping</a>
          </div>";
    exit;
}

// Prepare SQL query to fetch those products
$id_placeholders = implode(',', array_fill(0, count($compare_ids), '?'));
$query = "SELECT * FROM products WHERE id IN ($id_placeholders)";
$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('i', count($compare_ids)), ...$compare_ids);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Compare Products</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .compare-table th, .compare-table td {
            text-align: center;
            vertical-align: middle;
        }
        .compare-img {
            width: 120px;
            height: auto;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Compare Products</h2>
    <div class="table-responsive">
        <table class="table table-bordered compare-table">
            <thead class="bg-light">
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    
                    <th>Description</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): 
                    $images = json_decode($row['images']);
                    $firstImage = $images[0];
                ?>
                <tr>
                    <td><img src="admin/<?php echo $firstImage; ?>" class="compare-img" alt="Image"></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td>₹ <?php echo $row['price']; ?></td>
                    
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <form method="post" action="remove-compare.php" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <a href="shop.php" class="btn btn-secondary mt-3">← Back to Shop</a>
</div>
</body>
</html>
