<?php
// Session එක ආරම්භ වී නැත්නම් පමණක් ආරම්භ කරන්න
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// 1. Cart එකට භාණ්ඩයක් එකතු කිරීම
if (isset($_POST['add_to_cart'])) {
    $p_id = $_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    if (isset($_SESSION['cart'][$p_id])) {
        $_SESSION['cart'][$p_id] += $qty;
    } else {
        $_SESSION['cart'][$p_id] = $qty;
    }
    header("Location: cart.php");
    exit();
}

// 2. Cart එකේ Quantity එක Update කිරීම
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

// 3. භාණ්ඩයක් Cart එකෙන් ඉවත් කිරීම
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
        
        .cart-wrapper { 
            display: grid; 
            grid-template-columns: 1fr 350px; 
            gap: 30px; 
            align-items: start; 
        }

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
            width: 55px;
            padding: 8px;
            border: 1.5px solid #eee;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }

        .order-summary { 
            background: white; 
            border-radius: 20px; 
            padding: 25px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            position: sticky; 
            top: 100px; 
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

        /* --- Responsive View --- */
        
        @media (max-width: 992px) {
            .cart-wrapper { grid-template-columns: 1fr; }
            .order-summary { position: static; margin-top: 20px; }
        }

        @media (max-width: 600px) {
            .cart-item {
                grid-template-columns: 70px 1fr 40px;
                grid-template-areas: 
                    "img info remove"
                    "img qty price";
            }

            .cart-item img { grid-area: img; }
            .item-info { grid-area: info; }
            .remove-btn { grid-area: remove; }
            .cart-item form { grid-area: qty; }
            .subtotal-display { grid-area: price; text-align: right; font-size: 0.9rem; }

            .cart-container { margin: 15px auto; }
            .cart-items { padding: 15px; }
        }
    </style>
</head>
<body>

<div class="cart-container">
    <h2 style="margin-bottom: 25px;"></h2>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div class="cart-wrapper">
            <div class="cart-items">
                <?php 
                $total = 0;
                foreach ($_SESSION['cart'] as $id => $qty): 
                    $sql = "SELECT * FROM products WHERE product_id = '$id'";
                    $res = $conn->query($sql);
                    if($res->num_rows > 0):
                        $product = $res->fetch_assoc();
                        $subtotal = $product['price'] * $qty;
                        $total += $subtotal;
                ?>
                <div class="cart-item">
                    <img src="uploads/<?php echo $product['product_image']; ?>" alt="Product">
                    
                    <div class="item-info">
                        <h4><?php echo $product['product_name']; ?></h4>
                        <p style="font-size:0.8rem; color:#888; margin: 4px 0;">Rs. <?php echo number_format($product['price'], 2); ?></p>
                    </div>
                    
                    <form action="cart.php" method="POST" id="update-form-<?php echo $id; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                        <input type="hidden" name="update_cart" value="1">
                        <input type="number" name="quantity" class="qty-input" 
                               value="<?php echo $qty; ?>" min="1" 
                               onchange="document.getElementById('update-form-<?php echo $id; ?>').submit();">
                    </form>

                    <div class="subtotal-display" style="font-weight:700;">
                        Rs. <?php echo number_format($subtotal, 2); ?>
                    </div>

                    <a href="cart.php?remove=<?php echo $id; ?>" class="remove-btn">×</a>
                </div>
                <?php endif; endforeach; ?>
            </div>

            <div class="order-summary">
                <h3 style="margin-top:0;">Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>Rs. <?php echo number_format($total, 2); ?></span>
                </div>
                <?php $shipping = ($total > 10000 || $total == 0) ? 0 : 500.00; ?>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span><?php echo ($shipping == 0) ? "<b style='color:var(--success);'>FREE</b>" : "Rs. " . number_format($shipping, 2); ?></span>
                </div>
                <div class="summary-row total-row">
                    <span>Total</span>
                    <span>Rs. <?php echo number_format($total + $shipping, 2); ?></span>
                </div>
                <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                <p style="text-align:center; font-size:0.75rem; color:#888; margin-top:15px;">
                    <i class="fa fa-lock"></i> Secure Checkout Guaranteed
                </p>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align:center; background:white; padding:60px 20px; border-radius:20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <i class="fa-solid fa-cart-shopping" style="font-size: 4rem; color: #eee; margin-bottom: 20px;"></i>
            <h3>Your cart is empty</h3>
            <p style="color:#888;">Looks like you haven't added anything to your cart yet.</p>
            <a href="shop.php" class="btn-checkout" style="display:inline-block; padding:12px 35px; margin-top:10px;">Return to Shop</a>
        </div>
    <?php endif; ?>
</div>



<?php include 'footer.php'; ?>

</body>
</html>