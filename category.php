<?php
include 'connect.php'; 
?>

$sql = "SELECT * FROM categories ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Categories - Balaji Furniture</title>
    <style>
        .category-box {
            width: 250px;
            border: 1px solid #ccc;
            margin: 20px;
            text-align: center;
            padding: 10px;
            float: left;
            border-radius: 10px;
            transition: 0.3s;
        }
        .category-box img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .category-box:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">🪑 Our Furniture Categories</h2>

<div style="display:flex; flex-wrap: wrap; justify-content:center;">
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <a href="products.php?category=<?php echo urlencode($row['category_name']); ?>" style="text-decoration: none; color: inherit;">
  <div class="category-box">
      <img src="admin/uploads/<?php echo $row['category_image']; ?>" alt="<?php echo $row['category_name']; ?>">
      <h3><?php echo $row['category_name']; ?></h3>
  </div>
</a>

    <?php } ?>
</div>

</body>
</html>
