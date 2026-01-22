<?php
include 'db_connect.php';


// 1. පාරිභෝගිකයා ලොග් වී නැතිනම් Login පිටුවට යවන්න
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'navbar.php';

// 2. Cart එක හිස් නම් Shop පිටුවට හරවා යවන්න
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>alert('Your cart is empty!'); window.location.href='shop.php';</script>";
    exit();
}

if (isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $subtotal = $_POST['total_amount'];
    $shipping_cost = $_POST['shipping_cost'];
    $total_payable = $subtotal + $shipping_cost;
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']); // නව දත්තය

    // Orders වගුවට දත්ත ඇතුළත් කිරීම
    $sql_order = "INSERT INTO orders (user_id, total_amount, shipping_cost, order_status, shipping_address, payment_method) 
                  VALUES ('$user_id', '$total_payable', '$shipping_cost', 'Pending', '$address', '$payment_method')";
    
    if ($conn->query($sql_order) === TRUE) {
        $order_id = $conn->insert_id;

        foreach ($_SESSION['cart'] as $p_id => $qty) {
            $res = $conn->query("SELECT product_name, price FROM products WHERE product_id = '$p_id'");
            $product = $res->fetch_assoc();
            $p_name = mysqli_real_escape_string($conn, $product['product_name']);
            $unit_price = $product['price'];

            $sql_item = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) 
                         VALUES ('$order_id', '$p_id', '$p_name', '$unit_price', '$qty')";
            $conn->query($sql_item);

            // තොග ප්‍රමාණය අඩු කිරීම
            $conn->query("UPDATE products SET stock_quantity = stock_quantity - $qty WHERE product_id = '$p_id'");
        }

        unset($_SESSION['cart']);
        
        // Card Payment එකක් නම් පාරිභෝගිකයා වෙනත් පිටුවකට යැවිය හැක. දැනට අපි සෘජුවම සාර්ථක පණිවිඩය පෙන්වමු.
        echo "<script>alert('Order placed successfully! Method: $payment_method'); window.location.href='order_details.php?id=$order_id';</script>";
        exit();
    }
}

// මුළු එකතුව ගණනය කිරීම
$total = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $res = $conn->query("SELECT price FROM products WHERE product_id = '$id'");
    $p = $res->fetch_assoc();
    $total += ($p['price'] * $qty);
}
$shipping = ($total > 10000) ? 0 : 500.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --success: #27ae60;
            --bg-light: #f8f9fa;
            --white: #ffffff;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); color: var(--primary); }
        .checkout-container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .checkout-card { background: var(--white); padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        /* Payment Styles */
        .payment-options { margin: 20px 0; }
        .payment-box {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1.5px solid #eee;
            border-radius: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .payment-box:hover { border-color: var(--accent); }
        .payment-box input { margin-right: 15px; width: 18px; height: 18px; }
        .payment-box i { font-size: 1.2rem; margin-right: 10px; color: #555; }
        .payment-box.active { border-color: var(--accent); background: #f0f8ff; }

        .summary-box { background: var(--bg-light); padding: 20px; border-radius: 12px; margin-bottom: 20px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total-payable { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px solid #ddd; font-weight: 700; font-size: 1.2rem; }
        
        textarea { width: 100%; padding: 15px; border: 1.5px solid #eee; border-radius: 12px; box-sizing: border-box; resize: none; }
        .btn-place-order { width: 100%; background: var(--success); color: white; padding: 16px; border: none; border-radius: 12px; font-size: 1.1rem; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>

    <div class="checkout-container">
        <div class="checkout-card">
            <h2><i class="fa-solid fa-lock"></i> Secure Checkout</h2>
            
            <div class="summary-box">
                <div class="summary-item"><span>Subtotal</span><span>Rs. <?php echo number_format($total, 2); ?></span></div>
                <div class="summary-item"><span>Shipping Fee</span><span>Rs. <?php echo number_format($shipping, 2); ?></span></div>
                <div class="total-payable"><span>Total Amount</span><span>Rs. <?php echo number_format($total + $shipping, 2); ?></span></div>
            </div>

            <form action="" method="POST">
                <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                <input type="hidden" name="shipping_cost" value="<?php echo $shipping; ?>">
                
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea name="address" placeholder="No, Street, City" required></textarea>
                </div>

                <div class="payment-options">
                    <label>Payment Method</label>
                    
                    <label class="payment-box">
                        <input type="radio" name="payment_method" value="Cash on Delivery" checked>
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>Cash on Delivery (COD)</span>
                    </label>

                    <label class="payment-box">
                        <input type="radio" name="payment_method" value="Credit/Debit Card">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Credit or Debit Card</span>
                    </label>

                    <label class="payment-box">
                        <input type="radio" name="payment_method" value="Bank Transfer">
                        <i class="fa-solid fa-building-columns"></i>
                        <span>Online Bank Transfer</span>
                    </label>
                </div>

                <button type="submit" name="place_order" class="btn-place-order">
                    Confirm & Place Order
                </button>
            </form>
        </div>
    </div>

</body>
</html>