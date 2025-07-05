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
            echo "<script>alert('✅ Signup Successful!'); window.location.href='login.php';</script>";
            exit;
        } else {
            echo "<script>alert('❌ Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Signup Page</title>
    <link rel="stylesheet" href="signup.css">
    
</head>
<body>

    <div class="signup-container">
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
        </form>
    </div>

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


