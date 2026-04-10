<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'navbar.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>alert('Your cart is empty!'); window.location.href='shop.php';</script>";
    exit();
}

// 1. Cart එකේ තියෙන භාණ්ඩ වර්ග හඳුනාගනිමු
$has_digital = false;
$has_physical = false;
$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $res = $conn->query("SELECT price, product_type FROM products WHERE product_id = '$id'");
    $p = $res->fetch_assoc();
    $total += ($p['price'] * $qty);
    
    if (isset($p['product_type']) && $p['product_type'] == 'Digital') {
        $has_digital = true;
    } else {
        $has_physical = true;
    }
}

// Shipping Logic: Physical products තියෙනවා නම් පමණක් shipping එකතු වේ
$shipping = ($has_physical && $total <= 200000) ? 500.00 : 0;

if (isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $total_payable = $total + $shipping;
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // Delivery info සැකසීම
    $delivery_parts = [];
    if (isset($_POST['email'])) {
        $delivery_parts[] = "Email: " . mysqli_real_escape_string($conn, $_POST['email']);
    }
    if (isset($_POST['address'])) {
        $delivery_parts[] = "Address: " . mysqli_real_escape_string($conn, $_POST['address']);
    }
    $delivery_info = implode(" | ", $delivery_parts);

    $sql_order = "INSERT INTO orders (user_id, total_amount, shipping_cost, order_status, shipping_address, payment_method) 
                  VALUES ('$user_id', '$total_payable', '$shipping', 'Pending', '$delivery_info', '$payment_method')";
    
    if ($conn->query($sql_order) === TRUE) {
        $order_id = $conn->insert_id;
        foreach ($_SESSION['cart'] as $p_id => $qty) {
            $res = $conn->query("SELECT product_name, price FROM products WHERE product_id = '$p_id'");
            $product = $res->fetch_assoc();
            $p_name = mysqli_real_escape_string($conn, $product['product_name']);
            $unit_price = $product['price'];
            $conn->query("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES ('$order_id', '$p_id', '$p_name', '$unit_price', '$qty')");
            $conn->query("UPDATE products SET stock_quantity = stock_quantity - $qty WHERE product_id = '$p_id'");
        }
        unset($_SESSION['cart']);
        echo "<script>alert('Order placed successfully!'); window.location.href='order_details.php?id=$order_id';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-clr: #6366f1;
            --primary-hover: #4f46e5;
            --bg-main: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-clr: #e2e8f0;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-main); color: var(--text-main); margin: 0; padding: 20px; }
        .checkout-wrapper { max-width: 550px; margin: 40px auto; }
        .checkout-card { background: var(--card-bg); padding: 35px; border-radius: 30px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); border: 1px solid var(--border-clr); }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { font-size: 1.8rem; font-weight: 700; margin: 0; background: linear-gradient(to right, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .summary-box { background: #f1f5f9; padding: 20px; border-radius: 20px; margin-bottom: 25px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: 500; color: var(--text-muted); font-size: 0.9rem; }
        .total-row { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px solid #cbd5e1; font-weight: 700; font-size: 1.2rem; color: var(--text-main); }
        
        .form-section { margin-bottom: 20px; background: #fff; border: 1px solid #eee; padding: 15px; border-radius: 15px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.85rem; color: var(--text-main); }
        input[type="text"], input[type="email"], textarea { width: 100%; padding: 12px; border: 1.5px solid var(--border-clr); border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; background: #fafafa; }
        
        .payment-option { display: flex; align-items: center; padding: 15px; border: 2px solid var(--border-clr); border-radius: 12px; margin-bottom: 10px; cursor: pointer; transition: 0.3s; }
        .payment-option:hover { border-color: var(--primary-clr); background: #f5f3ff; }
        .payment-option input { width: 18px; height: 18px; margin-right: 12px; accent-color: var(--primary-clr); }
        
        .submit-btn { width: 100%; background: var(--primary-clr); color: white; padding: 16px; border: none; border-radius: 14px; font-size: 1rem; font-weight: 700; cursor: pointer; margin-top: 20px; box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3); }
        .badge { background: #dcfce7; color: #166534; padding: 3px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; }
        
        #card-details-form { background: #f8fafc; padding: 15px; border-radius: 12px; margin-top: 10px; border: 1px solid var(--border-clr); display: none; }
    </style>
</head>
<body>

<div class="checkout-wrapper">
    <div class="checkout-card">
        <div class="header">
            <h2>Secure Checkout</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Review and place your order</p>
        </div>
        
        <div class="summary-box">
            <div class="summary-item"><span>Subtotal</span><span>Rs. <?php echo number_format($total, 2); ?></span></div>
            <div class="summary-item">
                <span>Shipping Fee</span>
                <span><?php echo ($shipping == 0) ? '<span class="badge">FREE</span>' : "Rs. " . number_format($shipping, 2); ?></span>
            </div>
            <div class="total-row"><span>Total Payable</span><span>Rs. <?php echo number_format($total + $shipping, 2); ?></span></div>
        </div>

        <form action="" method="POST" id="checkoutForm">
            
            <div class="form-section">
                <?php if($has_digital): ?>
                    <div style="margin-bottom: 15px;">
                        <input type="email" name="email" placeholder="Enter your email to receive digital files" required>
                    </div>
                <?php endif; ?>

                <?php if($has_physical): ?>
                    <div>
                        <textarea name="address" rows="3" placeholder="No, Street, City" required></textarea>
                    </div>
                <?php endif; ?>
            </div>

            <div class="method-container">
                <label>Payment Method</label>
                
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="Cash on Delivery" checked onclick="toggleCardForm(false)">
                    <i class="fa-solid fa-truck-fast" style="margin-right: 10px; color: var(--primary-clr);"></i>
                    <span>Cash on Delivery</span>
                </label>

                <label class="payment-option">
                    <input type="radio" name="payment_method" value="Credit/Debit Card" onclick="toggleCardForm(true)">
                    <i class="fa-solid fa-credit-card" style="margin-right: 10px; color: var(--primary-clr);"></i>
                    <span>Credit or Debit Card</span>
                </label>

                <div id="card-details-form">
                    <input type="text" id="card_no" placeholder="Card Number" style="margin-bottom: 10px;" maxlength="19">
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="expiry" placeholder="MM/YY" maxlength="5">
                        <input type="text" id="cvv" placeholder="CVV" maxlength="3">
                    </div>
                </div>
            </div>

            <button type="submit" name="place_order" class="submit-btn">
                Confirm Order (Rs. <?php echo number_format($total + $shipping, 2); ?>)
            </button>
        </form>
    </div>
</div>

<script>
    function toggleCardForm(show) {
        const form = document.getElementById('card-details-form');
        form.style.display = show ? 'block' : 'none';
        const inputs = form.querySelectorAll('input');
        inputs.forEach(i => i.required = show);
    }

    // Basic Input Formatting
    document.getElementById('card_no').addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g, '').replace(/(\d{4})(?=\d)/g, '$1 ');
    });
</script>

</body>
</html>