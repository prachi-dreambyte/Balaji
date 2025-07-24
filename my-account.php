<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connect.php';
$user_id = $_SESSION['user_id'];




// Fetch user data
$user_query = mysqli_query($conn, "SELECT * FROM signup WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);

// Save Address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_address'])) {
    $address_line = trim($_POST['address_line']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $zipcode = trim($_POST['zipcode']);

    if (!empty($address_line) && !empty($city) && !empty($state) && !empty($zipcode)) {
        $stmt = $conn->prepare("INSERT INTO addresses (user_id, address_line, city, state, zipcode) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $address_line, $city, $state, $zipcode);
        $stmt->execute();
        header("Location: my-account.php#address");
         exit;

    } else {
        echo "<script>alert('Please fill all fields.');</script>";
    }
}

// Delete Address
if (isset($_GET['delete_address'])) {
    $id = intval($_GET['delete_address']);
    $stmt = $conn->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    header("Location: my-account.php#address");
    exit;

}
?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>My Account || Vonia</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link href='https://fonts.googleapis.com/css?family=Lato:400,100,300,700,900' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" href="css/meanmenu.min.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/nivo-slider.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <style>
        .account-container { margin-top: 30px; }
        .sidebar { background: #f8f9fa; padding: 20px; height: 100%; border-right: 1px solid #ddd; }
        .sidebar a { display: block; padding: 10px; color: #333; text-decoration: none; }
        .sidebar a:hover { background: #ddd; }
        .content-section { display: none; }
        .content-section.active { display: block; }
    </style>
</head>
<body>

	<?php include 'header.php'; ?>

<div class="container account-container">
    <div class="row">
        <div class="col-md-3 sidebar">
            <script>
                function showSection(id) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(function(section){
        section.classList.remove('active');
    });

    // Show selected section
    var section = document.getElementById(id);
    if (section) {
        section.classList.add('active');

        // Update URL hash
        if (history.pushState) {
            history.pushState(null, null, '#' + id);
        } else {
            window.location.hash = '#' + id;
        }
    }
}

            </script>
            <h4>My Account</h4>
            <a href="javascript:void(0);" onclick="showSection('personal-info')">Personal Info</a>
            <a href="javascript:void(0);" onclick="showSection('my-orders')">My Orders</a>
            <a href="javascript:void(0);" onclick="showSection('wishlist')">Wishlist</a>
            <a href="javascript:void(0);" onclick="showSection('my-reviews')">My Reviews</a>
            <a href="javascript:void(0);" onclick="showSection('address')">Address</a>
            <a href="javascript:void(0);" onclick="showSection('support')">Support</a>
            <a href="?action=logout">Logout</a>

        </div>

        <div class="col-md-9">
            <div id="personal-info" class="content-section active">
                <h3>Personal Info</h3>
                <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
            </div>

            <div id="my-orders" class="content-section">
                <h3>My Orders</h3>
                <?php
                $orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
                if (mysqli_num_rows($orders) > 0) {
                    while ($order = mysqli_fetch_assoc($orders)) {
                        echo "<div><strong>Order ID:</strong> {$order['id']} | <strong>Total:</strong> ₹{$order['total_amount']} | <strong>Date:</strong> {$order['created_at']}</div><hr>";
                    }
                } else {
                    echo "<p>No orders found.</p>";
                }
                ?>
            </div>

            <div id="wishlist" class="content-section">
                <h3>Wishlist</h3>
                
            </div>

            <div id="my-reviews" class="content-section">
                <h3>My Reviews</h3>
                
            </div>

            <div id="address" class="content-section">
    <h3>Manage Address</h3>
    

    <!-- Add New Address Form -->

    <form method="POST">
        <div class="row">
            <div class="col-md-6">
                <label>Address Line</label>
                <input type="text" name="address_line" required class="form-control">
            </div>
            <div class="col-md-6">
                <label>City</label>
                <input type="text" name="city" required class="form-control">
            </div>
            <div class="col-md-6">
                <label>State</label>
                <input type="text" name="state" required class="form-control">
            </div>
            <div class="col-md-6">
                <label>Pincode</label>
                <input type="text" name="zipcode" required class="form-control">
            </div>
        </div>
        <br>
        <button type="submit" name="save_address" class="btn btn-primary">Save Address</button>
    </form>

    <hr>

    <!-- Saved Addresses -->
    <h4>Saved Addresses</h4>
    <?php
    $result = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id = $user_id");

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card p-3 mb-3'>";
            echo "<p><strong>Address:</strong> " . htmlspecialchars($row['address_line']) . "</p>";
            echo "<p><strong>City:</strong> " . htmlspecialchars($row['city']) . "</p>";
            echo "<p><strong>State:</strong> " . htmlspecialchars($row['state']) . "</p>";
            echo "<p><strong>Pincode:</strong> " . htmlspecialchars($row['zipcode']) . "</p>";
            echo "<a href='my-account.php?delete_address=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this address?\")'>Delete</a>";
            echo "</div>";
        }
    } else {
        echo "<p>No address found.</p>";
    }
    ?>
</div>

            <div id="support" class="content-section">
                <h3>Support</h3>
                <p>If you need help, contact us at <strong>support@voniafurniture.com</strong></p>
            </div>
        </div>
    </div>
</div>


    <?php include 'footer.php'; ?>

    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.scrollUp.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.meanmenu.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/jquery.nivo.slider.pack.js"></script>
    <script src="js/countdown.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

	

<script>
    

// Show section from URL hash on page load
document.addEventListener('DOMContentLoaded', function() {
    var hash = window.location.hash.substring(1);
    var validSections = ['personal-info', 'my-orders', 'wishlist', 'my-reviews', 'address', 'support'];
    if (hash && validSections.includes(hash)) {
        showSection(hash);
    } else {
        showSection('personal-info');
    }
});
</script>


    // On page load, check for hash and show corresponding section
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash.substring(1);
        const validSections = ['personal-info', 'my-orders', 'wishlist', 'my-reviews', 'address', 'support'];
        
        if (hash && validSections.includes(hash)) {
            showSection(hash);
        } else {
            // Default to personal info if no valid hash
            showSection('personal-info');
        }
    });
</scrip>


    <?php
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        session_destroy();
        header("Location: index.php");
        exit;
    }
    ?>
</body>

</html>
