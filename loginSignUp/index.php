<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Signup code
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $user_type = $_POST['user_type'];

    $sql = "INSERT INTO users (username, email, password, user_type)
            VALUES ('$username', '$email', '$password', '$user_type')";

    if ($conn->query($sql) === TRUE) {
        echo "Signup successful!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Login code
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    $sql = "SELECT * FROM users WHERE email='$email' AND user_type='$user_type'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            echo "Welcome " . $user['username'] . " (" . $user['user_type'] . ")";
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login & Signup</title>
    <?php include 'style.css'; ?>

</head>
<body>

<h2>Signup</h2>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <select name="user_type" required>
        <option value="">Select User Type</option>
        <option value="corporate">Corporate User</option>
        <option value="personal">Personal User</option>
    </select>
    <input type="submit" name="signup" value="Sign Up" />
</form>

<h2>Login</h2>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <select name="user_type" required>
        <option value="">Select User Type</option>
        <option value="corporate">Corporate User</option>
        <option value="personal">Personal User</option>
    </select>
    <input type="submit" name="login" value="Login" />
</form>

</body>
</html>
