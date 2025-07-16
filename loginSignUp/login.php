<?php
session_start();
include '../connect.php'; // database connection

$message = '';

// Show message once, then clear
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $account_type = $_POST['account_type'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!empty($account_type) && !empty($email) && !empty($password)) {
        // Use prepared statement to prevent SQL injection
        $query = "SELECT * FROM signup WHERE email=? AND account_type=? LIMIT 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $email, $account_type);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
       echo 'hello';
        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
           
            // Debug: Check if password field exists and has value
            if (isset($user['password']) && !empty($user['password'])) {
                echo $password;
                if (password_verify($password, $user['password'])) {
                    echo 'princeraj908071@gmail.com';
                    $_SESSION['user'] = $user;
                    echo "<script>alert('✅ Login successful!'); window.location.href='../index.php';</script>";
                    exit;
                } 
                else {
                    $_SESSION['message'] = "❌ Incorrect password.";
                    header("Location: login.php");
                    exit;
                }
            } 
            
            else {
                $_SESSION['message'] = "❌ Password field not found in database.";
                header("Location: login.php");
                exit;
            }
        } 
        else {
            $_SESSION['message'] = "❌ Account not found.";
            header("Location: login.php");
            exit;
        }
    } 
    else {
        $_SESSION['message'] = "❌ All fields are required.";
        header("Location: login.php");
        exit;
    }
}



?>

<html class="no-js" lang="">
<head> <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Login Page|| Vonia</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
        <!-- Place favicon.ico in the root directory -->
		<!-- google font -->
		<link href='https://fonts.googleapis.com/css?family=Lato:400,100,300,700,900' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>
		<!-- all css here -->
		<!-- bootstrap v3.3.6 css -->
        <link rel="stylesheet" href="../css/bootstrap.min.css">
		<!-- animate css -->
        <link rel="stylesheet" href="../css/animate.css">
		<!-- jquery-ui.min css -->
        <link rel="stylesheet" href="../css/jquery-ui.min.css">
		<!-- meanmenu css -->
        <link rel="stylesheet" href="../css/meanmenu.min.css">
		<!-- owl.carousel css -->
        <link rel="stylesheet" href="../css/owl.carousel.css">
		<!-- font-awesome css -->
        <link rel="stylesheet" href="../css/font-awesome.min.css">
		<!-- nivo-slider css -->
        <link rel="stylesheet" href="../css/nivo-slider.css">
		<!-- style css -->
		<link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="login.css"> 

        <link rel="stylesheet" href="blog-detail.css">
		<!-- responsive css -->
        <link rel="stylesheet" href="../css/responsive.css">
		<!-- modernizr js -->
        <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
<body>
    <section class="LoginPageSection">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="signup-container">
                        <div class="aboutUniversal">
                        <img src="../img/balaji-TOP-LOGO.png"  class="LoginImage"/>
                        <h1 class="py-4">LOGIN</h1>
                        <div class="tab-buttons">
                            <button id="personalBtn" class="active" onclick="showForm('personal')">👤 Personal Login</button>
                            <button id="commercialBtn" onclick="showForm('commercial')">🏢 Commercial Login</button>
                        </div>

                        <?php if (!empty($message)) : ?>
                        <p style="color:red;text-align:center;"><?= $message ?></p>
                        <?php endif; ?>

                        <!-- Personal Login Form -->
                        <form method="POST" id="personalForm" class="signup-form active">
                           <input type="hidden" name="account_type" value="personal">
                                <div class="form-group">
                                 <label>Email:</label>
                                  <input type="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label>Password:</label>
                                    <input type="password" name="password" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Login">
                                </div>
                                <div class="LoginAccount">
                             <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
                            </div>
                        </form>

                         <!-- Commercial Login Form -->
                        <form method="POST" id="commercialForm" class="signup-form">
                            <input type="hidden" name="account_type" value="commercial">
                            <div class="form-group">
                                <label class="loginLabel">Email:</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group">
                            <label class="loginLabel">Password:</label>
                                <input type="password" name="password" required>
                            </div>
                            <div class="form-group">
                               <input type="submit" name = 'submit' value="Login">
                            </div>
                            <div class="LoginAccount">
                             <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
                            </div>
                        </form>
                    </div>
                   </div>
                </div>
               <div class="col-md-6 col-lg-6 image-column">
               <div class="signupImage">
                   <img src="../img/balaji/login.jpg"  class="LoginImg"/>

                </div>
            </div>
        </div>
    </section>
<script>
    function showForm(type) {
        const personalForm = document.getElementById("personalForm");
        const commercialForm = document.getElementById("commercialForm");
        const personalBtn = document.getElementById("personalBtn");
        const commercialBtn = document.getElementById("commercialBtn");

        if (type === 'personal') {
            personalForm.classList.add("active");
            commercialForm.classList.remove("active");
            personalBtn.classList.add("active");
            commercialBtn.classList.remove("active");
        } else {
            commercialForm.classList.add("active");
            personalForm.classList.remove("active");
            commercialBtn.classList.add("active");
            personalBtn.classList.remove("active");
        }
    }
</script>

 <!-- modal end -->
		<!-- all js here -->
		<!-- jquery latest version -->
        <script src="../js/vendor/jquery-1.12.4.min.js"></script>
		<!-- bootstrap js -->
        <script src="../js/bootstrap.min.js"></script>
		<!--jquery scrollUp js -->
        <script src="../js/jquery.scrollUp.js"></script>
		<!-- owl.carousel js -->
        <script src="../js/owl.carousel.min.js"></script>
		<!-- meanmenu js -->
        <script src="../js/jquery.meanmenu.js"></script>
		<!-- jquery-ui js -->
        <script src="../js/jquery-ui.min.js"></script>
		<!-- wow js -->
        <script src="../js/wow.min.js"></script>
		<!-- nivo slider js -->
        <script src="../js/jquery.nivo.slider.pack.js"></script>
		<!-- countdown js -->
        <script src="../js/countdown.js"></script>
		<!-- plugins js -->
        <script src="../js/plugins.js"></script>
		<!-- main js -->
        <script src="../js/main.js"></script>

</body>
</html>
