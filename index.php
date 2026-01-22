<?php 
include 'db_connect.php'; 
include 'navbar.php'; 

// අලුත්ම භාණ්ඩ 4ක් ලබාගැනීම
$featured_products = $conn->query("SELECT * FROM products ORDER BY product_id DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters - Premier Music Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --light-bg: #f8f9fa;
            --text-dark: #2d3436;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* --- Hero Section --- */
        .hero {
            height: 80vh;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('uploads/hero.jpg'); 
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 0 20px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .hero-content p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 30px;
        }

        .cta-btn {
            background: var(--accent);
            color: white;
            padding: 15px 35px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: 0.3s;
        }

        .cta-btn:hover { background: #2980b9; transform: scale(1.05); }

        /* --- Sections General --- */
        section { padding: 80px 10%; }
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        .section-title h2 { font-size: 2rem; color: var(--primary); }

        /* --- Category Highlights --- */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .cat-card {
            position: relative;
            height: 300px;
            border-radius: 15px;
            overflow: hidden;
            cursor: pointer;
        }

        .cat-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }

        .cat-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            display: flex;
            align-items: flex-end;
            padding: 20px;
            color: white;
        }

        .cat-card:hover img { transform: scale(1.1); }

        /* --- Featured Products --- */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
            transition: 0.3s;
        }

        .product-card:hover { transform: translateY(-10px); }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .price { color: var(--accent); font-weight: 700; font-size: 1.2rem; }

        /* --- Promo Banner --- */
        .promo-banner {
            position: relative;
            width: 95%; /* මෙතනින් ඔබට අවශ්‍ය පළල (Percentage) ලබා දෙන්න */
            max-width: 10000px; /* උපරිම පළල සීමා කිරීමෙන් ලොකු Screen වලදී ලස්සනට පෙනේ */
            min-height: 350px;
            background: url('upload/11.jpg') no-repeat center center/cover;
            background-color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            overflow: hidden;
            margin: 40px auto; /* auto යෙදීමෙන් banner එක මැදට (Center) පැමිණේ */
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        /* රූපය මත අඳුරු පටලයක් ඇති කර අකුරු පැහැදිලි කිරීමට */
        .promo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
            z-index: 1;
        }

        .promo-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 20px;
        }

        .offer-badge {
            background: var(--accent);
            color: white;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .promo-content h2 {
            font-size: 2.5rem;
            margin: 15px 0;
            line-height: 1.2;
        }

        .promo-content h2 span {
            color: #f1c40f; /* Premium Keyboards යන වර්ණය වෙනස් කිරීමට */
        }

        .promo-content p {
            font-size: 1.1rem;
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .cta-btn {
            display: inline-block;
            background: white;
            color: #2c3e50;
            padding: 12px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            transition: 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .cta-btn:hover {
            background: var(--accent);
            color: white;
            transform: translateY(-3px);
        }

        /* More Card එක සඳහා විශේෂ CSS */
        .more-card {
            background: var(--sidebar-bg); /* තද පැහැති පසුබිමක් */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: 0.3s;
            border: 2px solid var(--accent);
        }

        .more-content {
            color: white;
            padding: 20px;
        }

        .more-content i {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 10px;
        }

        .more-content h3 {
            margin: 5px 0;
            font-size: 1.5rem;
            color: white;
        }

        .more-content p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .more-card:hover {
            background: var(--accent);
            transform: translateY(-5px);
        }

        .more-card:hover i {
            color: white;
        }

        /* Mobile වලට ගැලපෙන ලෙස */
        @media (max-width: 768px) {
            .promo-content h2 { font-size: 1.8rem; }
            .promo-banner { min-height: 300px; border-radius: 10px; }
        }
    </style>
</head>
<body>

    <header class="hero">
        <div class="hero-content">
            <h1>Unlock Your Musical Journey</h1>
            <p>Explore high-quality instruments and digital sheet music curated for true musicians.</p>
            <a href="shop.php" class="cta-btn">Shop Collection</a>
        </div>
    </header>

    <section>
    <div class="section-title">
        <h2>Explore Categories</h2>
    </div>
    <div class="category-grid">
        <a href="shop.php?cat=Guitars" class="cat-card">
            <img src="upload/gitars.jpg" alt="Guitars">
            <div class="cat-overlay"><h3>Guitars</h3></div>
        </a>

        <a href="shop.php?cat=Keyboards" class="cat-card">
            <img src="upload/keyboard.jpg" alt="Keyboards">
            <div class="cat-overlay"><h3>Keyboards</h3></div>
        </a>

        <a href="shop.php?cat=Pianos" class="cat-card">
            <img src="upload/piano.jpg" alt="Pianos">
            <div class="cat-overlay"><h3>Pianos</h3></div>
        </a>

        <a href="shop.php?cat=Drums" class="cat-card">
            <img src="upload/drum.jpg" alt="Drums">
            <div class="cat-overlay"><h3>Drums</h3></div>
        </a>

        <a href="shop.php?cat=SheetMusic" class="cat-card">
            <img src="upload/sheets.jpg" alt="Sheet Music">
            <div class="cat-overlay"><h3>Sheet Music</h3></div>
        </a>

        <a href="shop.php?cat=Violins" class="cat-card">
            <img src="upload/Violins.jpg" alt="Violins">
            <div class="cat-overlay"><h3>Violins</h3></div>
        </a>

        <a href="shop.php?cat=Accessories" class="cat-card">
            <img src="upload/Accessories.jpg" alt="Accessories">
            <div class="cat-overlay"><h3>Accessories</h3></div>
        </a>

            <a href="shop.php" class="cat-card more-card" style="text-decoration: none;">
            <div class="more-content">
                <i class="fa fa-arrow-circle-right"></i>
                <h3>View All</h3>
                <p>Explore More</p>
            </div>
        </a>
        </div>
    </section>

    <div class="promo-banner">
    <div class="promo-overlay"></div>
    
    <div class="promo-content">
        <span class="offer-badge">Limited Time Offer</span>
        <h2>Get 20% Off on All <br><span>Premium Keyboards</span></h2>
        <p>Experience world-class sound with our latest collection.</p>
        <a href="shop.php" class="cta-btn">View Offers <i class="fas fa-arrow-right"></i></a>
    </div>
</div>

    <section style="background: var(--light-bg); padding: 50px 20px;">
        <div class="section-title" style="text-align: center; margin-bottom: 30px;">
            <h2>Featured Products</h2>
        </div>

        <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto;">
            
            <?php if ($featured_products->num_rows > 0): ?>
                <?php while($row = $featured_products->fetch_assoc()): ?>
                    <div class="product-card" style="background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                        <img src="uploads/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                        
                        <h3 style="margin: 15px 0 10px; font-size: 1.2rem;"><?php echo $row['product_name']; ?></h3>
                        
                        <p class="price" style="font-weight: bold; color: var(--sidebar-bg); font-size: 1.1rem;">
                            Rs. <?php echo number_format($row['price'], 2); ?>
                        </p>
                        
                        <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="nav-link" style="color: var(--accent); text-decoration: none; font-weight: 600;">View Detail</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1 / -1;">No products found.</p>
            <?php endif; ?>

        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="shop.php" style="background: var(--sidebar-bg); color: blue; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: 0.3s; display: inline-block;">
                See More Products <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
            </a>
        </div>
    </section>

    <?php include 'footer.php'; ?>

</body>
</html>