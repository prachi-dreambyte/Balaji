<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $mobile  = htmlspecialchars($_POST['mobile']);
    $message = htmlspecialchars($_POST['message']);

    $sql = "INSERT INTO contact_form (name, email, mobile, message) 
            VALUES ('$name', '$email', '$mobile', '$message')";
    if (mysqli_query($conn, $sql)) {
        $adminNumber = "8979892185";
        $adminName = "Balaji Store";

        $whatsappMessage = urlencode("Namaste $adminName,\nName: $name\nEmail: $email\nMobile: $mobile\nMessage: $message");
        $whatsappURL = "https://wa.me/91$adminNumber?text=$whatsappMessage";
        echo $whatsappURL;
        exit;
    } else {
        echo "ERROR";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
          <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Home four || Vonia</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="img/favicon.ico" type="image/x-icon" rel="shortcut icon">
        <!-- Place favicon.ico in the root directory -->
		<!-- google font -->
		<link href='https://fonts.googleapis.com/css?family=Lato:400,100,300,700,900' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>
		<!-- all css here -->
		<!-- bootstrap v3.3.6 css -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
		<!-- animate css -->
        <link rel="stylesheet" href="css/animate.css">
		<!-- jquery-ui.min css -->
        <link rel="stylesheet" href="css/jquery-ui.min.css">
		<!-- meanmenu css -->
        <link rel="stylesheet" href="css/meanmenu.min.css">
		<!-- owl.carousel css -->
        <link rel="stylesheet" href="css/owl.carousel.css">
		<!-- font-awesome css -->
        <link rel="stylesheet" href="css/font-awesome.min.css">
		<!-- nivo-slider css -->
        <link rel="stylesheet" href="css/nivo-slider.css">
		<!-- style css -->
		<link rel="stylesheet" href="style.css">
		<!-- responsive css -->
        <link rel="stylesheet" href="css/responsive.css">
		<!-- modernizr css -->
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>

        <style>
  /* Force contact form inputs to full width */
  #contactForm input,
  #contactForm textarea {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }
</style>

</head>
<body>

<?php include 'header.php'; ?>

<!-- Hero Section -->
<div class="text-white text-center d-flex justify-content-center align-items-end"
     style="
       background: url('img/banner-contact.jpg') top center no-repeat;
       background-size: cover;
       width: 100%;
       height: 400px;
       padding-bottom: 30px;
     ">
  <h1 class="display-5 fw-bold bg-dark bg-opacity-50 px-4 py-2 rounded">
    Leave Us Your Info
  </h1>
</div>



<!-- Contact Form + Info Section Side-by-Side -->
<div class="container my-5">
  <div class="row g-4 justify-content-center">
    
    <!-- Contact Form (Left) -->
   <div class="col-md-6">
  <div class="card shadow" style="border-radius: 12px;">
    <div class="card-body">
      <h2 class="card-title text-center mb-4">Contact Form</h2>
      <form method="POST" id="contactForm">
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" name="name" class="form-control w-100" required style="border-radius: 6px;">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required style="border-radius: 6px;">
        </div>
        <div class="mb-3">
          <label for="mobile" class="form-label">Mobile</label>
          <input type="text" name="mobile" class="form-control" required style="border-radius: 6px;">
        </div>
        <div class="mb-3">
          <label for="message" class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="8" required style="border-radius: 6px;"></textarea>
        </div>
        <button type="submit" class="btn btn-dark w-100" style="border-radius: 6px;">Send via WhatsApp</button>
      </form>
    </div>
  </div>
</div>


    <!-- Info Block (Right) -->
   <div class="col-md-5">
  <div class="bg-white text-black rounded p-4 h-100 d-flex flex-column justify-content-center text-center shadow border">
    <h4 style="color:rgb(242, 130, 182);" class="fw-bold mb-3">Address</h4>

    <p class="mb-3">
      Jay Shri Balaji Foam & Furniture,<br>
      Opposite Mall Of Dehradun,<br>
      Near Miyawala Underpass,<br>
      Haridwar Road, Dehradun,<br>
      Uttarakhand - 248005
    </p>

    <h4 style="color:rgb(242, 130, 182);" class="fw-bold mb-3">Email id</h4>
    <p class="mb-3">Decorwithbalaji@gmail.com</p>

    <h4 style="color:rgb(242, 130, 182);" class="fw-bold mb-3">Phone</h4>
    <p class="mb-0">+91 89798 92185</p>
  </div>
</div>


  </div>
</div>


<!-- map Section -->
<div class="container-fluid mt-5 px-0">
  <h3 class="text-center mb-4">📍 Find Us on Map</h3>
  <iframe 
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d110273.84397830622!2d77.94002734335938!3d30.2639393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390929114f3328ff%3A0x3d6bafe374102ca9!2sBALAJI%20FURNITURE!5e0!3m2!1sen!2sin!4v1752133858868!5m2!1sen!2sin" 
    width="100%" 
    height="450" 
    style="border:0; border-radius: 10px; display: block;" 
    allowfullscreen="" 
    loading="lazy" 
    referrerpolicy="no-referrer-when-downgrade">
  </iframe>
</div>


<!-- JS Script -->
<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);

  fetch('', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(url => {
    if (url.startsWith('https://wa.me')) {
      window.open(url, '_blank');
      alert("✅ WhatsApp opened.");
    } else {
      alert("❌ Failed to send message.");
    }
  });
});
</script>

<!-- Bootstrap JS (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/vendor/jquery-1.12.4.min.js"></script>
		<!-- bootstrap js -->
        <script src="js/bootstrap.min.js"></script>
		<!--jquery scrollUp js -->
        <script src="js/jquery.scrollUp.js"></script>
		<!-- owl.carousel js -->
        <script src="js/owl.carousel.min.js"></script>
		<!-- meanmenu js -->
        <script src="js/jquery.meanmenu.js"></script>
		<!-- jquery-ui js -->
        <script src="js/jquery-ui.min.js"></script>
		<!-- wow js -->
        <script src="js/wow.min.js"></script>
		<!-- nivo slider js -->
        <script src="js/jquery.nivo.slider.pack.js"></script>
		<!-- countdown js -->
        <script src="js/countdown.js"></script>
		<!-- plugins js -->
        <script src="js/plugins.js"></script>
		<!-- main js -->
        <script src="js/main.js"></script>

<?php include 'footer.php';
?>

</body>
</html>
