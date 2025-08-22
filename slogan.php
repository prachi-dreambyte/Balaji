<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'connect.php';
?>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome 5 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
.slogan-section {
    background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
    padding: 101px 20px 5px 20px;
    text-align: center;
    position: relative;
}

.slogan-text {
    font-size: 42px;
    font-weight: 700;
    line-height: 1.4;
    font-family: 'Poppins', sans-serif;
    animation: fadeInZoom 2s ease forwards;
    opacity: 0;
}

/* Gradient effect for first part */
.slogan-text span:first-child {
    background: linear-gradient(90deg, #845848, #b88b5e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: block;   /* ek line me upar */
    margin-bottom: 15px;
}

/* Typing animation for second part */
.slogan-text .highlight {
    color: #333;
    display: inline-block;
    border-right: 3px solid #845848; /* cursor */
    overflow: hidden;
    white-space: nowrap;
    animation: typing 4s steps(14, end) infinite, blink 0.7s step-end infinite;
}

/* Typing effect */
@keyframes typing {
    0% { width: 0ch; }
    40% { width: 14ch; }   /* "Not Just Chair" = 14 characters */
    60% { width: 14ch; }
    100% { width: 0ch; }
}

/* Cursor blink */
@keyframes blink {
    50% { border-color: transparent; }
}

/* Zoom-in effect */
@keyframes fadeInZoom {
    0% { opacity: 0; transform: scale(0.9) translateY(30px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}
</style>

<section class="slogan-section">
    <div class="container">
        <h2 class="slogan-text">
            <span>We Sell Comfort</span>
            <span class="highlight">Not Just Chair</span>
        </h2>
    </div>
</section>
