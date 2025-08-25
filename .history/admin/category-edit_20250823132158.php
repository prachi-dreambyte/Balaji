<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['admin_id'])) {
     header("Location: auth-signin.php");
     exit;
}

// Get category id
if (!isset($_GET['id']) || empty($_GET['id'])) {
     die("Invalid Category ID.");
}
$categoryId = intval($_GET['id']);

// Fetch category data
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
     die("Category not found.");
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $categoryName = trim($_POST['category_name']);
     $mainCategory = trim($_POST['main_category']);

     // Image uploads
     function uploadImage($fileInput, $existingImage)
     {
          if (!empty($_FILES[$fileInput]['name'])) {
               $targetDir = "uploads/";
               $fileName = time() . "_" . basename($_FILES[$fileInput]["name"]);
               $targetFile = $targetDir . $fileName;
               move_uploaded_file($_FILES[$fileInput]["tmp_name"], $targetFile);
               return $fileName;
          }
          return $existingImage;
     }

     $thumbnailPath = uploadImage("category_image", $category['category_image']);
     $bannerPath = uploadImage("banner_image", $category['banner_image']);

     // Update query
     $updateStmt = $conn->prepare("UPDATE categories 
        SET category_name=?, Main_Category_name=?, category_image=?, banner_image=? 
        WHERE id=?");
     $updateStmt->bind_param("ssssi", $categoryName, $mainCategory, $thumbnailPath, $bannerPath, $categoryId);

     if ($updateStmt->execute()) {
          echo "<script>
            alert('Category Updated Successfully!');
            window.location.href = 'category-list.php';
        </script>";
          exit;
     } else {
          echo "<script>alert('Update failed. Try again.');</script>";
     }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <title>Edit Category</title>
     <link href="assets/css/app.min.css" rel="stylesheet" />
</head>

<body>
     <div class="container mt-4">
          <h2>Edit Category</h2>
          <form action="" method="POST" enctype="multipart/form-data">
               <div class="mb-3">
                    <label>Main Category</label>
                    <input type="text" name="main_category" class="form-control"
                         value="<?= htmlspecialchars($category['Main_Category_name']) ?>" required>
               </div>
               <div class="mb-3">
                    <label>Category Title</label>
                    <input type="text" name="category_name" class="form-control"
                         value="<?= htmlspecialchars($category['category_name']) ?>" required>
               </div>
               <div class="mb-3">
                    <label>Thumbnail</label><br>
                    <img src="uploads/<?= $category['category_image'] ?>" height="60"><br>
                    <input type="file" name="category_image" class="form-control">
               </div>
               <div class="mb-3">
                    <label>Banner</label><br>
                    <img src="uploads/<?= $category['banner_image'] ?>" height="60"><br>
                    <input type="file" name="banner_image" class="form-control">
               </div>
               <button type="submit" class="btn btn-primary">Update Category</button>
               <a href="category-list.php" class="btn btn-secondary">Cancel</a>
          </form>
     </div>
</body>

</html>