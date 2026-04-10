<?php 
include 'db_connect.php'; 
include 'navbar.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --text-slate: #64748b;
            --glass: rgba(255, 255, 255, 0.7);
            --transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            color: var(--primary); 
            line-height: 1.7;
            background: #fdfdfd;
        }

        /* --- Hero Section --- */
        .about-hero {
            height: 70vh;
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.8)), 
                        url('upload/about1.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 0 10%;
        }

        .about-hero h1 { 
            font-size: clamp(2.5rem, 6vw, 4rem); 
            font-weight: 800; 
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .about-hero p { 
            font-size: 1.2rem; 
            max-width: 600px;
            opacity: 0.9;
            font-weight: 400;
        }

        /* --- Story Section --- */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 5%;
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .story-image-box {
            position: relative;
        }

        .story-image-box img {
            width: 100%;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .experience-badge {
            position: absolute;
            bottom: -30px;
            right: -30px;
            background: var(--accent);
            color: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.3);
        }

        .experience-badge span { font-size: 2.5rem; font-weight: 800; display: block; line-height: 1; }
        .experience-badge small { font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

        .story-content h2 { 
            font-size: 2.5rem; 
            font-weight: 800; 
            margin-bottom: 25px;
            background: linear-gradient(to right, #0f172a, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .story-content p { color: var(--text-slate); margin-bottom: 20px; }

        /* --- Features Section --- */
        .features-section {
            background: #f8fafc;
            padding: 100px 5%;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 { font-size: 2.2rem; font-weight: 800; }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 50px 40px;
            border-radius: 24px;
            border: 1px solid rgba(0,0,0,0.03);
            transition: var(--transition);
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 40px 80px rgba(0,0,0,0.05);
            border-color: var(--accent);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: #eff6ff;
            color: var(--accent);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 25px;
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            background: var(--accent);
            color: white;
            transform: rotateY(180deg);
        }

        /* --- CTA Section --- */
        .cta-box {
            background: var(--primary);
            border-radius: 40px;
            padding: 80px 40px;
            text-align: center;
            color: white;
            margin: 80px 5%;
            position: relative;
            overflow: hidden;
        }

        .cta-box h2 { font-size: 2.5rem; font-weight: 800; margin-bottom: 20px; }
        
        .cta-btn {
            display: inline-block;
            background: var(--accent);
            color: white;
            padding: 18px 45px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            transition: var(--transition);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

        .cta-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.4);
        }

        /* --- Responsive --- */
        @media (max-width: 992px) {
            .story-grid { grid-template-columns: 1fr; gap: 60px; text-align: center; }
            .experience-badge { right: 0; bottom: -20px; padding: 20px; }
        }
    </style>
</head>
<body>

    <section class="about-hero">
        <h1>Melody Masters</h1>
        <p>The rhythm of our expertise combined with the soul of your music.</p>
    </section>

    <div class="container">
        <div class="story-grid">
            <div class="story-image-box">
                <img src="upload/about2.jpg" alt="Our Store Experience">
                <div class="experience-badge">
                    <span>14+</span>
                    <small>Years of Excellence</small>
                </div>
            </div>
            <div class="story-content">
                <h2>Our Story, Your Sound</h2>
                <p>Founded in 2010, Melody Masters began with a simple belief: that everyone deserves access to high-quality musical instruments. What started as a small passion project among music lovers has evolved into Sri Lanka's premier destination for artists.</p>
                <p>We don't just sell equipment; we curate experiences. From the beginner picking up their first guitar to the professional tuning a grand piano, we provide the tools that turn inspiration into reality.</p>
                <p style="font-weight: 700; color: var(--primary);">Your musical journey is our greatest symphony.</p>
            </div>
        </div>
    </div>

    <section class="features-section">
        <div class="section-title">
            <h2>Why Musicians Trust Us</h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-award"></i></div>
                <h3>World Class Brands</h3>
                <p>We are authorized dealers for the world's most iconic musical brands, ensuring 100% authenticity.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                <h3>Expert Tuning</h3>
                <p>Our in-house specialists provide professional setup and tuning for every instrument we sell.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <h3>Islandwide Care</h3>
                <p>Secure, specialized delivery across Sri Lanka to ensure your instrument arrives in perfect harmony.</p>
            </div>
        </div>
    </section>

    <section class="cta-box">
        <h2>Ready to start your next masterpiece?</h2>
        <p style="margin-bottom: 40px; opacity: 0.8;">Our experts are ready to help you find the perfect instrument.</p>
        <a href="contactus.php" class="cta-btn">Talk to an Expert</a>
    </section>

    <?php include 'footer.php'; ?>

</body>
</html>