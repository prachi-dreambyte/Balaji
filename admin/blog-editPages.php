<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 }
 $stmt = $conn->prepare('SELECT * FROM blog');
 $stmt->execute();
 $result1 = $stmt->get_result();
 $stmt->close();
// Get Blog ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Blog ID.");
}
$blog_id = intval($_GET['id']);

// Fetch Blog details
$sql = "SELECT * FROM blog WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    die("Blog not found.");
}
$subImages = json_decode($blog['sub_images'], true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch form values
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $main_content = $_POST['main_content'];
    $sub_content = $_POST['sub_content'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $og_title = $_POST['og_title'];
    $og_description = $_POST['og_description'];
    $schema = $_POST['schema'];
    $keywords = $_POST['keywords'];
    $rating = $_POST['rating'];

   $old_main_image = $_POST['old_main_image'] ?? null;
   if (isset($_FILES['main_images']) && $_FILES['main_images']['error'] === 0) {
    // New image uploaded
    $main_image_name = time() . '_' . $_FILES['main_images']['name'];
    move_uploaded_file($_FILES['main_images']['tmp_name'], 'uploads/' . $main_image_name);

    // Optionally delete old image from server
    if (!empty($old_main_image) && file_exists('uploads/' . $old_main_image)) {
        unlink('uploads/' . $old_main_image);
    }
   } else {
    // No new image
    $main_image_name = $old_main_image;
  }

          //New Sub Img//
     $old_sub_image = $_POST['old_sub_image'] ?? null;
    if (isset($_FILES['sub_images']) && $_FILES['sub_images']['error'] === 0) {
    // New image uploaded
    $sub_image_name = time() . '_' . $_FILES['sub_images']['name'];
    move_uploaded_file($_FILES['sub_images']['tmp_name'], 'uploads/' . $sub_image_name);

    // Optionally delete old image from server
    if (!empty($old_sub_image) && file_exists('uploads/' . $old_sub_image)) {
        unlink('uploads/' . $old_sub_image);
    }
} else {
    // No new image
    $sub_image_name = $old_sub_image;
}      


    // Handle main image
    $main_image = $blog['main_images'];
    if (!empty($_FILES['main_images']['name'])) {
        $main_image = time() . '_' . $_FILES['main_images']['name'];
        move_uploaded_file($_FILES['main_images']['tmp_name'], 'uploads/' . $main_image);
    }

    // Handle sub images (multiple)
  
   
    if (!empty($_FILES['sub_images']['name'][0])) {
        foreach ($_FILES['sub_images']['name'] as $key => $name) {
            if ($_FILES['sub_images']['error'][$key] === 0) {
                $imgName = time() . '_' . $name;
                move_uploaded_file($_FILES['sub_images']['tmp_name'][$key], 'uploads/' . $imgName);
                $sub_images[] = $imgName;
            }
        }
    }

    $sub_images_json = json_encode($sub_images);

    // Update query
    $update_sql = "UPDATE blog SET 
        title = ?, slug = ?, main_content = ?, sub_content = ?, 
        meta_title = ?, meta_description = ?, og_title = ?, og_description = ?, 
        schema_data = ?, keywords = ?, rating = ?, main_images = ?, sub_images = ?
        WHERE id = ?";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param(
        "ssssssssssssss",
        $title, $slug, $main_content, $sub_content,
        $meta_title, $meta_description, $og_title, $og_description,
        $schema, $keywords, $rating, $main_image, $sub_images_json,
        $blog_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Blog updated successfully!'); window.location.href='blog-list.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating blog.');</script>";
    }

    $stmt->close();
}

// Close statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">



