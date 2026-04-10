<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --nav-bg: rgba(255, 255, 255, 0.95);
        --nav-text: #1a202c;
        --nav-accent: #3182ce; /* Modern Blue */
        --nav-hover-bg: #f7fafc;
        --nav-logout: #e53e3e;
        --nav-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body { margin: 0; padding-top: 80px; } /* Space for fixed nav */

    .main-nav {
        display: flex; 
        justify-content: space-between; 
        padding: 0 8%; 
        height: 80px;
        background: var(--nav-bg); 
        backdrop-filter: blur(10px); /* Blur effect */
        color: var(--nav-text); 
        align-items: center;
        box-shadow: var(--nav-shadow);
        font-family: 'Plus Jakarta Sans', sans-serif;
        position: fixed;
        top: 0;
        width: 100%;
        box-sizing: border-box;
        z-index: 2000;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    /* Logo Styling */
    .brand {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--nav-text);
        text-decoration: none;
        background: linear-gradient(135deg, #2c3e50, var(--nav-accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        transition: var(--transition);
    }

    .brand:hover { opacity: 0.8; transform: scale(1.02); }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 8px; /* Compact spacing */
    }

    .nav-link {
        color: var(--nav-text); 
        text-decoration: none;
        font-size: 0.92rem;
        font-weight: 600;
        padding: 10px 16px;
        border-radius: 10px;
        transition: var(--transition);
        position: relative;
    }

    .nav-link:hover {
        background: var(--nav-hover-bg);
        color: var(--nav-accent);
    }

    /* Active Link Indicator */
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 50%;
        width: 0;
        height: 2px;
        background: var(--nav-accent);
        transition: var(--transition);
        transform: translateX(-50%);
    }

    .nav-link:hover::after { width: 30%; }

    /* Cart Icon Enhancement */
    .cart-container { position: relative; margin-right: 5px; }
    .cart-icon { font-size: 1.2rem; }
    .cart-count {
        position: absolute; 
        top: -5px; 
        right: -8px;
        background: var(--nav-accent); 
        color: white;
        font-size: 0.65rem; 
        font-weight: 800;
        height: 18px;
        width: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }

    /* User Section Styling */
    .user-section {
        display: flex; 
        align-items: center; 
        gap: 12px;
        margin-left: 15px; 
        padding-left: 15px;
        border-left: 2px solid #edf2f7;
    }

    .user-name { 
        font-weight: 700; 
        color: #4a5568; 
        font-size: 0.88rem;
        background: #f1f5f9;
        padding: 6px 12px;
        border-radius: 20px;
    }

    /* Buttons Style */
    .btn-register {
        background: var(--nav-accent); 
        color: white !important; 
        padding: 10px 22px; 
        border-radius: 12px; 
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(49, 130, 206, 0.3);
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(49, 130, 206, 0.4);
    }

    /* Logout Button Modern Design */
    .btn-logout {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff0f0; /* Light red background */
        color: #e53e3e; /* Modern Red */
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: 1px solid #fed7d7;
    }

    .btn-logout i {
        font-size: 0.9rem;
        transition: transform 0.3s ease;
    }

    .btn-logout:hover {
        background: #e53e3e;
        color: white;
        border-color: #e53e3e;
        box-shadow: 0 4px 12px rgba(229, 62, 62, 0.2);
        transform: translateY(-1px);
    }

    .btn-logout:hover i {
        transform: translateX(3px); /* Logout icon එක මදක් දකුණට යන animation එකක් */
    }

    /* User Section එකේ පරතරය නිවැරදි කිරීම */
    .user-section {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-left: 10px;
        padding-left: 20px;
        border-left: 2px solid #edf2f7;
    }

    /* Mobile Menu Icon */
    .menu-toggle {
        display: none;
        font-size: 1.4rem;
        cursor: pointer;
        padding: 10px;
    }

    /* --- RESPONSIVE SETTINGS --- */
    @media (max-width: 1024px) {
        .main-nav { padding: 0 4%; }
    }

    @media (max-width: 768px) {
        body { padding-top: 70px; }
        .main-nav { height: 70px; }
        .menu-toggle { display: block; }

        .nav-links {
            position: fixed;
            top: 70px;
            left: -100%; 
            width: 100%;
            height: 100vh;
            background: white;
            flex-direction: column;
            padding: 50px 0;
            transition: 0.5s cubic-bezier(0.77, 0, 0.175, 1);
            gap: 20px;
        }

        .nav-links.active { left: 0; }
        .user-section {
            flex-direction: column;
            border-left: none;
            padding: 20px 0;
            width: 80%;
            border-top: 1px solid #eee;
        }
    }
</style>

<nav class="main-nav">
    <a href="index.php" class="brand">Melody Masters</a>
    
    <div class="menu-toggle" id="mobile-menu">
        <i class="fa-solid fa-bars-staggered"></i>
    </div>
    
    <div class="nav-links" id="nav-menu">
        <a href="index.php" class="nav-link">Home</a>
        <a href="shop.php" class="nav-link">Shop</a>
        <a href="aboutus.php" class="nav-link">About</a>
        <a href="contactus.php" class="nav-link">Contact</a>

        <?php if(isset($_SESSION['role'])): ?>
            <?php if($_SESSION['role'] == 'Customer'): ?>
                <a href="my_orders.php" class="nav-link">My Orders</a>
                
                <a href="cart.php" class="nav-link cart-container">
                    <i class="fa-solid fa-bag-shopping cart-icon"></i>
                    <?php 
                    $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                    if($cart_count > 0): 
                    ?>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <?php if($_SESSION['role'] == 'Admin'): ?>
                <a href="admin_dashboard.php" class="nav-link"><i class="fa-solid fa-gauge-high"></i> Admin</a>
            <?php endif; ?>

            <?php if($_SESSION['role'] == 'Staff'): ?>
                <a href="staff_dashboard.php" class="nav-link"><i class="fa-solid fa-boxes-stacked"></i> Inventory</a>
            <?php endif; ?>

            <div class="user-section">
                <span class="user-name">
                    <i class="fa-regular fa-circle-user"></i> 
                    <?php echo isset($_SESSION['full_name']) ? explode(' ', $_SESSION['full_name'])[0] : 'User'; ?>
                </span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>

        <?php else: ?>
            <a href="login.php" class="nav-link">Login</a>
            <a href="register.php" class="btn-register">Get Started</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    const mobileMenu = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');

    mobileMenu.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        const icon = mobileMenu.querySelector('i');
        icon.classList.toggle('fa-bars-staggered');
        icon.classList.toggle('fa-xmark');
    });

    // Close menu when clicking a link (Mobile)
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
</script>