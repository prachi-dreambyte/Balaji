<?php
include 'connect.php';

if (isset($_POST['update_product'])) {
    $id = $_POST['product_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'];
    $category = $_POST['category'];

    // ✅ Start with images from DB if form didn't send them
    $imgs = [];
    $result = mysqli_query($conn, "SELECT images FROM products WHERE id='$id'");
    if ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['images'])) {
            $imgs = json_decode($row['images'], true);
            if (!is_array($imgs)) $imgs = [];
        }
    }

    // ✅ If form sends existing_images, override DB
    if (isset($_POST['existing_images']) && is_array($_POST['existing_images'])) {
        $imgs = $_POST['existing_images'];
    }

    // ✅ Remove selected images (compare full relative path + unlink file)
    if (!empty($_POST['remove_image'])) {
        foreach ($_POST['remove_image'] as $remove_path) {
            $imgs = array_filter($imgs, function ($img) use ($remove_path) {
                $img_rel = ltrim(str_replace('\\', '/', $img), '/');
                $remove_rel = ltrim(str_replace('\\', '/', $remove_path), '/');

                // ✅ Delete file if exists
                if ($img_rel === $remove_rel && file_exists($remove_rel)) {
                    unlink($remove_rel);
                }

                return $img_rel !== $remove_rel;
            });
        }
        $imgs = array_values($imgs); // reindex
    }

    // ✅ Handle new uploads
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $filename) {
            $tmp_name = $_FILES['images']['tmp_name'][$key];
            $new_name = uniqid() . "_" . basename($filename);
            $target_file = "uploads/products/" . $new_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $imgs[] = $target_file;
            }
        }
    }

    // ✅ Save updated images JSON
    $images_json = mysqli_real_escape_string($conn, json_encode($imgs));

    // ✅ Update DB
    $query = "UPDATE products 
              SET name='$name', description='$description', price='$price', 
                  discount_price='$discount_price', category='$category', 
                  images='$images_json' 
              WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: manage_products.php?success=1");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
