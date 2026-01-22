<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --nav-bg: #ffffff;
        --nav-text: #2c3e50;
        --nav-accent: #3498db;
        --nav-logout: #e74c3c;
        --nav-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .main-nav {
        display: flex; 
        justify-content: space-between; 
        padding: 0 10%; 
        height: 75px;
        background: var(--nav-bg); 
        color: var(--nav-text); 
        align-items: center;
        box-shadow: var(--nav-shadow);
        font-family: 'Inter', sans-serif;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .brand {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--nav-text);
        text-decoration: none;
        letter-spacing: -0.5px;
        z-index: 1001;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 25px;
    }

    .nav-link {
        color: var(--nav-text); 
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .nav-link:hover { color: var(--nav-accent); }

    .cart-container { position: relative; display: flex; align-items: center; }
    .cart-icon { font-size: 1.3rem; color: var(--nav-text); transition: color 0.3s; }
    .cart-count {
        position: absolute; top: -8px; right: -10px;
        background: var(--nav-accent); color: white;
        font-size: 0.7rem; font-weight: 700;
        padding: 2px 6px; border-radius: 50%;
        min-width: 12px; text-align: center;
    }

    .user-section {
        display: flex; align-items: center; gap: 15px;
        margin-left: 10px; padding-left: 20px;
        border-left: 1px solid #eee;
    }

    .user-name { font-weight: 600; color: var(--nav-text); font-size: 0.9rem; }
    .btn-register {
        background: var(--nav-accent); color: white !important; 
        padding: 10px 20px; border-radius: 8px; 
        text-decoration: none; font-weight: 600;
    }

    .btn-logout {
        background: #fff0f0; color: var(--nav-logout); 
        padding: 8px 18px; border-radius: 8px; 
        text-decoration: none; font-weight: 600;
        border: 1px solid #ffebeb;
    }

    /* Hamburger Menu Icon - Mobile Only */
    .menu-toggle {
        display: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--nav-text);
    }

    /* --- RESPONSIVE CSS --- */
    @media (max-width: 1024px) {
        .main-nav { padding: 0 5%; }
        .nav-links { gap: 15px; }
    }

    @media (max-width: 768px) {
        .menu-toggle { display: block; }

        .nav-links {
            position: fixed;
            top: 75px;
            left: -100%; /* සැඟවී පවතින ලෙස */
            width: 100%;
            height: calc(100vh - 75px);
            background: var(--nav-bg);
            flex-direction: column;
            align-items: center;
            padding: 40px 0;
            transition: 0.4s ease;
            box-shadow: 0 10px 10px rgba(0,0,0,0.05);
            gap: 30px;
            overflow-y: auto;
        }

        .nav-links.active {
            left: 0; /* Menu එක පෙන්වන ලෙස */
        }

        .user-section {
            flex-direction: column;
            border-left: none;
            padding-left: 0;
            margin-left: 0;
            text-align: center;
        }
    }
</style>

<nav class="main-nav">
    <a href="index.php" class="brand">Melody Masters</a>
    
    <div class="menu-toggle" id="mobile-menu">
        <i class="fa-solid fa-bars"></i>
    </div>
    
    <div class="nav-links" id="nav-menu">
        <a href="index.php" class="nav-link">Home</a>
        <a href="shop.php" class="nav-link">Shop</a>

        <?php if(isset($_SESSION['role'])): ?>
            <?php if($_SESSION['role'] == 'Customer'): ?>
                <a href="aboutus.php" class="nav-link">About Us</a>
                <a href="contactus.php" class="nav-link">Contact</a>
                <a href="my_orders.php" class="nav-link">My Orders</a>
                
                <a href="cart.php" class="cart-container nav-link">
                    <i class="fa-solid fa-cart-shopping cart-icon"></i>
                    <?php 
                    $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                    if($cart_count > 0): 
                    ?>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <?php if($_SESSION['role'] == 'Admin'): ?>
                <a href="admin_dashboard.php" class="nav-link">Admin Panel</a>
            <?php endif; ?>

            <?php if($_SESSION['role'] == 'Staff'): ?>
                <a href="staff_dashboard.php" class="nav-link">Inventory</a>
            <?php endif; ?>

            <div class="user-section">
                <span class="user-name">Hi, <?php echo isset($_SESSION['full_name']) ? explode(' ', $_SESSION['full_name'])[0] : 'User'; ?> 👋</span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>

        <?php else: ?>
            <a href="login.php" class="nav-link">Login</a>
            <a href="register.php" class="btn-register">Register</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    const mobileMenu = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');

    mobileMenu.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        // Icon එක මාරු කිරීම (Bars -> X)
        const icon = mobileMenu.querySelector('i');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-xmark');
    });
</script>