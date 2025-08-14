<?php
include 'db_connect.php';

if (!isset($_POST['id'])) {
    echo "<p class='text-danger'>Invalid request.</p>";
    exit;
}

$id = intval($_POST['id']);
$sql = "SELECT name, email, phone, company_name, account_type, address, gst, pan, website, created_at 
        FROM signup 
        WHERE id = $id LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<p class='text-danger'>Customer not found.</p>";
    exit;
}

$row = $result->fetch_assoc();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6"><strong>Name:</strong> <?= htmlspecialchars($row['name']); ?></div>
        <div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars($row['email']); ?></div>
        <div class="col-md-6"><strong>Phone:</strong> <?= htmlspecialchars($row['phone']); ?></div>
        <div class="col-md-6"><strong>Company:</strong> <?= htmlspecialchars($row['company_name']); ?></div>
        <div class="col-md-6"><strong>Account Type:</strong> <?= htmlspecialchars($row['account_type']); ?></div>
        <div class="col-md-6"><strong>Created At:</strong> <?= htmlspecialchars($row['created_at']); ?></div>

        <?php if (!empty($row['address'])) { ?>
            <div class="col-md-12"><strong>Address:</strong> <?= htmlspecialchars($row['address']); ?></div>
        <?php } ?>

        <?php if (!empty($row['gst'])) { ?>
            <div class="col-md-6"><strong>GST:</strong> <?= htmlspecialchars($row['gst']); ?></div>
        <?php } ?>

        <?php if (!empty($row['pan'])) { ?>
            <div class="col-md-6"><strong>PAN:</strong> <?= htmlspecialchars($row['pan']); ?></div>
        <?php } ?>

        <?php if (!empty($row['website'])) { ?>
            <div class="col-md-12"><strong>Website:</strong> <a href="<?= htmlspecialchars($row['website']); ?>"
                    target="_blank"><?= htmlspecialchars($row['website']); ?></a></div>
        <?php } ?>
    </div>
</div>