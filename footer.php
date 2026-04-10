<style>
    :root {
        --footer-bg: #2c3e50;
        --footer-text: #bdc3c7;
        --footer-heading: #ffffff;
        --footer-accent: #3498db;
    }

    .main-footer {
        background-color: var(--footer-bg);
        color: var(--footer-text);
        padding: 60px 10% 30px 10%;
        font-family: 'Inter', sans-serif;
        margin-top: 50px;
    }

    .footer-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-section h3 {
        color: var(--footer-heading);
        font-size: 1.2rem;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .footer-section p {
        line-height: 1.6;
        font-size: 0.9rem;
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: var(--footer-text);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .footer-links a:hover {
        color: var(--footer-accent);
        padding-left: 5px;
    }

    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 15px;
    }

    .social-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: white;
        text-decoration: none;
        transition: background 0.3s;
    }

    .social-icon:hover {
        background: var(--footer-accent);
    }

    .footer-bottom {
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 25px;
        text-align: center;
        font-size: 0.85rem;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .main-footer {
            padding: 40px 5% 20px 5%;
            text-align: center;
        }
        .footer-links a:hover {
            padding-left: 0;
        }
        .social-links {
            justify-content: center;
        }
    }
</style>

<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3>Melody Masters</h3>
            <p>We offer all the high-quality musical instruments and services you need to make your musical dreams come true.</p>
            <div class="social-links">
                <a href="#" class="social-icon">F</a>
                <a href="#" class="social-icon">I</a>
                <a href="#" class="social-icon">Y</a>
            </div>
        </div>

        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop Instruments</a></li>
                <li><a href="aboutus.php">About Us</a></li>
                <li><a href="contactus.php">Contact Us</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Help & Support</h3>
            <ul class="footer-links">
                <li><a href="#">Track Order</a></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Return Policy</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Contact Us</h3>
            <p> No 123, Music Road, Colombo</p>
            <p> +94 112 345 678</p>
            <p> info@melodymasters.com</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Melody Masters. All Rights Reserved. Designed with </p>
    </div>
</footer>