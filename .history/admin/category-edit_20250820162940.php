<?php
session_start();
include "./db_connect.php";
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
}

$id = $_GET['id'] ?? 0;

// Fetch category details
$stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

// Function to handle image upload
function uploadImage($fileKey, $oldFile = '', $uploadDir = "uploads/")
{
     if (!empty($_FILES[$fileKey]['name'])) {
          $fileName = time() . "_" . basename($_FILES[$fileKey]['name']);
          $targetFile = $uploadDir . $fileName;
          $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
          $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

          if (!in_array($fileType, $allowed)) {
               echo "<script>alert('Invalid File Type!');</script>";
               return $oldFile;
          }

          if (!is_dir($uploadDir)) {
               mkdir($uploadDir, 0777, true);
          }

          if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
               // Delete old image if exists
               if (!empty($oldFile) && file_exists($oldFile)) {
                    unlink($oldFile);
               }
               return $targetFile;
          }
     }
     return $oldFile; // keep old image if no new upload
}

// Update category on form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $categoryName = trim($_POST['category']);
     $metaTitle = trim($_POST['meta_title']);
     $metaKeyword = trim($_POST['meta_keyword']);
     $metaDesc = trim($_POST['meta_desc']);
     $stock = trim($_POST['stock']);
     $tagId = trim($_POST['tag_id']);
     $description = trim($_POST['description']);

     $thumbnailPath = uploadImage('category_image', $category['category_image']);
     $bannerPath = uploadImage('banner_image', $category['banner_image']);

     $update = $conn->prepare("UPDATE categories SET category_name=?, category_image=?, banner_image=?, meta_title=?, meta_keyword=?, meta_desc=?, stock=?, tag_id=?, description=? WHERE id=?");
     $update->bind_param("ssssssissi", $categoryName, $thumbnailPath, $bannerPath, $metaTitle, $metaKeyword, $metaDesc, $stock, $tagId, $description, $id);

     if ($update->execute()) {
          echo "<script>
            alert('Category updated successfully');
            window.location.href='category-list.php';
        </script>";
     } else {
          echo "<script>alert('Error updating category');</script>";
     }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Edit Category</title>
</head>
<body>
    <h2>Edit Category</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Category Title:</label>
        <input type="text" name="category" value="<?= $category['category_name'] ?>"><br><br>

        <label>Thumbnail:</label><br>
        <img src="<?= $category['category_image'] ?>" width="100"><br>
        <input type="file" name="category_image"><br><br>

        <label>Banner:</label><br>
        <img src="<?= $category['banner_image'] ?>" width="200"><br>
        <input type="file" name="banner_image"><br><br>

        <label>Stock:</label>
        <input type="number" name="stock" value="<?= $category['stock'] ?? '' ?>"><br><br>

        <label>Tag ID:</label>
        <input type="text" name="tag_id" value="<?= $category['tag_id'] ?? '' ?>"><br><br>

        <label>Description:</label>
        <textarea name="description"><?= $category['description'] ?? '' ?></textarea><br><br>

        <h3>Meta Options</h3>
        <label>Meta Title:</label>
        <input type="text" name="meta_title" value="<?= $category['meta_title'] ?? '' ?>"><br><br>

        <label>Meta Keyword:</label>
        <input type="text" name="meta_keyword" value="<?= $category['meta_keyword'] ?? '' ?>"><br><br>

        <label>Meta Description:</label>
        <textarea name="meta_desc"><?= $category['meta_desc'] ?? '' ?></textarea><br><br>

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
