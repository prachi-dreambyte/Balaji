<?php
session_start();
include "./db_connect.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM categories WHERE id = $id");
$category = mysqli_fetch_assoc($result);

if (!$category) {
     die("Category not found.");
}

// Update category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $name = mysqli_real_escape_string($conn, $_POST['category_name']);
     $desc = mysqli_real_escape_string($conn, $_POST['description']);
     mysqli_query($conn, "UPDATE categories SET category_name='$name', description='$desc' WHERE id=$id");
     header("Location: category-list.php");
     exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <title>Edit Category</title>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container py-4">

     <h2>Edit Category</h2>
     <form method="POST">
          <div class="mb-3">
               <label class="form-label">Category Name</label>
               <input type="text" name="category_name" class="form-control"
                    value="<?= htmlspecialchars($category['category_name']) ?>" required>
          </div>
          <div class="mb-3">
               <label class="form-label">Description</label>
               <textarea name="description"
                    class="form-control"><?= htmlspecialchars($category['description']) ?></textarea>
          </div>
          <button type="submit" class="btn btn-success">Update</button>
          <a href="category-list.php" class="btn btn-secondary">Cancel</a>
     </form>

</body>

</html>