<head>
     <!-- Title Meta -->
     <meta charset="utf-8" />
     <title>Create Blog | Larkon - Responsive Admin Dashboard Template</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta name="description" content="A fully responsive premium admin dashboard template" />
     <meta name="author" content="Techzaa" />
     <meta http-equiv="X-UA-Compatible" content="IE=edge" />

     <!-- App favicon -->
     <link rel="shortcut icon" href="assets/images/favicon.ico">

     <!-- Vendor css (Require in all Page) -->
     <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

     <!-- Icons css (Require in all Page) -->
     <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

     <!-- App css (Require in all Page) -->
     <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

     <!-- Theme Config js (Require in all Page) -->
     <script src="assets/js/config.js"></script>
     <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
</head>

<body>
    <div class="wrapper">
        <?php include 'header.php'; ?>

        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-xl-8 col-lg-10 mx-auto">

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Blog</h4>
                            </div>
                            <div class="card-body">
                               <form method="post" enctype="multipart/form-data">
  <div class="card-body">
    <div class="row">
      <!-- Blog Title -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Title</label>
          <input type="text" name="title" class="form-control" placeholder="blog-title" value="<?= htmlspecialchars($blog['title']) ?>">
        </div>
      </div>

      <!-- Blog Slug -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Blog Page Name</label>
          <input type="text" name="slug" class="form-control" placeholder="page-name" value="<?= htmlspecialchars($blog['slug']) ?>">
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-12">
        <div class="mb-3">
        <label>Blog Main Content</label>
         <input type="text" name="main_content" class="form-control" value="<?= htmlspecialchars($blog['main_content']) ?>">
      </div>
      </div>

      <!-- Sub Content -->
      <div class="col-md-12">
        <div class="mb-3">
    <label for="description" class="form-label">Blog Sub Content</label>
    <textarea class="form-control bg-light-subtle" name="sub_content" id="description" rows="7"><?= htmlspecialchars($blog['sub_content']) ?></textarea>
</div>

      </div>

      <!-- Meta Title -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Meta Title</label>
          <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($blog['meta_title']) ?>">
        </div>
      </div>

      <!-- Meta Description -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Meta Description</label>
          <input type="text" name="meta_description" class="form-control" value="<?= htmlspecialchars($blog['meta_description']) ?>">
        </div>
      </div>

      <!-- OG Title -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>OG Title</label>
          <input type="text" name="og_title" class="form-control" value="<?= htmlspecialchars($blog['og_title']) ?>">
        </div>
      </div>

      <!-- OG Description -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>OG Description</label>
          <input type="text" name="og_description" class="form-control" value="<?= htmlspecialchars($blog['og_description']) ?>">
        </div>
      </div>

      <!-- Schema -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Schema</label>
          <input type="text" name="schema" class="form-control" value="<?= htmlspecialchars($blog['schema_data']) ?>">
        </div>
      </div>

      <!-- Keywords -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Keywords</label>
          <input type="text" name="keywords" class="form-control" placeholder="e.g. blog, seo, tech" value="<?= htmlspecialchars($blog['keywords']) ?>">
        </div>
      </div>

      <!-- Rating -->
      <div class="col-md-6">
        <div class="mb-3">
          <label>Rating</label>
          <input type="number" name="rating" step="0.1" max="5" class="form-control" value="<?= htmlspecialchars($blog['rating']) ?>">
        </div>
      </div>

      <!-- Main Image (single) -->
     <!-- Main Image (single) -->
<div class="col-md-6">
  <div class="mb-3">
    <label>Blog Main Image</label>
    <input type="file" name="main_images" id="mainImageInput" class="form-control">
    <input type="hidden" name="old_main_image" id="oldMainImageInput" value="<?= htmlspecialchars($blog['main_images']) ?>">
    
    <?php if (!empty($blog['main_images'])): ?>
      <div id="existingImageWrapper" style="position: relative; display: inline-block;">
        <img src="uploads/<?= htmlspecialchars(json_decode($blog['main_images'],true)) ?>" id="existingImage" style="max-width: 150px; margin-top: 10px;">
        <span id="removeImageBtn" style="position: absolute; top: 0; right: 0; background: red; color: white; padding: 2px 5px; cursor: pointer;">&times;</span>
      </div>
    <?php endif; ?>
  </div>
