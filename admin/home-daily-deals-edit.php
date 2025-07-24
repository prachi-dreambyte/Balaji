<?php
session_start();
include "./db_connect.php";
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }


// Check if ID is provided in URL
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = intval($_GET['id']);

// Handle update form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $old_price   = $_POST['old_price'];
    $start_time  = $_POST['start_time'];
    $end_time    = $_POST['end_time'];

    // Image upload logic
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetPath = "../uploads/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
        $imageQuery = ", image = '$imageName'";
    } else {
        $imageQuery = "";
    }

    // Update query
    $sql = "UPDATE home_daily_deal SET 
                name = '$name',
                description = '$description',
                price = '$price',
                old_price = '$old_price',
                start_time = '$start_time',
                end_time = '$end_time'
                $imageQuery
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Product updated successfully'); window.location.href='home-daily-deals-list.php';</script>";
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch existing product data
$result = $conn->query("SELECT * FROM `home_daily_deal` WHERE id = $id");
if ($result->num_rows != 1) {
    die("Product not found or not a daily deal.");
}
$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Daily Deal</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        form { max-width: 600px; margin: auto; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        img { margin-top: 10px; max-height: 150px; }
        button { margin-top: 15px; padding: 10px 20px; }
    </style>
</head>
<body>

<h2>Edit Daily Deal Product</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Product Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

    <label>Description</label>
    <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>

    <label>Price</label>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>

    <label>Old Price</label>
    <input type="number" step="0.01" name="old_price" value="<?= $product['old_price'] ?>">

    <label>Start Time</label>
    <input type="datetime-local" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($product['start_time'])) ?>">

    <label>End Time</label>
    <input type="datetime-local" name="end_time" value="<?= date('Y-m-d\TH:i', strtotime($product['end_time'])) ?>">

    <label>Image</label>
    <input type="file" name="image">
    <?php if (!empty($product['image'])): ?>
        <img src="../uploads/<?= $product['image'] ?>" alt="Product Image">
    <?php endif; ?>

    <button type="submit">Update Product</button>
</form>

</body>
</html>
 