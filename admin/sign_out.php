<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
     <!-- SweetAlert2 JS -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
</body>
</html>

<?php
session_destroy(); // Destroy all sessions

echo "<script>
    Swal.fire({
        icon: 'success',
        title: 'Logged Out!',
        text: 'You have been successfully logged out.',
        showConfirmButton: false,
        timer: 2000
    }).then(() => {
        window.location.href = 'auth-signin.php';
    });
</script>";
exit; 
?>
