<?php
include "./db_connect.php";

if (!isset($_GET['id'])) {
    die("No product ID provided.");
}

$id = intval($_GET['id']);

// Fetch existing product data
$stmt = $conn->prepare("SELECT * FROM home_daily_deal WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $old_price = $_POST['old_price'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;

    // Image handling
    $image = $product['images']; // default to existing image
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = '../uploads/' . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image = $image_name;
        }
    }

    // Update product
    $stmt = $conn->prepare("UPDATE home_daily_deal SET product_name = ?, description = ?, price = ?, old_price = ?, deal_start = ?, deal_end = ?, images = ? WHERE id = ?");
    $stmt->bind_param("ssddsssi", $name, $description, $price, $old_price, $start_time, $end_time, $image, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully'); window.location.href='home-daily-deals.php';</script>";
    } else {
        echo "Error updating: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Daily Deal</title>
    <style>
        form {
            max-width: 600px;
            margin: auto;
        }
        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
        }
        input, textarea {
            width: 100%;
            padding: 6px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
        }
        img {
            max-height: 100px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>Edit Daily Deal Product</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Product Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['product_name']) ?>" required>

    <label>Description</label>
    <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>

    <label>Price</label>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>

    <label>Old Price</label>
    <input type="number" step="0.01" name="old_price" value="<?= $product['old_price'] ?>">

    <label>Start Time</label>
    <input type="datetime-local" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($product['deal_start'])) ?>">

    <label>End Time</label>
    <input type="datetime-local" name="end_time" value="<?= date('Y-m-d\TH:i', strtotime($product['deal_end'])) ?>">

    <label>Image</label>
    <input type="file" name="image">
    <?php if (!empty($product['images'])): ?>
        <img src="../uploads/<?= htmlspecialchars($product['images']) ?>" alt="Product Image">
    <?php endif; ?>

    <button type="submit">Update Product</button>
</form>

</body>
</html>
