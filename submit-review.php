<?php
include 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']);
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $user_name = 'Guest';

    // Fetch user name
    if ($user_id > 0) {
        $query = "SELECT name FROM signup WHERE id = $user_id LIMIT 1";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $user_name = mysqli_real_escape_string($conn, $row['name']);
        }
    }

    $rating = intval($_POST['rating']);
    $review_text = mysqli_real_escape_string($conn, $_POST['review_text']);
    $created_at = date('Y-m-d H:i:s');

    // Handle multiple image upload
    $uploaded_images = [];
    if (!empty($_FILES['review_image']['name'][0])) {
        $upload_dir = 'uploads/review-images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['review_image']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['review_image']['error'][$key] === 0) {
                $filename = time() . '_' . basename($_FILES['review_image']['name'][$key]);
                $target = $upload_dir . $filename;

                if (move_uploaded_file($tmp_name, $target)) {
                    $uploaded_images[] = $target;
                }
            }
        }
    }

    // Save paths as JSON in image_path column
    $image_path_json = mysqli_real_escape_string($conn, json_encode($uploaded_images));

    // Insert review into DB
    $query = "INSERT INTO reviews (product_id, user_name, rating, review_text, image_path, created_at)
              VALUES ($product_id, '$user_name', $rating, '$review_text', '$image_path_json', '$created_at')";

    if (mysqli_query($conn, $query)) {
        header("Location: product-details.php?id=$product_id#reviews");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
