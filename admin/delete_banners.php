<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleting Category...</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
if (isset($_GET['id']) && isset($_GET['image'])) {
    $id = $_GET['id'];
    $image = $_GET['image'];

    // Delete category from database
    $stmt = $conn->prepare("DELETE FROM home_banners WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Delete image from folder
        $imagePath = "uploads/" . $image;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Home page benners Deleted!',
                text: 'The category has been successfully deleted.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'home-banners-list.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Could not delete the category.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'home-banners-list.php';
            });
        </script>";
    }
}
?>
</body>
</html>
