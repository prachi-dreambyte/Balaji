<?php
include 'connect.php';

$category = $_GET['category'] ?? '';

if ($category == '') {
    echo "<h2>No category selected!</h2>";
    exit;
}

$sql = "SELECT * FROM products WHERE category = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products - <?php echo htmlspecialchars($category); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .product-box {
            flex: 1 1 250px;
            max-width: 300px;
            border: 1px solid #ccc;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            transition: 0.3s;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }

        .product-box img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-box:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        @media screen and (max-width: 768px) {
            .product-box {
                flex: 1 1 100%;
                max-width: 90%;
            }
        }
    </style>
</head>
<body>

<h2>🪑 Products in "<?php echo htmlspecialchars($category); ?>"</h2>

<div class="product-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php
                $images = json_decode($row['images']);
                $imagePath = isset($images[0]) ? '../admin/' . str_replace("\\", "/", $images[0]) : '../admin/uploads/no-image.jpg';
            ?>
            <div class="product-box">
                <img src="<?php echo $imagePath; ?>" alt="<?php echo $row['product_name']; ?>">
                <h3><?php echo $row['product_name']; ?></h3>
                <p><strong>Brand:</strong> <?php echo $row['brand']; ?></p>
                <p><strong>Price:</strong> ₹<?php echo $row['price']; ?></p>
                <p><strong>Description:</strong> <?php echo $row['description']; ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">No products found in this category.</p>
    <?php endif; ?>
</div>

</body>
</html>
