<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// --- ADD TO CART LOGIC ---
if (isset($_POST['add_to_cart'])) {
    $p_id = $_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    
    $check_sql = "SELECT product_type FROM products WHERE product_id = '$p_id'";
    $check_res = $conn->query($check_sql);
    $p_data = $check_res->fetch_assoc();
    
    if($p_data['product_type'] == 'Digital') {
        $qty = 1; 
    }

    if (isset($_SESSION['cart'][$p_id])) {
        if($p_data['product_type'] != 'Digital') {
            $_SESSION['cart'][$p_id] += $qty;
        } else {
            $_SESSION['cart'][$p_id] = 1;
        }
    } else {
        $_SESSION['cart'][$p_id] = $qty;
    }
    header("Location: cart.php");
    exit();
}

// --- UPDATE CART LOGIC ---
if (isset($_POST['update_cart'])) {
    $p_id = $_POST['product_id'];
    $new_qty = (int)$_POST['quantity'];
    
    if ($new_qty > 0) {
        $_SESSION['cart'][$p_id] = $new_qty;
    } else {
        unset($_SESSION['cart'][$p_id]);
    }
    header("Location: cart.php");
    exit();
}

// --- REMOVE LOGIC ---
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --bg-light: #f8f9fa;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); margin: 0; color: var(--primary); }
        .cart-container { max-width: 1100px; margin: 40px auto; padding: 0 15px; }
        .cart-wrapper { display: grid; grid-template-columns: 1fr 350px; gap: 30px; align-items: start; }
        .cart-items { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        .cart-item {
            display: grid;
            grid-template-columns: 80px 1fr 100px 100px 30px;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }

        .cart-item img { width: 70px; height: 70px; object-fit: contain; background: var(--bg-light); border-radius: 10px; }
        .item-info h4 { margin: 0; font-size: 0.95rem; }
        
        .qty-input {
            width: 55px; padding: 8px; border: 1.5px solid #eee; border-radius: 8px;
            text-align: center; font-weight: 600;
        }

        .qty-readonly {
            background-color: #f9f9f9;
            color: #999;
            cursor: not-allowed;
            border-style: dashed;
        }

        .order-summary { 
            background: white; border-radius: 20px; padding: 25px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); position: sticky; top: 100px; 
        }

        .summary-row { display: flex; justify-content: space-between; margin: 12px 0; font-size: 0.95rem; }
        .total-row { border-top: 2px solid var(--bg-light); margin-top: 15px; padding-top: 15px; font-weight: 700; font-size: 1.15rem; }
        
        .btn-checkout {
            display: block; background: var(--primary); color: white; text-align: center;
            padding: 14px; text-decoration: none; border-radius: 12px; font-weight: 600; margin-top: 20px;
            transition: 0.3s;
        }
        .btn-checkout:hover { background: #1a252f; }

        .remove-btn { color: #bbb; text-decoration: none; font-size: 1.4rem; text-align: center; transition: 0.3s; }
        .remove-btn:hover { color: var(--danger); }

        @media (max-width: 992px) {
            .cart-wrapper { grid-template-columns: 1fr; }
            .order-summary { position: static; margin-top: 20px; }
        }
    </style>
</head>
<body>

<div class="cart-container">
    <h2 style="margin-bottom: 25px;"><i class="fa fa-shopping-cart"></i> Shopping Cart</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div class="cart-wrapper">
            <div class="cart-items">
                <?php 
                $total = 0;
                $has_physical = false; // Physical item එකක් තියෙනවදැයි බැලීමට

                foreach ($_SESSION['cart'] as $id => $qty): 
                    $sql = "SELECT * FROM products WHERE product_id = '$id'";
                    $res = $conn->query($sql);
                    if($res->num_rows > 0):
                        $product = $res->fetch_assoc();
                        $is_digital = (isset($product['product_type']) && $product['product_type'] == 'Digital');
                        
                        // එකම එක physical item එකක් හෝ හමු වුවහොත් මෙය true වේ
                        if(!$is_digital) {
                            $has_physical = true;
                        }

                        if($is_digital) $qty = 1;

                        $subtotal = $product['price'] * $qty;
                        $total += $subtotal;
                ?>
                <div class="cart-item">
                    <img src="uploads/<?php echo $product['product_image']; ?>" alt="Product">
                    
                    <div class="item-info">
                        <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                        <?php if($is_digital): ?>
                            <span style="font-size: 0.7rem; color: var(--accent); font-weight: 700;"><i class="fa fa-download"></i> DIGITAL PRODUCT</span>
                        <?php else: ?>
                            <span style="font-size: 0.7rem; color: var(--success); font-weight: 700;"><i class="fa fa-truck"></i> PHYSICAL PRODUCT</span>
                        <?php endif; ?>
                        <p style="font-size:0.8rem; color:#888; margin: 4px 0;">Rs. <?php echo number_format($product['price'], 2); ?></p>
                    </div>
                    
                    <form action="cart.php" method="POST" id="update-form-<?php echo $id; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                        <input type="hidden" name="update_cart" value="1">
                        
                        <?php if($is_digital): ?>
                            <input type="number" name="quantity" class="qty-input qty-readonly" 
                                   value="1" readonly title="Digital products are limited to 1 per order">
                        <?php else: ?>
                            <input type="number" name="quantity" class="qty-input" 
                                   value="<?php echo $qty; ?>" min="1" 
                                   onchange="document.getElementById('update-form-<?php echo $id; ?>').submit();">
                        <?php endif; ?>
                    </form>

                    <div class="subtotal-display" style="font-weight:700;">
                        Rs. <?php echo number_format($subtotal, 2); ?>
                    </div>

                    <a href="cart.php?remove=<?php echo $id; ?>" class="remove-btn" onclick="return confirm('Remove item?')">×</a>
                </div>
                <?php endif; endforeach; ?>
            </div>

            <div class="order-summary">
                <h3 style="margin-top:0;">Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>Rs. <?php echo number_format($total, 2); ?></span>
                </div>
                
                <?php 
                // Shipping Logic:
                // 1. Physical items නැත්නම් සැමවිටම 0.
                // 2. Physical items තිබේ නම් සහ total > 200,000 නම් 0 (Free).
                // 3. Physical items තිබේ නම් සහ total <= 200,000 නම් 500.
                if (!$has_physical) {
                    $shipping = 0;
                    $shipping_text = "<b style='color:var(--accent);'>N/A (Digital)</b>";
                } else {
                    if ($total > 200000) {
                        $shipping = 0;
                        $shipping_text = "<b style='color:var(--success);'>FREE</b>";
                    } else {
                        $shipping = 500.00;
                        $shipping_text = "Rs. " . number_format($shipping, 2);
                    }
                }
                ?>
                
                <div class="summary-row">
                    <span>Shipping</span>
                    <span><?php echo $shipping_text; ?></span>
                </div>

                <div class="summary-row total-row">
                    <span>Total</span>
                    <span>Rs. <?php echo number_format($total + $shipping, 2); ?></span>
                </div>
                
                <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                
                <p style="text-align:center; font-size:0.75rem; color:#888; margin-top:15px;">
                    <i class="fa fa-lock"></i> Secure Checkout Guaranteed
                </p>
                
                <?php if(!$has_physical): ?>
                <p style="font-size: 0.7rem; color: #666; background: #eef9ff; padding: 10px; border-radius: 8px; margin-top: 10px;">
                    <i class="fa fa-info-circle"></i> Digital products will be available for download instantly after payment.
                </p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align:center; background:white; padding:60px 20px; border-radius:20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <i class="fa-solid fa-cart-shopping" style="font-size: 4rem; color: #eee; margin-bottom: 20px;"></i>
            <h3>Your cart is empty</h3>
            <p style="color:#888;">Looks like you haven't added any musical instruments yet.</p>
            <a href="shop.php" class="btn-checkout" style="display:inline-block; padding:12px 35px; margin-top:10px;">Return to Shop</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>