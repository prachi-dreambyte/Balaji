<?php
session_start();
require_once 'connect.php';
require_once 'includes/coin_system.php';
require_once 'includes/session_timeout.php';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Balaji | Privacy Policy</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- FontAwesome -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

	<style>
		:root {
			--primary-color: #c06b81; /* Accent */
			--dark-bg: #363636; /* Dark background */
			--light-color: #ffffff; /* Light text */
		}

		body {
			background-color: var(--dark-bg);
			color: var(--light-color);
			font-family: Arial, sans-serif;
		}

		.page-header {
			background-color: var(--primary-color);
			color: var(--light-color);
			padding: 40px 0;
			text-align: center;
			font-weight: bold;
			font-size: 2rem;
			box-shadow: 0 2px 10px rgba(0,0,0,0.3);
		}

		.policy-container {
			background-color: rgba(255,255,255,0.1);
			border-radius: 10px;
			padding: 30px;
			margin: 30px auto;
			max-width: 900px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.4);
		}

		h2, h3 {
			color: var(--primary-color);
			font-weight: bold;
		}

		a {
			color: var(--primary-color);
			text-decoration: none;
		}

		a:hover {
			color: #ffb6c1;
			text-decoration: underline;
		}

		@media (max-width: 768px) {
			.page-header {
				font-size: 1.5rem;
				padding: 20px 0;
			}
			.policy-container {
				padding: 20px;
			}
		}
	</style>
</head>
<body>

	<div class="page-header">
		Privacy Policy
	</div>

	<div class="container">
		<div class="policy-container">
			<h2>Introduction</h2>
			<p>Your privacy is important to us. This Privacy Policy explains how Balaji collects, uses, and safeguards your personal information when you use our website.</p>

			<h3>Information We Collect</h3>
			<p>We may collect personal information including your name, email address, phone number, and payment details when you interact with our site or make a purchase.</p>

			<h3>How We Use Your Information</h3>
			<p>Your information is used to process transactions, improve our services, send important updates, and respond to your inquiries.</p>

			<h3>Data Protection</h3>
			<p>We implement strong security measures to protect your personal data from unauthorized access, alteration, disclosure, or destruction.</p>

			<h3>Cookies</h3>
			<p>Our site uses cookies to enhance user experience, track website performance, and remember your preferences. You can manage cookies through your browser settings.</p>

			<h3>Third-Party Sharing</h3>
			<p>We do not sell, rent, or trade your personal information. However, we may share data with trusted partners for service delivery purposes.</p>

			<h3>Changes to This Policy</h3>
			<p>We may update our Privacy Policy from time to time. Changes will be posted on this page with an updated effective date.</p>

			<h3>Contact Us</h3>
			<p>If you have any questions about this Privacy Policy, please contact us at <a href="mailto:info@balaji.com">info@balaji.com</a>.</p>
		</div>
	</div>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
