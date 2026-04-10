<?php
include 'db_connect.php';
include 'navbar.php';

if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $sql = "SELECT p.*, c.category_name FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = '$product_id'";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "<div style='text-align:center; padding:50px;'><h2>Product not found!</h2><a href='shop.php'>Back to Shop</a></div>";
        exit();
    }
} else {
    header("Location: shop.php");
    exit();
}

$has_purchased = false;
$download_info = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $check_purchase = "SELECT * FROM orders o 
                       JOIN order_items oi ON o.order_id = oi.order_id 
                       WHERE o.user_id = '$user_id' 
                       AND oi.product_id = '$product_id' 
                       AND o.order_status = 'Delivered'"; 

    $purchase_result = $conn->query($check_purchase);
    if ($purchase_result && $purchase_result->num_rows > 0) {
        $has_purchased = true;
    }

    if ($product['product_type'] == 'Digital') {
        $dl_query = "SELECT * FROM digital_downloads 
                     WHERE user_id = '$user_id' AND product_id = '$product_id' LIMIT 1";
        $dl_res = $conn->query($dl_query);
        if ($dl_res && $dl_res->num_rows > 0) {
            $download_info = $dl_res->fetch_assoc();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['product_name']; ?> - Melody Masters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --success: #27ae60;
            --text-muted: #636e72;
            --bg-light: #f8f9fa;
            --warning: #f1c40f;
            --danger: #e74c3c;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        body { font-family: 'Inter', sans-serif; background-color: #fff; margin: 0; color: var(--primary); line-height: 1.6; }
        
        /* Layout */
        .product-page-container { max-width: 1200px; margin: 50px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; }
        .product-image-container { background: var(--bg-light); border-radius: 20px; padding: 30px; display: flex; align-items: center; justify-content: center; position: sticky; top: 100px; height: fit-content; }
        .image-wrapper img { max-width: 100%; height: auto; transition: 0.4s ease; border-radius: 10px; }
        .image-wrapper:hover img { transform: scale(1.05); }
        
        .brand-label { text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: var(--accent); font-size: 0.85rem; }
        h1 { font-size: 2.5rem; margin: 10px 0; font-weight: 700; }
        .price-tag { font-size: 2rem; font-weight: 700; margin: 20px 0; color: var(--primary); }
        
        .specs-container { background: var(--bg-light); padding: 25px; border-radius: 15px; margin-top: 30px; }
        .purchase-box { margin-top: 30px; padding: 25px; border: 2px solid #eee; border-radius: 15px; }
        
        .btn-add-cart { background: var(--primary); color: white; padding: 15px 40px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; text-decoration: none; display: inline-block; text-align: center; }
        .btn-add-cart:hover { background: #1a252f; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

        /* Digital Download Box */
        .download-box { margin-top: 20px; padding: 20px; background: #eef9ff; border: 2px dashed var(--accent); border-radius: 15px; text-align: center; }

        /* --- Updated Review Section --- */
        .review-section { max-width: 1200px; margin: 80px auto; padding: 0 20px; border-top: 1px solid #eee; padding-top: 50px; }
        .review-header { margin-bottom: 40px; text-align: center; }
        .review-header h2 { font-size: 2rem; font-weight: 700; }

        .review-form { background: var(--bg-light); padding: 30px; border-radius: 20px; margin-bottom: 50px; border: 1px solid #eee; max-width: 800px; margin-left: auto; margin-right: auto; }
        .review-form h3 { margin-top: 0; margin-bottom: 20px; }
        .review-form textarea { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 12px; margin: 15px 0; font-family: inherit; resize: vertical; box-sizing: border-box; }
        
        /* Stars Input */
        .stars-container { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 8px; margin: 10px 0; }
        .stars-container input { display: none; }
        .stars-container label { font-size: 1.8rem; color: #ddd; cursor: pointer; transition: 0.2s; }
        .stars-container label:hover, .stars-container label:hover ~ label, .stars-container input:checked ~ label { color: var(--warning); }

        /* --- Review Grid (Card display) --- */
        .reviews-list { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); 
            gap: 25px; 
        }
        
        .review-card { 
            background: var(--white); 
            padding: 25px; 
            border-radius: 20px; 
            border: 1px solid #f0f0f0; 
            transition: 0.3s ease; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
        }
        .review-card:hover { transform: translateY(-5px); box-shadow: var(--shadow); }
        
        .review-meta { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .user-avatar { width: 45px; height: 45px; background: #e0e7ff; color: #4338ca; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .user-info h4 { margin: 0; font-size: 1rem; }
        .user-info span { font-size: 0.8rem; color: var(--text-muted); }
        
        .stars-display { color: var(--warning); font-size: 0.85rem; margin-bottom: 10px; }
        .review-text { color: #4a5568; line-height: 1.6; margin: 0; font-size: 0.95rem; }

        .notice-box { background: #fffbeb; border-left: 4px solid var(--warning); padding: 15px 20px; border-radius: 8px; margin-bottom: 30px; text-align: center; }

        @media (max-width: 850px) { 
            .product-page-container { grid-template-columns: 1fr; } 
            .product-image-container { position: static; }
            .reviews-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="product-page-container">
        <div class="product-image-container">
            <div class="image-wrapper">
                <img src="uploads/<?php echo $product['product_image']; ?>" alt="<?php echo $product['product_name']; ?>">
            </div>
        </div>

        <div class="product-details">
            <span class="brand-label"><?php echo $product['brand']; ?></span>
            <h1><?php echo $product['product_name']; ?></h1>
            <p style="color: var(--text-muted);">Store / <?php echo $product['category_name']; ?> / <?php echo $product['product_type']; ?></p>
            
            <div class="price-tag">Rs. <?php echo number_format($product['price'], 2); ?></div>

            <div class="specs-container">
                <h3>Specifications</h3>
                <p><?php echo nl2br($product['specifications']); ?></p>
            </div>

            <?php if ($download_info): ?>
                <div class="download-box">
                    <h3 style="margin-top:0;"><i class="fa-solid fa-cloud-arrow-down"></i> Digital Access</h3>
                    <?php 
                    $current_time = date('Y-m-d H:i:s');
                    $is_expired = ($current_time > $download_info['expiry_date']);
                    $no_limit = ($download_info['download_count'] >= $download_info['max_limit']);

                    if ($is_expired): ?>
                        <p style="color: var(--danger);">Link expired.</p>
                    <?php elseif ($no_limit): ?>
                        <p style="color: var(--danger);">Download limit reached.</p>
                    <?php else: ?>
                        <p>Remaining: <?php echo ($download_info['max_limit'] - $download_info['download_count']); ?> downloads</p>
                        <a href="download.php?id=<?php echo $product['product_id']; ?>" class="btn-add-cart" style="background: var(--success); width: 100%; box-sizing: border-box;">Download Now</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="purchase-box">
                <?php if ($product['stock_quantity'] > 0 || $product['product_type'] == 'Digital'): ?>
                    <form action="cart.php" method="POST" style="display: flex; gap: 15px;">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <?php if($product['product_type'] !== 'Digital'): ?>
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" style="width: 80px; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
                        <?php else: ?>
                            <input type="hidden" name="quantity" value="1">
                        <?php endif; ?>
                        <button type="submit" name="add_to_cart" class="btn-add-cart" style="flex: 1;">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <p style="color: var(--danger); font-weight: 700;">Out of Stock</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="review-section">
        <div class="review-header">
            <h2>Customer Feedback</h2>
        </div>

        <?php if ($has_purchased): ?>
            <div class="review-form">
                <h3>Rate this product</h3>
                <form action="submit_review.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <div class="stars-container">
                        <input type="radio" id="star5" name="rating" value="5" required /><label for="star5"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1"><i class="fa-solid fa-star"></i></label>
                    </div>
                    <textarea name="comment" rows="3" placeholder="How was the product? Share your thoughts..." required></textarea>
                    <button type="submit" name="submit_review" class="btn-add-cart">Post My Review</button>
                </form>
            </div>
        <?php elseif(isset($_SESSION['user_id'])): ?>
            <div class="notice-box">
                <p><i class="fa-solid fa-info-circle"></i> Reviews are only available for verified purchasers after delivery.</p>
            </div>
        <?php endif; ?>

        <div class="reviews-list">
            <?php
            $review_sql = "SELECT r.*, u.full_name FROM reviews r 
                           JOIN users u ON r.user_id = u.user_id 
                           WHERE r.product_id = '$product_id' ORDER BY r.created_at DESC";
            $reviews = $conn->query($review_sql);

            if ($reviews->num_rows > 0):
                while($row = $reviews->fetch_assoc()): 
                    $initial = strtoupper(substr($row['full_name'], 0, 1));
            ?>
                    <div class="review-card">
                        <div class="review-meta">
                            <div class="user-avatar"><?php echo $initial; ?></div>
                            <div class="user-info">
                                <h4><?php echo htmlspecialchars($row['full_name']); ?></h4>
                                <span><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                            </div>
                        </div>
                        <div class="stars-display">
                            <?php for($i=1; $i<=5; $i++) {
                                echo ($i <= $row['rating']) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                            } ?>
                        </div>
                        <p class="review-text"><?php echo nl2br(htmlspecialchars($row['comment'])); ?></p>
                    </div>
                <?php endwhile;
            else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p style="color: var(--text-muted);">No reviews yet. Be the first to rate!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>