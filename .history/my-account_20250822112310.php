<style>
    .Account-body{
            /* border: 1px solid; */
    padding: 15px 30px;
    margin-bottom: 20px;
    box-shadow: 2px 5px 22px #e9ecef
    }
    .account-para{
        font-size: 18px;
    color: #555;;
    font-weight: 400;
    }
</style>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: loginSignUp/login.php");
    exit;
}

include 'connect.php';
$user_id = $_SESSION['user_id'];

// Create addresses table if it doesn't exist
$createTableQuery = "CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    zipcode VARCHAR(20) NOT NULL,
    contact_no VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES signup(id) ON DELETE CASCADE
)";
$conn->query($createTableQuery);

// Fetch user data
$user_query = mysqli_query($conn, "SELECT * FROM signup WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);


// =====================
// Save Address
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_address'])) {
    $address_line = trim($_POST['address_line']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $zipcode = trim($_POST['zipcode']);
    $contact_no = trim($_POST['contact_no']);

    if (!empty($address_line) && !empty($city) && !empty($state) && !empty($zipcode) && !empty($contact_no)) {

        // ✅ Restrict shipping only to Uttarakhand
        if (strtolower($state) !== "uttarakhand") {
            echo "<script>alert('⚠️ Sorry! We only ship within Uttarakhand.'); window.history.back();</script>";
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO addresses (user_id, address_line, city, state, zipcode, contact_no) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $address_line, $city, $state, $zipcode, $contact_no);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: my-account.php#address");
            exit;
        } else {
            echo "<script>alert('❌ Failed to save address: " . $stmt->error . "');</script>";
            $stmt->close();
        }
    } else {
        echo "<script>alert('⚠️ Please fill in all fields.');</script>";
    }
}

// =====================
// Delete Address
// =====================
if (isset($_GET['delete_address'])) {
    $delete_id = intval($_GET['delete_address']);

    // Validate delete_id and user_id
    if ($delete_id > 0 && $user_id) {
        $stmt = $conn->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $delete_id, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: my-account.php#address");
            exit;
        } else {
            echo "<script>alert('❌ Failed to delete address: " . $stmt->error . "');</script>";
            $stmt->close();
        }
    } else {
        echo "<script>alert('⚠️ Invalid delete request.');</script>";
    }
}



// Logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>My Account Balaji</title>
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
        /* Base Styles & Typography */
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Lato', sans-serif;
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 0.5em;
        }

        p {
            margin-bottom: 1em;
        }

        /* Main Account Wrapper */
        .account-wrapper {
            margin-top: 50px;
            /* Increased margin-top for better spacing from header */
            margin-bottom: 50px;
            /* Increased margin-bottom */
            background-color: #ffffff;
            border-radius: 12px;
            /* Slightly more rounded corners */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            /* Stronger, softer shadow */
            padding: 0;
            /* Remove padding from wrapper to manage within sidebar/content */
            overflow: hidden;
            /* Ensures border-radius applies correctly */
        }

        /* Sidebar Navigation */
        .account-sidebar {
            background: #fdfdfd;
            padding: 30px 0;
            /* Increased vertical padding */
            border-right: 1px solid #eee;
            border-radius: 12px 0 0 12px;
            /* Matches wrapper radius */
        }

        .account-sidebar h4 {
            font-size: 26px;
            /* Slightly larger */
            color: #2c3e50;
            padding: 15px 30px;
            /* Increased horizontal padding */
            margin-bottom: 25px;
            /* More space below heading */
            border-bottom: 1px solid #eee;
            font-weight: 700;
            /* Bolder */
        }

        .account-sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 18px 30px;
            /* More padding for larger clickable area */
            color: #555;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            /* Bolder for better visibility */
            font-size: 17px;
            /* Slightly larger font */
            border-left: 5px solid transparent;
            /* Prepare for active border */
        }

        .account-sidebar .nav-link i {
            margin-right: 15px;
            /* More space between icon and text */
            font-size: 20px;
            /* Larger icons */
            color: #888;
        }

        .account-sidebar .nav-link:hover {
            background-color: #f1f3f5;
            /* Lighter hover background */
            color: #845848;
            /* Primary color on hover */
            border-left-color: #845848;
            /* Active border color on hover */
        }

        .account-sidebar .nav-link.active {
            background-color: #e9ecef;
            color: #845848;
            /* Primary color for active state */
            border-left: 5px solid #845848;
            /* Accent border */
        }

        .account-sidebar .nav-link.active i {
            color: #845848;
        }

        /* Content Area */
        .account-content {
            padding: 30px 40px;
            /* More padding for content */
            border-radius: 0 12px 12px 0;
            /* Matches wrapper radius */
        }

        .account-content .section-header {
            font-size: 32px;
            /* Larger heading */
            color: black;
            margin-bottom: 35px;
            /* More space below header */
            padding-bottom: 15px;
            border-bottom: 3px solid #845848;
            /* Thicker accent border */
            font-weight: 700;
        }

        .content-section {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }

        .content-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            /* Stronger translate effect */
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Personal Info Section */
        /* Personal Info Section */
        .personal-info {
            background-color: #f9f9f9;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            font-family: 'Segoe UI', sans-serif;
        }

        .personal-info p {
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            line-height: 1.6;
            color: #34495e;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .personal-info p:last-child {
            border-bottom: none;
        }

        .personal-info p strong {
            color: #2c3e50;
            min-width: 160px;
            display: inline-block;
            font-weight: 600;
        }


        /* Orders Section - Updated UI */
        .order-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .order-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #e9ecef;
            flex-wrap: wrap;
            /* Allows wrapping on smaller screens */
            gap: 10px;
        }

        .order-card-header h5 {
            font-size: 20px;
            color: #845848;
            margin: 0;
            flex-grow: 1;
        }

        .order-card-header .order-date,
        .order-card-header .order-total {
            font-size: 16px;
            color: #555;
            font-weight: 600;
        }

        .order-card-header .order-total {
            color: #28a745;
            /* Green for total amount */
        }

        .order-card-body p {
            margin-bottom: 8px;
            font-size: 15px;
        }

        .order-card-body p strong {
            color: #333;
            min-width: 100px;
            /* For alignment */
            display: inline-block;
        }

        .order-card-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            text-align: right;
        }

        .order-card-footer .btn-primary {
            background-color: #845848;
            border-color: #845848;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .order-card-footer .btn-primary:hover {
            background-color: #a85d71;
            border-color: #a85d71;
            transform: translateY(-2px);
        }

        /* Addresses Section */
        .address-form {
            background-color: #fdfdfd;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 40px;
            /* Space below the form */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        .address-form h4 {
            font-size: 22px;
            /* Slightly larger heading for "Add New Address" */
            margin-bottom: 25px;
            color: #2c3e50;
            font-weight: 600;
        }

        .address-form .form-group {
            margin-bottom: 25px;
            /* More spacing between form groups */
        }

        .address-form label {
            font-weight: 600;
            color: #444;
            margin-bottom: 10px;
            /* More space below label */
            display: block;
            /* Ensure label is on its own line */
            font-size: 15px;
        }

        .address-form .form-control {
            border-radius: 6px;
            /* Slightly more rounded inputs */
            border: 1px solid #d4d8dc;
            /* Slightly darker border */
            padding: 12px 18px;
            /* More padding inside inputs */
            font-size: 16px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
            /* Subtle inner shadow */
        }

        .address-form .form-control:focus {
            border-color: #845848;
            box-shadow: 0 0 0 0.2rem rgba(192, 107, 129, 0.25);
            /* Focus ring with primary color */
        }

        .address-form .btn-primary {
            background-color: #845848;
            border-color: #845848;
            padding: 12px 30px;
            /* Larger button */
            border-radius: 6px;
            font-weight: 600;
            font-size: 18px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .address-form .btn-primary:hover {
            background-color: #a85d71;
            /* Darker shade on hover */
            border-color: #a85d71;
            transform: translateY(-2px);
            /* Slight lift effect */
        }

        .saved-address-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            /* More rounded card corners */
            padding: 25px;
            /* More internal padding */
            margin-bottom: 25px;
            /* More spacing between cards */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            /* Stronger card shadow */
            position: relative;
            transition: transform 0.2s ease;
            /* Add transition for hover effect */
        }

        .saved-address-card:hover {
            transform: translateY(-3px);
            /* Slight lift on hover */
        }

        .saved-address-card p {
            margin-bottom: 10px;
            /* More spacing between lines in card */
            font-size: 16px;
            line-height: 1.5;
        }

        .saved-address-card p strong {
            color: #333;
            width: 110px;
            /* Increased width for alignment */
            display: inline-block;
            font-weight: 600;
        }

        .saved-address-card .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            padding: 8px 16px;
            /* Larger delete button */
            font-size: 15px;
            /* Larger font for delete button */
            border-radius: 5px;
            position: absolute;
            top: 25px;
            /* Aligns with padding */
            right: 25px;
            /* Aligns with padding */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .saved-address-card .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-1px);
        }

        /* Horizontal Rule */
        hr.my-5 {
            border-top: 1px dashed #e0e0e0;
            /* Dashed line for subtle separation */
            margin-top: 50px !important;
            margin-bottom: 50px !important;
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            /* More padding */
            color: #6c757d;
            font-size: 18px;
            background-color: #f8f9fa;
            border-radius: 10px;
            border: 1px dashed #e0e0e0;
            margin-top: 30px;
        }

        .empty-state i {
            font-size: 60px;
            /* Larger icons */
            margin-bottom: 25px;
            /* More space below icon */
            color: #adb5bd;
        }

        .empty-state p {
            font-size: 18px;
            /* Larger text for empty state */
            color: #555;
            line-height: 1.5;
        }

        /* Responsive Adjustments */
        @media (max-width: 991.98px) {

            /* Target tablets and smaller desktops */
            .account-wrapper {
                margin-top: 30px;
                margin-bottom: 30px;
                padding: 0;
                /* Remove padding for smaller screens as columns handle it */
                box-shadow: none;
                /* Remove shadow to simplify layout on smaller screens */
                border-radius: 0;
                /* Remove border radius */
            }

            .account-sidebar {
                border-right: none;
                border-bottom: 1px solid #eee;
                border-radius: 0;
                /* Remove border radius */
                padding: 20px 0;
            }

            .account-sidebar h4 {
                text-align: center;
                margin-bottom: 15px;
                padding: 10px 15px;
            }

            .account-sidebar .nav-link {
                justify-content: flex-start;
                /* Align left */
                padding: 12px 20px;
                font-size: 16px;
                border-left: none;
                /* No border on left for collapsed view */
                border-bottom: 3px solid transparent;
                /* Use bottom border for active state */
            }

            .account-sidebar .nav-link.active {
                border-left: none;
                border-bottom: 3px solid #845848;
                /* Active bottom border */
            }

            .account-sidebar .nav-link:hover {
                border-left-color: transparent;
                /* No left border on hover */
                border-bottom-color: #845848;
                /* Hover bottom border */
            }

            .account-sidebar i {
                margin-right: 10px;
            }
        }

        @media (max-width: 767.98px) {

            /* Target mobile devices */
            .account-sidebar {
                padding: 15px 0;
                display: flex;
                /* Make sidebar items display as a row */
                overflow-x: auto;
                /* Enable horizontal scrolling */
                -webkit-overflow-scrolling: touch;
                /* Smooth scrolling on iOS */
                white-space: nowrap;
                /* Prevent items from wrapping */
                justify-content: flex-start;
                border-bottom: 1px solid #eee;
            }

            .account-sidebar h4 {
                display: none;
                /* Hide 'My Account' title on very small screens */
            }

            .account-sidebar .nav-link {
                flex-shrink: 0;
                /* Prevent items from shrinking */
                padding: 10px 15px;
                font-size: 14px;
                white-space: nowrap;
                flex-direction: column;
                /* Stack icon and text */
                text-align: center;
                gap: 5px;
                /* Space between icon and text */
                border-bottom: 3px solid transparent;
                /* Maintain active/hover indicator */
            }

            .account-sidebar .nav-link i {
                margin-right: 0;
                margin-bottom: 5px;
                /* Space between icon and text */
                font-size: 18px;
            }

            .account-content {
                padding: 20px 15px;
                border-radius: 0;
                /* Remove border radius */
            }

            .account-content .section-header {
                font-size: 26px;
                margin-bottom: 25px;
                padding-bottom: 10px;
            }

            .address-form {
                padding: 20px;
            }

            .address-form h4 {
                font-size: 20px;
                margin-bottom: 20px;
            }

            .saved-address-card {
                padding: 20px;
                margin-bottom: 15px;
            }

            .saved-address-card .btn-danger {
                position: static;
                /* Position normally within flow */
                margin-top: 15px;
                display: block;
                /* Make button full width */
                width: 100%;
                text-align: center;
            }

            .order-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .order-card-header h5 {
                width: 100%;
            }

            .order-card-footer {
                text-align: center;
                /* Center the button on small screens */
            }

            .empty-state {
                padding: 30px 15px;
                font-size: 16px;
            }

            .empty-state i {
                font-size: 40px;
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container account-wrapper">
        <div class="row no-gutters">
            <div class="col-lg-3 col-md-4 account-sidebar">
                <h4>My Account</h4>
                <a href="#" class="nav-link active" data-section="personal-info">
                    <i class="fa fa-user"></i> Personal Info
                </a>
                <a href="#" class="nav-link" data-section="my-orders">
                    <i class="fa fa-shopping-bag"></i> My Orders
                </a>
                <a href="./wishlist.php" class="nav-link" data-section="wishlist">
                    <i class="fa fa-heart"></i> Wishlist
                </a>
                <a href="#" class="nav-link" data-section="my-reviews">
                    <i class="fa fa-star"></i> My Reviews
                </a>
                <a href="#" class="nav-link" data-section="address">
                    <i class="fa fa-map-marker"></i> Address
                </a>
                <a href="#" class="nav-link" data-section="support">
                    <i class="fa fa-life-ring"></i> Support
                </a>
                <a href="?action=logout" class="nav-link">
                    <i class="fa fa-sign-out"></i> Logout
                </a>
            </div>

            <div class="col-lg-9 col-md-8 account-content">
                <div id="personal-info" class="content-section active">
                    <h3 class="section-header">Personal Information</h3>
                    <div class="personal-info">
                        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>

                        <?php if (isset($user['account_type']) && $user['account_type'] === 'commercial'): ?>
                            <p><strong>Company Name:</strong>
                                <?= !empty($user['company_name']) ? htmlspecialchars($user['company_name']) : 'Not Provided' ?>
                            </p>
                            <p><strong>GST Number:</strong>
                                <?= !empty($user['gst']) ? htmlspecialchars($user['gst']) : 'Not Provided' ?></p>
                            <p><strong>PAN Number:</strong>
                                <?= !empty($user['pan']) ? htmlspecialchars($user['pan']) : 'Not Provided' ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="my-orders" class="content-section">
                    <h3 class="section-header">My Orders</h3>
                    <?php
                    // Prepare the SQL statement to prevent SQL injection
                    $stmt = $conn->prepare("SELECT id, order_id, amount, discount, status, payment_status, payment_method, created_at, address_line, city, state, zipcode, contact_no FROM orders WHERE user_id = ? AND payment_status = 'paid' ORDER BY created_at DESC");
                    $stmt->bind_param("i", $user_id); // "i" means the variable is an integer
                    $stmt->execute();
                    $orders_query = $stmt->get_result();

                    if ($orders_query->num_rows > 0) {
                        while ($order = $orders_query->fetch_assoc()) {
                            ?>
                            <div class='order-card'>
                                <div class='order-card-header'>
                                    <h5>Order ID: #<strong><?= htmlspecialchars($order['order_id']) ?></strong></h5>
                                    <span class='order-date'>Placed On:
                                        <?= date("M d, Y H:i A", strtotime($order['created_at'])) ?></span>
                                    <span class='order-total'>Total: ₹<?= number_format($order['amount'], 2) ?></span>
                                </div>

                                <div class='order-card-body'>
                                    <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($order['status'])) ?></p>
                                    <p><strong>Payment Status:</strong>
                                        <?= htmlspecialchars(ucfirst($order['payment_status'])) ?>
                                        (<?= htmlspecialchars($order['payment_method']) ?>)</p>
                                    <p><strong>Delivery To:</strong>
                                        <?= htmlspecialchars($order['address_line'] . ", " . $order['city'] . ", " . $order['state'] . " - " . $order['zipcode']) ?>
                                    </p>
                                    <p><strong>Contact:</strong> <?= htmlspecialchars($order['contact_no']) ?></p>
                                </div>

                                <div class='order-card-footer'>
                                    <a href='order-details.php?order_id=<?= urlencode($order['order_id']) ?>'
                                        class='btn btn-primary'>View Details</a>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<div class='empty-state'><i class='fa fa-shopping-basket'></i><p>No paid orders found. Start shopping!</p></div>";
                    }

                    $stmt->close();
                    ?>
                </div>


                <div id="my-reviews" class="content-section">
                    <h3 class="section-header">My Reviews</h3>
                    
                    <div class="empty-state">
    <i class="fa fa-comments-o"></i>
    <?php
    include 'connect.php'; // or adjust the path if already included above

    if (isset($_SESSION['user']['name'])) {
        $username = $_SESSION['user']['name'];

        $stmt = $conn->prepare("SELECT review_text, rating, created_at FROM reviews WHERE user_name = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($review = $result->fetch_assoc()) {
                echo "<div class='review-item'>";
                echo "<p><strong>Review:</strong> " . htmlspecialchars($review['review_text']) . "</p>";
                echo "<p><strong>Rating:</strong> " . str_repeat("★", (int)$review['rating']) . "</p>";
                echo "<p><strong>Date:</strong> " . date("d M Y, h:i A", strtotime($review['created_at'])) . "</p>";
                echo "<hr>";
                echo "</div>";
            }
        } else {
            echo "<p>You haven't submitted any reviews yet.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>⚠️ User not logged in.</p>";
    }
    ?>
</div>

                </div>

              <div id="address" class="content-section">
    <h3 class="section-header">Manage Addresses</h3>
<div class="address-form">
    <h4>Add New Address</h4>
    <form method="POST" id="addressForm">
        <input type="hidden" name="save_address" value="1">
        <div class="row">

            <!-- Pincode First -->
            <div class="col-md-6 form-group">
                <label for="zipcode">Pincode</label>
                <input type="text" id="zipcode" name="zipcode" required class="form-control" maxlength="6">
                <small id="pincode-msg" class="text-danger"></small>
            </div>

            <div class="col-md-6 form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required class="form-control" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" required class="form-control" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="address_line">Address Line</label>
                <input type="text" id="address_line" name="address_line" required class="form-control">
            </div>

            <div class="col-md-6 form-group">
                <label for="contact_no">Contact No.</label>
                <input type="text" id="contact_no" name="contact_no" required class="form-control">
            </div>
        </div>
        <button type="submit" name="save_address" id="saveBtn" class="btn btn-primary mt-3" disabled>
            Save Address
        </button>
    </form>
</div>

    <hr class="my-5">

    <h4>Saved Addresses</h4>
    <?php
                $stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='saved-address-card'>";
                        echo "<p><strong>Address:</strong> " . htmlspecialchars($row['address_line']) . "</p>";
                        echo "<p><strong>City:</strong> " . htmlspecialchars($row['city']) . "</p>";
                        echo "<p><strong>State:</strong> " . htmlspecialchars($row['state']) . "</p>";
                        echo "<p><strong>Pincode:</strong> " . htmlspecialchars($row['zipcode']) . "</p>";
                        echo "<p><strong>Phone No.:</strong> " . htmlspecialchars($row['contact_no']) . "</p>";
                        echo "<a href='my-account.php?delete_address=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this address?\")'>Delete</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='empty-state'><i class='fa fa-map-marker'></i><p>No saved addresses. Add one to speed up checkout!</p></div>";
                }

                $stmt->close();
                ?>
            </div>


                <div id="support" class="content-section">

                    <h3 class="section-header">How Can We Help You?</h3>
                    <div class="Account-body">
                    <h3>Conatct Us:</h3>
                     <p class="mb-1 account-para">+91-8979892185</p></div>
                     <div class="Account-body">
                     <h3>Address:</Address></h1>
                     <p class="mb-1 account-para">Jay Shri Balaji Foam & Furniture, Opp. Mall Of Dehradun, Near Miyawala Underpass, Haridwar Road, Dehradun, Uttarakhand-248005</p></div>
                     <div class="Account-body">
                     <h3>Email:</h3><p class="mb-1 account-para">Balajidecor@gmail.com</p></div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const navLinks = document.querySelectorAll('.account-sidebar .nav-link');
        const contentSections = document.querySelectorAll('.content-section');

        function showSection(id) {
            // Hide all sections
            contentSections.forEach(function (section) {
                section.classList.remove('active');
            });

            // Remove active class from all nav links
            navLinks.forEach(function (link) {
                link.classList.remove('active');
            });

            // Show selected section
            const section = document.getElementById(id);
            if (section) {
                section.classList.add('active');

                // Add active class to corresponding nav link
                const targetLink = document.querySelector(`.account-sidebar .nav-link[data-section="${id}"]`);
                if (targetLink) {
                    targetLink.classList.add('active');
                }

                // Update URL hash without page reload
                if (history.pushState) {
                    history.pushState(null, null, '#' + id);
                } else {
                    window.location.hash = '#' + id;
                }
            } else {
                console.warn(`Section with ID '${id}' not found.`);
            }
        }

        // Event listeners for navigation links
        navLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                const sectionId = this.getAttribute('data-section');
                const href = this.getAttribute('href');

                if (sectionId && href === '#') {
                    e.preventDefault();
                    showSection(sectionId);
                }
            });
        });

        // Show section from URL hash on page load
        const hash = window.location.hash.substring(1);
        const validSections = ['personal-info', 'my-orders', 'wishlist', 'my-reviews', 'address', 'support'];

        if (hash && validSections.includes(hash)) {
            showSection(hash);
        } else {
            showSection('personal-info');
        }

        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading state for form submissions
        // document.querySelectorAll('form').forEach(form => {
        //     form.addEventListener('submit', function (e) {
        //         e.preventDefault;
        //         console.log('Form submitted:');
        //         const submitBtn = this.querySelector('button[type="submit"]');
        //         if (submitBtn) {
        //             submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
        //             submitBtn.disabled = true;
        //         }
        //     });
        // });
    });
