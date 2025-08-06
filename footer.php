<style>
/* Footer Styles */
.pg-footer {
    font-family: 'Roboto', sans-serif;
    background-color: #363636;
    color: #fff;
    position: relative;
}

.footer-wave-svg {
    background-color: transparent;
    display: block;
    height: 50px;
    width: 100%;
}

.footer-wave-path {
    fill: #c06b81;
}

.footer-content {
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.footer-logo-link {
    display: inline-block;
    margin-bottom: 20px;
}

.footer-logo-link img {
    max-width: 100%;
    height: auto;
}

.footer-menu-name {
    color: #fff;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.footer-menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-menu-list li {
    margin-bottom: 10px;
}

.footer-menu-list li a {
    color: #ccc;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.footer-menu-list li a:hover {
    color: #c06b81;
}

.footer-menu-list li p {
    color: #ccc;
    font-size: 14px;
    line-height: 1.5;
    margin: 0;
}

.footer-call-to-action {
    margin-bottom: 20px;
}

.footer-call-to-action-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 10px;
}

.footer-call-to-action-description {
    color: #ccc;
    font-size: 14px;
    margin-bottom: 15px;
}

.footer-call-to-action-button {
    background-color: #c06b81;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.footer-call-to-action-button:hover {
    background-color: #e393a7;
}

.footer-social-links {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.footer-social-link {
    color: #fff;
    font-size: 20px;
    transition: color 0.3s;
}

.footer-social-link:hover {
    color: #c06b81;
}

.footer-copyright {
    background-color: #c06b81;
    padding: 15px 0;
    text-align: center;
}

.footer-copyright-text {
    color: #fff;
    font-size: 13px;
    margin: 0;
}

.footer-copyright-link {
    color: #fff;
    text-decoration: none;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .footer-menu-name {
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .footer-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .footer-logo-link {
        text-align: center;
    }
    
    .footer-menu {
        text-align: center;
    }
    
    .footer-social-links {
        justify-content: center;
    }
}
</style>

<footer class="pg-footer">
    <svg class="footer-wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 100" preserveAspectRatio="none">
        <path class="footer-wave-path" d="M851.8,100c125,0,288.3-45,348.2-64V0H0v44c3.7-1,7.3-1.9,11-2.9C80.7,22,151.7,10.8,223.5,6.3C276.7,2.9,330,4,383,9.8 c52.2,5.7,103.3,16.2,153.4,32.8C623.9,71.3,726.8,100,851.8,100z"></path>
    </svg>
    
    <div class="footer-content">
        <div class="footer-content-column">
            <div class="footer-logo">
                <a class="footer-logo-link" href="#">
                    <img src="./img/balaji-logo-top.png" alt="Balaji Furniture" class="img-fluid">
                </a>
            </div>
            <div class="footer-menu">
                <h2 class="footer-menu-name">Shop Location</h2>
                <ul class="footer-menu-list">
                    <li>
                        <p>Jay Shri Balaji Foam & Furniture, Opposite Mall Of Dehradun, Near Miyawala Underpass, Haridwar Road, Dehradun, Uttarakhand-248005</p>
                    </li>
                    <li>
                        <p>+91-8979892185</p>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-content-column">
            <div class="footer-menu">
                <h2 class="footer-menu-name">Information</h2>
                <ul class="footer-menu-list">
                    <li><a href="index.php#deals">Daily Deals</a></li>
                    <li><a href="index.php#newarrival">New Products</a></li>
                    <li><a href="index.php#bestseller">Bestseller</a></li>
                </ul>
            </div>
            
            <div class="footer-menu">
                <h2 class="footer-menu-name">Legal</h2>
                <ul class="footer-menu-list">
                    <li><a href="#">Privacy Notice</a></li>
                    <li><a href="#">Terms of Use</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-content-column">
            <div class="footer-menu">
                <h2 class="footer-menu-name">Quick Links</h2>
                <ul class="footer-menu-list">
                    <li><a href="about-us.php">About Us</a></li>
                    <li><a href="shop.php">Category</a></li>
                    <li><a href="index.php#deals">Offer</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="#">Customers</a></li>
                    <li><a href="#">Reviews</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-content-column">
            <div class="footer-call-to-action">
                <h2 class="footer-call-to-action-title">Let's Chat</h2>
                <p class="footer-call-to-action-description">Have a support question?</p>
                <a class="footer-call-to-action-button" href="contact.php">Get in Touch</a>
            </div>
            
            <div class="footer-call-to-action">
                <h2 class="footer-call-to-action-title">Call Us</h2>
                <p><a href="tel:+918979892185" class="footer-menu-list">+91-8979892185</a></p>
            </div>
            
            <div class="footer-social-links">
                <a href="#" class="footer-social-link"><i class="bi bi-facebook"></i></a>
                <a href="#" class="footer-social-link"><i class="bi bi-twitter"></i></a>
                <a href="#" class="footer-social-link"><i class="bi bi-instagram"></i></a>
                <a href="#" class="footer-social-link"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div>
    
    <div class="footer-copyright">
        <div class="footer-copyright-wrapper">
            <p class="footer-copyright-text">
                <a class="footer-copyright-link" href="#">© Copyright 2024 Balaji Furniture | powered by Dreambyte Solution Pvt.Ltd</a>
            </p>
        </div>
    </div>
</footer>

<!-- Add this in your head section for Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">