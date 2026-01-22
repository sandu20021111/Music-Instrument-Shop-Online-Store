<?php 
include 'db_connect.php'; 
include 'navbar.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --bg-light: #f8f9fa;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; color: #333; line-height: 1.8; }

        /* --- Page Header --- */
        .about-header {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)), 
                        url('upload/about1.jpg'); 
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 10%;
            text-align: center;
        }

        .about-header h1 { font-size: 3rem; margin: 0; font-weight: 700; }
        .about-header p { font-size: 1.1rem; opacity: 0.8; }

        /* --- Story Section --- */
        .story-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            padding: 80px 10%;
            align-items: center;
        }

        .story-img img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .story-content h2 { color: var(--primary); font-size: 2rem; margin-bottom: 20px; }

        /* --- Values Section --- */
        .values-section {
            background: var(--bg-light);
            padding: 80px 10%;
            text-align: center;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .value-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            transition: 0.3s;
        }

        .value-card:hover { transform: translateY(-10px); }

        .value-card i { font-size: 2.5rem; color: var(--accent); display: block; margin-bottom: 20px; }
        .value-card h3 { margin-bottom: 15px; color: var(--primary); }

        /* --- CTA Section --- */
        .contact-cta {
            padding: 80px 10%;
            text-align: center;
            background: var(--primary);
            color: white;
        }

        .cta-btn {
            display: inline-block;
            background: var(--accent);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .story-section { grid-template-columns: 1fr; text-align: center; }
        }
    </style>
</head>
<body>

    <header class="about-header">
        <h1>Our Musical Journey</h1>
        <p>Connecting soul to sound since 2010</p>
    </header>

    <section class="story-section">
        <div class="story-img">
            <img src="upload/about2.jpg" alt="Our Store">
        </div>
        <div class="story-content">
            <h2>Crafting the Perfect Sound</h2>
            <p>Melody Masters was started by a small group of music lovers. Today, we have become one of the leading musical instrument dealers in Sri Lanka due to the high quality products and service we provide to our customers.</p>
            <p>We are committed to being more than just a merchandise seller, but a partner in your musical journey.</p>
        </div>
    </section>

    <section class="values-section">
        <h2>Why Choose Us?</h2>
        <div class="values-grid">
            <div class="value-card">
                <h3>High Quality</h3>
                <p>We only stock the highest quality musical instruments from world-renowned brands.</p>
            </div>
            <div class="value-card">
                <h3>Expert Support</h3>
                <p>Our expert team helps you choose the right instrument for your needs.</p>
            </div>
            <div class="value-card">
                <h3>Fast Delivery</h3>
                <p>Fast and secure delivery options are available for all our products.</p>
            </div>
        </div>
    </section>

    <section class="contact-cta">
        <h2>Have Questions? We are here to help.</h2>
        <p>Visit our store or call us today.</p>
        <a href="contactus.php" class="cta-btn">Contact Us Now</a>
    </section>

    <?php include 'footer.php'; ?>

</body>
</html>