<?php
include 'connect.php';

$category = $_GET['category'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Category & Products</title>
</head>
<body>

<!-- Category List -->
<h2>🪑 All Categories</h2>
<span style="display: grid; grid-template-columns: 200px 200px; gap: 10px;">
<?php
    $sql = "SELECT * FROM categories";
    $result = mysqli_query($conn, $sql);
    if ($result):
        while ($row = mysqli_fetch_assoc($result)):
?>
        <a href="category.php?category=<?php echo urlencode($row['category_name']); ?>" 
           style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <?php echo $row['category_name']; ?>
        </a>
<?php
        endwhile;
    else:
        echo "<span>No categories found.</span>";
    endif;
?>
</span>

<hr>

<!-- Product List -->
<?php if ($category): ?>
    <h2>🛒 Products in "<?php echo htmlspecialchars($category); ?>"</h2>
    <ul>
        <?php
        $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0):
            while ($product = $result->fetch_assoc()):
        ?>
            <li>
                <strong><?php echo $product['product_name']; ?></strong><br>
                ₹<?php echo $product['price']; ?><br>
                <?php echo $product['description']; ?><br>
                <img src="uploads/<?php echo $product['image']; ?>" width="100"><br><br>
            </li>
        <?php
            endwhile;
        else:
            echo "<p>No products found in this category.</p>";
        endif;
        ?>
    </ul>
<?php endif; ?>

</body>
</html>