</div>


      <!-- Sub Images (multiple) -->
      <div class="col-md-6">
  <div class="mb-3">
    <label>Blog Sub Image</label>
    <input type="file" name="sub_images[]" id="subImageInput" class="form-control" multiple>
    <input type="hidden" name="old_main_image" id="oldSubImageInput" value="<?= htmlspecialchars($blog['sub_images']) ?>">
    
<?php if (!empty($subImages) && is_array($subImages)): ?>
  <?php foreach ($subImages as $index => $img): ?>
    <div class="sub-image-wrapper" style="display:inline-block; position:relative; margin-right:10px;" data-image="<?= htmlspecialchars($img) ?>">
        <img src="uploads/<?= htmlspecialchars($img) ?>" style="max-width:100px; margin-top:10px;">
        <span class="remove-sub-image" style="position: absolute; top: 0; right: 0; background: red; color: white; padding: 2px 5px; cursor: pointer;">&times;</span>
        <input type="hidden" name="existing_sub_images[]" value="<?= htmlspecialchars($img) ?>">
    </div>
  <?php endforeach; ?>
<?php endif; ?>


  </div>
</div>
<div class="col-12">
        <button type="submit" class="btn btn-primary">Update Blog</button>
        <a href="blog-list.php" class="btn btn-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center">
                            <script>document.write(new Date().getFullYear())</script> &copy; Larkon. Crafted by <a href="https://1.envato.market/techzaa" target="_blank">Techzaa</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>
  <script>
   document.addEventListener("DOMContentLoaded", function () {
    const removeBtn = document.getElementById("removeImageBtn");
    const existingImage = document.getElementById("existingImage");
    const wrapper = document.getElementById("existingImageWrapper");
    const hiddenInput = document.getElementById("oldMainImageInput");

    if (removeBtn) {
        removeBtn.addEventListener("click", function () {
            wrapper.style.display = "none";
            hiddenInput.value = ""; // Clear old image
        });
    }
   });
  </script>

<script>
   document.getElementById('mainImageInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const previewURL = URL.createObjectURL(file);
        const wrapper = document.getElementById("existingImageWrapper");
        if (wrapper) wrapper.remove();

        const img = document.createElement('img');
        img.src = previewURL;
        img.style.maxWidth = '150px';
        img.style.marginTop = '10px';
        document.querySelector('input[name="main_images"]').parentElement.appendChild(img);
    }
   });
</script>
<!-- <script>
    document.getElementById('subImageInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const previewURL = URL.createObjectURL(file);
        const wrapper = document.getElementById("existingImageWrapper");
        if (wrapper) wrapper.remove();

        const img = document.createElement('img');
        img.src = previewURL;
        img.style.maxWidth = '150px';
        img.style.marginTop = '10px';
        document.querySelector('input[name="sub_images"]').parentElement.appendChild(img);
    }
});

</script> -->
<script>
    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', '|',
                'bulletedList', 'numberedList', '|',
                'link', 'blockQuote', '|',
                'undo', 'redo'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' }
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Delete existing sub image
    document.querySelectorAll(".remove-sub-image").forEach(function(btn) {
        btn.addEventListener("click", function () {
            const wrapper = btn.closest(".sub-image-wrapper");
            if (wrapper) wrapper.remove();
        });
    });

    // Preview newly selected sub images
    document.getElementById('subImageInput').addEventListener('change', function (e) {
        const files = e.target.files;
        const previewContainer = e.target.parentElement;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const previewURL = URL.createObjectURL(file);

            const wrapper = document.createElement('div');
            wrapper.className = "sub-image-wrapper";
            wrapper.style = "display:inline-block; position:relative; margin-right:10px;";

            const img = document.createElement('img');
            img.src = previewURL;
            img.style = "max-width:100px; margin-top:10px;";
            
            wrapper.appendChild(img);
            previewContainer.appendChild(wrapper);
        }
    });
});
</script>




</body>
</html>
