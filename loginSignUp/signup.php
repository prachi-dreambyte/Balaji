<?php
include '../connect.php';

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
        <form action="signup_process.php" method="POST" id="personalForm" class="signup-form active">
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
        <form action="signup_process.php" method="POST" id="commercialForm" class="signup-form">
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
