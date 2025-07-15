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

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" href="signup.css"> <!-- Using the same CSS -->
</head>
<body>
    <section class="LoginPageSection">
        <div class="container">
            </div class="row">
                <div  class="col-md-6">
                 <div class="signup-container">
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
        <div style="text-align: center;">
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </div>
    </form>

    <!-- Commercial Login Form -->
    <form method="POST" id="commercialForm" class="signup-form">
        <input type="hidden" name="account_type" value="commercial">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <input type="submit" name = 'submit' value="Login">
        </div>
        <div style="text-align: center;">
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </div>
    </form>
</div>
                </div>
               <div  class="col-md-6">
                    <img src="img\balaji\b8b4210aefbd30f3d8c9a892024e6c0e.jpg"/>
                </div>
                </div>
            </div></div>
    </section>
    <div class="container">



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
