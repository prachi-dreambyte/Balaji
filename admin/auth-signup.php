<?php
include "./db_connect.php";


$query = "CREATE TABLE IF NOT EXISTS admin_users (
     id INT AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(255) NOT NULL,
     email VARCHAR(255) NOT NULL UNIQUE,
     password VARCHAR(255) NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$conn->query($query)

?>




<!DOCTYPE html>
<html lang="en" class="h-100">


<!-- Mirrored from techzaa.in/larkon/admin/auth-signup.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 20 Mar 2025 09:20:09 GMT -->

<head>
     <!-- Title Meta -->
     <meta charset="utf-8" />
     <title>Sign In | Larkon - Responsive Admin Dashboard Template</title>
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

     <!-- SweetAlert2 CSS -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
     <!-- SweetAlert2 JS -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <style>
        .adminButton{
           background: #c06b81;
    color: #ffffff;
    font-size: 18px;
        }
        .adminButton:hover{
           background: #c06b81;
    color: #ffffff;
    font-size: 18px;
        }
        .adminInstruction{
             box-shadow: 0px 10px 10px rgba(3, 4, 28, 0.06);
             padding: 15px 9px;
             font-size: 18px;
             text-align: center;
             color: #c06b81;
        }
    </style>

</head>

<body class="h-100">
     <div class="d-flex flex-column h-100 p-3">
          <div class="d-flex flex-column flex-grow-1">
               <div class="row h-100">
                    <div class="col-xxl-7">
                         <div class="row justify-content-center h-100">
                              <div class="col-lg-6 py-lg-5">
                                   <div class="d-flex flex-column h-100 justify-content-center">
                                        <div class="auth-logo mb-4">
                                             <a href="index.php" class="logo-dark text-center">
                                                  <img src="assets\images\balaji-TOP-LOGO.png" height="124" alt="logo dark">
                                             </a>

                                             <a href="index.php" class="logo-light text-center">
                                                  <img src="assets\images\balaji-TOP-LOGO.png" height="124" alt="logo light">
                                             </a>
                                        </div>

                                        <h2 class="fw-bold fs-35 text-center">Sign Up</h2>

                                        <p class="mt-3 mb-3 adminInstruction">New to our platform? Sign up now! It only takes a minute</p>

                                        <div class="mb-5">
                                             <form method="POST" class="authentication-form">
                                                  <div class="mb-3 pt-3">
                                                       <label class="form-label fs-25 text-dark " for="example-name">Name</label>
                                                       <input type="name" id="example-name" name="example-name" class="form-control  fs-18" placeholder="Enter your name">
                                                  </div>
                                                  <div class="mb-3 pt-3">
                                                       <label class="form-label fs-25 text-dark" for="example-email">Email</label>
                                                       <input type="email" id="example-email" name="example-email" class="form-control fs-18" placeholder="Enter your email">
                                                  </div>
                                                  <div class="mb-3 pt-3">
                                                       <label class="form-label fs-25 text-dark" for="example-password">Password</label>
                                                       <input type="text" name="example-password" id="example-password" class="form-control fs-18" placeholder="Enter your password">
                                                  </div>
                                                 

                                                  <div class="mb-1 text-center d-grid">
                                                       <button class="btn adminButton" type="submit">Sign Up</button>
                                                  </div>
                                             </form>


                                        </div>

                                        <p class="fs-15 text-center">I already have an account <a href="auth-signin.php" class="text-dark fs-18 fw-bold ms-1">Sign In</a></p>
                                   </div>
                              </div>
                         </div>
                    </div>

                    <div class="col-xxl-5 d-none d-xxl-flex">
                         <div class="card h-100 mb-0 overflow-hidden">
                              <div class="d-flex flex-column h-100">
                                   <img src="assets\images\login.jpg" alt="" class="w-100 h-100">
                              </div>
                         </div> <!-- end card -->
                    </div>
               </div>
          </div>
     </div>

     <!-- Vendor Javascript (Require in all Page) -->
     <script src="assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="assets/js/app.js"></script>

</body>



</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     // Retrieve and sanitize form data
     try{
     $name     = trim($_POST['example-name'] ?? '');
     $email    = trim($_POST['example-email'] ?? '');
     $password = trim($_POST['example-password'] ?? '');
   
     // Initialize an array to hold any errors
     $errors = [];

     // Validate inputs
     if (empty($name)) {
          $errors[] = "Name is required.";
     }
     if (empty($email)) {
          $errors[] = "Email is required.";
     } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $errors[] = "Invalid email format.";
     }
     if (empty($password)) {
          $errors[] = "Password is required.";
     }
     

     if (!empty($errors)) {
          // Display errors using SweetAlert2
          echo "<script>
                Swal.fire({
                    title: 'Error!',
                    html: '" . implode('<br>', $errors) . "',
                    icon: 'error'
                });
              </script>";
     } else {
          // Hash the password for security
          $passwordHash = password_hash($password, PASSWORD_DEFAULT);

          // Prepare an SQL statement to insert the new user into the database
          $stmt = $conn->prepare("INSERT INTO admin_users (name, email, password) VALUES (?, ?, ?)");
          if ($stmt) {
               $stmt->bind_param("sss", $name, $email, $passwordHash);
               if ($stmt->execute()) {
                    // Success message
                    echo "<script>
                        Swal.fire({
                            title: 'Success!',
                            text: 'Sign Up Successful! You can now sign in.',
                            icon: 'success'
                        }).then(() => {
                            window.location.href = 'auth-signin.php';
                        });
                      </script>";
               } else {
                    // Error message
                    echo "<script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred. Please try again later.',
                            icon: 'error'
                        });
                      </script>";
               }
               $stmt->close();
          } else {
               // Error preparing statement
               echo "<script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred. Please try again later.',
                        icon: 'error'
                    });
                  </script>";
          }
     }
}catch (Exception $e) {
    error_log("Error occurred: " . $e->getMessage());
}


}
?>