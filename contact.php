<?php
session_start();
include 'connect.php'; // your DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $mobile  = htmlspecialchars($_POST['mobile']);
    $message = htmlspecialchars($_POST['message']);

    // Save to database
    $sql = "INSERT INTO contact_form (name, email, mobile, message) 
            VALUES ('$name', '$email', '$mobile', '$message')";
    if (mysqli_query($conn, $sql)) {
        // WhatsApp link
        $adminNumber = "7291894699"; // Replace with your real WhatsApp number
        $adminName = "Balaji Store";

        $whatsappMessage = urlencode("Namaste $adminName,\nName: $name\nEmail: $email\nMobile: $mobile\nMessage: $message");
        $whatsappURL = "https://wa.me/91$adminNumber?text=$whatsappMessage";

         
       echo $whatsappURL;
    exit; // Important to stop PHP here
} else {
    echo "ERROR";
    exit;
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact - Balaji Store</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
      padding: 20px;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background: #fff;
      padding: 30px 20px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    label {
      font-weight: bold;
      margin-top: 15px;
      display: block;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
    }
    textarea { resize: vertical; min-height: 120px; }
    button {
      width: 100%;
      background-color: #28a745;
      color: white;
      padding: 12px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      margin-top: 20px;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background-color: #218838;
    }
    @media (max-width: 480px) {
      .container { padding: 20px 15px; }
      h2 { font-size: 22px; }
      input, textarea { font-size: 15px; }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>📩 Contact Balaji Store</h2>

  <form method="POST" id="contactForm">

    <label for="name">Your Name</label>
    <input type="text" name="name" required />

    <label for="email">Your Email</label>
    <input type="email" name="email" required />

    <label for="mobile">Mobile Number</label>
    <input type="text" name="mobile" required />

    <label for="message">Your Message</label>
    <textarea name="message" required></textarea>

    <button type="submit">Send on WhatsApp</button>
  </form>
</div>


<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
  e.preventDefault(); // Stop default form submission

  const form = e.target;
  const formData = new FormData(form);

  fetch('', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(url => {
    if (url.startsWith('https://wa.me')) {
      window.open(url, '_blank'); // Open WhatsApp
      window.location.href = 'index.php'; // Then redirect
    } else {
      alert('❌ Failed to send message.');
    }
  });
});
</script>


</body>
</html>
