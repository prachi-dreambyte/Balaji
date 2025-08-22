<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id']))  {
    header("Location: auth-signin.php");
    exit;
}

// Validate blog ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid blog ID.");
}

$blogId = intval($_GET['id']);

// Fetch blog data to delete associated images
$stmt = $conn->prepare("SELECT main_images, sub_images FROM blog WHERE id = ?");
$stmt->bind_param("i", $blogId);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();
$stmt->close();

if (!$blog) {
    die("Blog not found.");
}

// Delete main image
if (!empty($blog['main_images'])) {
    $mainPath = "uploads/" . $blog['main_images'];
    if (file_exists($mainPath)) {
        unlink($mainPath);
    }
}

// Delete sub images
$subImages = json_decode($blog['sub_images'], true);
if (!empty($subImages) && is_array($subImages)) {
    foreach ($subImages as $subImg) {
        $subPath = "uploads/" . $subImg;
        if (file_exists($subPath)) {
            unlink($subPath);
        }
    }
}

// Delete blog record from database
$stmt = $conn->prepare("DELETE FROM blog WHERE id = ?");
$stmt->bind_param("i", $blogId);
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    echo "<script>
        alert('Blog deleted successfully.');
        window.location.href = 'blog-list.php';
    </script>";
} else {
    $stmt->close();
    $conn->close();
    echo "<script>
        alert('Failed to delete blog.');
        window.history.back();
    </script>";
}
?>
