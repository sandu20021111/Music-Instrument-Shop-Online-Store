<?php 
include 'db_connect.php'; 
include 'navbar.php'; 

$featured_products = $conn->query("SELECT * FROM products ORDER BY product_id DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters - Premier Music Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --light-bg: #f8f9fa;
            --text-dark: #1a202c;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* --- Keyframe Animations --- */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes zoomIn {
            from { transform: scale(1.1); }
            to { transform: scale(1); }
        }

        /* --- Scroll Reveal Logic --- */
        .reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: var(--transition);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* --- Hero Section --- */
        .hero {
            height: 90vh;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('uploads/hero.jpg'); 
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 0 20px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            animation: fadeInUp 1.2s ease-out forwards;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            margin-bottom: 20px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .hero-content p {
            font-size: 1.2rem;
            max-width: 650px;
            margin: 0 auto 35px;
            opacity: 0.9;
        }

        .cta-btn-hero {
            background: var(--accent);
            color: white;
            padding: 16px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            transition: var(--transition);
            display: inline-block;
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        }

        .cta-btn-hero:hover {
            transform: translateY(-3px) scale(1.05);
            background: #2980b9;
            box-shadow: 0 15px 30px rgba(52, 152, 219, 0.4);
        }

        /* --- Category Highlights --- */
        section { padding: 100px 8%; }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h2 { 
            font-size: 2.2rem; 
            font-weight: 800;
            color: var(--primary); 
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 60px;
            height: 4px;
            background: var(--accent);
            transform: translateX(-50%);
            border-radius: 10px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .cat-card {
            position: relative;
            height: 320px;
            border-radius: 20px;
            overflow: hidden;
            display: block;
            text-decoration: none;
        }

        .cat-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .cat-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%);
            display: flex;
            align-items: flex-end;
            padding: 30px;
            color: white;
        }

        .cat-overlay h3 { font-size: 1.5rem; margin: 0; font-weight: 700; }

        .cat-card:hover img { transform: scale(1.1); }
        .cat-card:hover .cat-overlay { background: linear-gradient(to top, var(--accent) 0%, transparent 80%); opacity: 0.9; }

        /* --- Featured Products --- */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            text-align: center;
            transition: var(--transition);
            border: 1px solid #f1f5f9;
        }

        .product-card:hover { 
            transform: translateY(-12px); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-color: var(--accent);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            margin-bottom: 20px;
            transition: var(--transition);
        }

        .product-card:hover img { transform: scale(1.05); }

        .price { color: var(--primary); font-weight: 800; font-size: 1.3rem; margin: 10px 0; }

        .view-detail-btn {
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        /* --- Promo Banner --- */
        .promo-banner {
            position: relative;
            width: 85%; 
            min-height: 400px;
            background: url('upload/11.jpg') center/cover;
            background-color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 30px;
            overflow: hidden;
            margin: 60px auto; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .promo-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.2) 100%);
            z-index: 1;
        }

        .promo-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 40px;
        }

        .offer-badge {
            background: #f1c40f;
            color: #2c3e50;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .promo-content h2 { font-size: clamp(1.8rem, 4vw, 3rem); margin: 20px 0; font-weight: 800; }
        .promo-content span { color: #f1c40f; }

        /* More Card */
        .more-card {
            background: #f1f5f9; 
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 2px dashed #cbd5e1;
        }

        .more-content i { font-size: 3rem; color: var(--accent); margin-bottom: 15px; transition: var(--transition); }
        .more-card:hover { background: var(--accent); border-style: solid; border-color: var(--accent); }
        .more-card:hover i, .more-card:hover h3, .more-card:hover p { color: white; }

        @media (max-width: 768px) {
            section { padding: 60px 5%; }
            .promo-banner { width: 95%; min-height: 350px; }
        }
    </style>
</head>
<body>

    <header class="hero">
        <div class="hero-content">
            <h1>Unlock Your <br>Musical Journey</h1>
            <p>Experience the symphony of high-quality instruments and professional digital scores designed for the modern artist.</p>
            <a href="shop.php" class="cta-btn-hero">Shop the Collection <i class="fa-solid fa-arrow-right" style="margin-left:8px;"></i></a>
        </div>
    </header>

    <section class="reveal">
        <div class="section-title">
            <h2>Explore Categories</h2>
        </div>
        <div class="category-grid">
            <a href="shop.php?cat=Guitars" class="cat-card">
                <img src="upload/gitars.jpg" alt="Guitars">
                <div class="cat-overlay"><h3>Guitars</h3></div>
            </a>
            <a href="shop.php?cat=Pianos" class="cat-card">
                <img src="upload/piano.jpg" alt="Pianos">
                <div class="cat-overlay"><h3>Pianos</h3></div>
            </a>
            <a href="shop.php?cat=Drums" class="cat-card">
                <img src="upload/drum.jpg" alt="Drums">
                <div class="cat-overlay"><h3>Drums</h3></div>
            </a>
            <a href="shop.php" class="cat-card more-card">
                <div class="more-content">
                    <i class="fa-solid fa-circle-arrow-right"></i>
                    <h3>View All</h3>
                    <p>Discover More</p>
                </div>
            </a>
        </div>
    </section>

    <div class="promo-banner reveal">
        <div class="promo-overlay"></div>
        <div class="promo-content">
            <span class="offer-badge">Exclusive Deal</span>
            <h2>Up to <span>20% Off</span> on All <br>Premium Instruments</h2>
            <p>Upgrade your sound today with our premium selection.</p>
            <a href="shop.php" class="cta-btn-hero" style="background: white; color: var(--primary);">Claim Offer</a>
        </div>
    </div>

    <section style="background: var(--light-bg);" class="reveal">
        <div class="section-title">
            <h2>Featured Collection</h2>
        </div>

        <div class="product-grid">
            <?php if ($featured_products->num_rows > 0): ?>
                <?php while($row = $featured_products->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="uploads/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>">
                        <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?php echo $row['product_name']; ?></h3>
                        <div class="price">Rs. <?php echo number_format($row['price'], 2); ?></div>
                        <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="view-detail-btn">
                            View Product <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1 / -1;">New arrivals coming soon!</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 50px;">
            <a href="shop.php" style="color: var(--primary); font-weight: 700; text-decoration: none; border-bottom: 2px solid var(--accent); padding-bottom: 5px;">
                Browse All Products <i class="fa-solid fa-arrow-right-long" style="margin-left: 10px;"></i>
            </a>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
        function reveal() {
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 100; 

                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }

        window.addEventListener("scroll", reveal);
        // Initial check on load
        window.addEventListener("load", reveal);
    </script>
</body>
</html>