</script>
<script>
document.getElementById("zipcode").addEventListener("blur", function () {
    let pincode = this.value.trim();
    if (pincode.length === 6) {
        fetch("https://api.postalpincode.in/pincode/" + pincode)
            .then(response => response.json())
            .then(data => {
                if (data[0].Status === "Success") {
                    let postOffice = data[0].PostOffice[0];
                    document.getElementById("city").value = postOffice.District;
                    document.getElementById("state").value = postOffice.State;

                    // Check if Uttarakhand
                    if (postOffice.State.toLowerCase() !== "uttarakhand") {
                        alert("⚠️ Sorry! We only ship within Uttarakhand.");
                        document.getElementById("state").value = "";
                        document.getElementById("city").value = "";
                        this.value = "";
                        document.getElementById("saveBtn").disabled = true;
                    } else {
                        document.getElementById("saveBtn").disabled = false;
                    }
                } else {
                    alert("⚠️ Invalid Pincode. Please enter a valid one.");
                    document.getElementById("city").value = "";
                    document.getElementById("state").value = "";
                    document.getElementById("saveBtn").disabled = true;
                }
            })
            .catch(() => {
                alert("❌ Failed to fetch location details. Try again.");
                document.getElementById("saveBtn").disabled = true;
            });
    } else {
        document.getElementById("city").value = "";
        document.getElementById("state").value = "";
        document.getElementById("saveBtn").disabled = true;
    }
});
</script>



</body>

</html>