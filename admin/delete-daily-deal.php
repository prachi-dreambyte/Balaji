<?php
session_start();

// Check for login
if (!isset($_SESSION['admin_id']))  {
    header("Location: auth-signin.php");
    exit;
}

// DB connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'balaji';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and delete
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Optional: Get image and delete file (if needed)
    $getImage = $conn->query("SELECT image FROM home_daily_deal WHERE id = $id");
    if ($getImage && $getImage->num_rows > 0) {
        $imgRow = $getImage->fetch_assoc();
        $imgPath = 'uploads/' . $imgRow['image'];
        if (file_exists($imgPath)) {
            unlink($imgPath); // delete image file
        }
    }

    // Delete record
    $deleteQuery = "DELETE FROM home_daily_deal WHERE id = $id";
    if ($conn->query($deleteQuery) === TRUE) {
        header("Location: home-daily-deals-list.php?deleted=1");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
