<?php
include 'connect.php';

echo "<h2>Database Debug Information</h2>";

// Check if signup table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'signup'");
if (mysqli_num_rows($table_check) == 0) {
    echo "<p style='color:red;'>❌ Table 'signup' does not exist!</p>";
    exit;
}

// Show table structure
echo "<h3>Table Structure:</h3>";
$structure = mysqli_query($conn, "DESCRIBE signup");
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($row = mysqli_fetch_assoc($structure)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show all users (without passwords for security)
echo "<h3>Registered Users:</h3>";
$users = mysqli_query($conn, "SELECT id, name, email, account_type, LENGTH(password) as pass_length FROM signup");
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Account Type</th><th>Password Length</th></tr>";
while ($row = mysqli_fetch_assoc($users)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['account_type'] . "</td>";
    echo "<td>" . $row['pass_length'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test password verification
echo "<h3>Password Verification Test:</h3>";
if (isset($_POST['test_email']) && isset($_POST['test_password'])) {
    $test_email = $_POST['test_email'];
    $test_password = $_POST['test_password'];
    
    $query = "SELECT * FROM signup WHERE email=? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $test_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        echo "<p>User found: " . $user['name'] . "</p>";
        echo "<p>Account type: " . $user['account_type'] . "</p>";
        echo "<p>Password hash: " . substr($user['password'], 0, 20) . "...</p>";
        
        if (password_verify($test_password, $user['password'])) {
            echo "<p style='color:green;'>✅ Password verification successful!</p>";
        } else {
            echo "<p style='color:red;'>❌ Password verification failed!</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ User not found with email: " . $test_email . "</p>";
    }
}
?>

<form method="POST">
    <h4>Test Password Verification:</h4>
    <p>Email: <input type="email" name="test_email" required></p>
    <p>Password: <input type="password" name="test_password" required></p>
    <p><input type="submit" value="Test Login"></p>
</form>

<p><a href="loginSignUp/login.php">Back to Login</a></p> 