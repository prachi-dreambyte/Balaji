<?php
session_start();
include "./db_connect.php";
if (!isset($_SESSION['user_id'])) {
     header("Location: auth-signin.php");
     exit;
 }

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle file upload
    if ($_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../img/banners/';
        $fileName = uniqid() . '_' . basename($_FILES['banner_image']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetPath)) {
            // Insert into database
            $stmt = $conn->prepare("
                INSERT INTO category_banners 
                (category_id, banner_image, is_active) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                banner_image = VALUES(banner_image),
                is_active = VALUES(is_active)
            ");
            $stmt->bind_param("isi", $category_id, $fileName, $is_active);
            $stmt->execute();
        }
    }
}

// Fetch all categories
$categories = $conn->query("SELECT id, category_name FROM categories");
?>

<form method="POST" enctype="multipart/form-data">
    <select name="category_id" required>
        <option value="">Select Category</option>
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
        <?php endwhile; ?>
    </select>
    
    <input type="file" name="banner_image" accept="image/*" required>
    
    <label>
        <input type="checkbox" name="is_active" checked> Active
    </label>
    
    <button type="submit">Upload Banner</button>
</form>