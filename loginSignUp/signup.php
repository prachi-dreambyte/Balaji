<?php
session_start();
include '../connect.php'; // your DB connection file

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_type = $_POST['account_type'];
    $name       = $_POST['name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $address    = $_POST['address'];
    $password   = $_POST['password'];
    $cpassword  = $_POST['cpassword'];
   
    $check_email_query = "SELECT * FROM signup WHERE email = '$email' AND account_type = '$account_type'";
    $check_result = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('❌ Email already exists. Please use a different email.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Check passwords match
    if ($password !== $cpassword) {
        $message = "❌ Passwords do not match.";
        echo $message;
        
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($account_type === 'personal') {
            $sql = "INSERT INTO signup (name, email, phone, address, password, account_type)
                    VALUES ('$name', '$email', '$phone', '$address', '$hashed_password', '$account_type')";
        } elseif ($account_type === 'commercial') {
            $gst      = $_POST['gst'];
            $pan      = $_POST['pan'];
            $website  = $_POST['website'];
            $company_name = $_POST['company_name'];

            $sql = "INSERT INTO signup (company_name, name, email, phone, address, gst, pan, website, password, account_type)
                    VALUES ('$company_name', '$name', '$email', '$phone', '$address', '$gst', '$pan', '$website', '$hashed_password', '$account_type')";
        }
         
        if (mysqli_query($conn, $sql)) {

            $user_id = mysqli_insert_id($conn);

            // Initialize wallet for new user
            include_once '../includes/coin_system.php';
            $coinSystem = new CoinSystem($conn);
            $coinSystem->initializeWallet($user_id, 50); // Give 50 welcome coins
            echo "<script>alert('✅ Signup Successful!'); window.location.href='login.php';</script>";
            exit;
        } else {
            echo "<script>alert('❌ Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>


    <html class="no-js" lang="">
        <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Login Page|| Balaji</title>
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
    <link rel="stylesheet" href="signup1.css"> 

        <link rel="stylesheet" href="blog-detail.css">
		<!-- responsive css -->
        <link rel="stylesheet" href="../css/responsive.css">
		<!-- modernizr js -->
        <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
         <!-- <link rel="stylesheet" href="signup.css"> -->
    
</head>
<body>

    <section class="LoginPageSection">
        <div class="container">
            <div class="row">
                
                <div class="col-md-6 col-lg-6 image-column">
                    <div class="signupImage">
                   <img src="../img/balaji/login.jpg"  class="LoginImg"/>
                </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="signup-container">
                        <div class="aboutUniversal">
                            <img src="../img/balaji-TOP-LOGO.png"  class="LoginImage"/>
                            <h1 class="py-4">SIGN UP</h1>
                            <div class="tab-buttons">
                                <button id="personalBtn" class="active" onclick="showForm('personal')">👤 Personal Signup</button>
                                  <button id="commercialBtn" onclick="showForm('commercial')">🏢 Commercial Signup</button>
                            </div>
                    

        <!-- Personal Signup Form -->
                    <form action="" method="POST" id="personalForm" class="signup-form active">

            <input type="hidden" name="account_type" value="personal">

            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" required>
            </div>

            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="address" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="cpassword" required>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" value="Signup">
            </div>

            <div class="form-group text-center" style="margin-top: 15px;">
    <a href="login.php" style="color: #2575fc; font-weight: bold; text-decoration: none;">
        ← Back to Login
    </a>
</div>
                    </form>

        <!-- Commercial Signup Form -->
                    <form action="" method="POST" id="commercialForm" class="signup-form">

            <input type="hidden" name="account_type" value="commercial">

            <div class="form-group">
                <label>Company Name:</label>
                <input type="text" name="company_name" required>
            </div>

            <div class="form-group">
                <label>Contact Person:</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" required>
            </div>

            <div class="form-group">
                <label>Business Address:</label>
                <input type="text" name="address" required>
            </div>

            <div class="form-group">
                <label>GST Number:</label>
                <input type="text" name="gst" required>
            </div>

            <div class="form-group">
                <label>PAN Number:</label>
                <input type="text" name="pan" required>
            </div>

            <div class="form-group">
                <label>Website (optional):</label>
                <input type="text" name="website">
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="cpassword" required>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" value="Signup">
            </div>
              
            <div class="form-group text-center" style="margin-top: 15px;">
    <a href="login.php" style="color: #2575fc; font-weight: bold; text-decoration: none;">
        ← Back to Login
    </a>
</div>

                    </form>
                </div>
</div>
</div>
                
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

</body>
</html>




