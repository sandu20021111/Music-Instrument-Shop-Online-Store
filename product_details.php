<?php
include 'db_connect.php';
include 'navbar.php';

// URL එකෙන් Product ID එක ලබා ගැනීම
if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // භාණ්ඩයේ විස්තර ලබා ගැනීම
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

// පාරිභෝගිකයා මෙම භාණ්ඩය මිලදී ගෙන ඇත්දැයි පරීක්ෂා කිරීම
$has_purchased = false;
$download_info = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // 1. Review දැමීමට අවසර ඇත්දැයි බැලීම (Delivered status)
    $check_purchase = "SELECT * FROM orders o 
                       JOIN order_items oi ON o.order_id = oi.order_id 
                       WHERE o.user_id = '$user_id' 
                       AND oi.product_id = '$product_id' 
                       AND o.order_status = 'Delivered'"; 

    $purchase_result = $conn->query($check_purchase);
    if ($purchase_result && $purchase_result->num_rows > 0) {
        $has_purchased = true;
    }

    // 2. ඩිජිටල් භාණ්ඩයක් නම් Download විස්තර ලබා ගැනීම
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
            --warning: #f39c12;
        }

        body { font-family: 'Inter', sans-serif; background-color: white; margin: 0; color: var(--primary); line-height: 1.6; }
        .product-page-container { max-width: 1200px; margin: 50px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; }
        .product-image-container { background: var(--bg-light); border-radius: 20px; padding: 30px; display: flex; align-items: center; justify-content: center; position: sticky; top: 100px; height: fit-content; }
        .image-wrapper img { max-width: 100%; height: auto; transition: 0.4s ease; border-radius: 10px; }
        .image-wrapper:hover img { transform: scale(1.05); }
        .brand-label { text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: var(--accent); font-size: 0.85rem; }
        h1 { font-size: 2.5rem; margin: 10px 0; font-weight: 700; }
        .price-tag { font-size: 2rem; font-weight: 700; margin: 20px 0; }
        .specs-container { background: var(--bg-light); padding: 25px; border-radius: 15px; margin-top: 30px; }
        .purchase-box { margin-top: 30px; padding: 25px; border: 2px solid #eee; border-radius: 15px; }
        .btn-add-cart { background: var(--primary); color: white; padding: 15px 40px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; text-decoration: none; display: inline-block; }
        .btn-add-cart:hover { background: #1a252f; transform: translateY(-2px); }

        /* Digital Download Box */
        .download-box { margin-top: 20px; padding: 20px; background: #eef9ff; border: 2px dashed var(--accent); border-radius: 15px; text-align: center; }

        /* Modern Star Rating */
        .stars-container { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 10px; margin-bottom: 20px; }
        .stars-container input { display: none; }
        .stars-container label { font-size: 2rem; color: #ddd; cursor: pointer; transition: 0.2s; }
        .stars-container label:hover, .stars-container label:hover ~ label, .stars-container input:checked ~ label { color: #f1c40f; }

        /* Review Section */
        .review-section { max-width: 1200px; margin: 50px auto; padding: 40px 20px; border-top: 1px solid #eee; }
        .review-form { background: var(--bg-light); padding: 30px; border-radius: 15px; margin-bottom: 40px; }
        .review-form textarea { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 10px; margin: 10px 0; font-family: inherit; resize: vertical; }
        .review-card { border-bottom: 1px solid #eee; padding: 20px 0; }
        .stars { color: #f1c40f; margin-bottom: 5px; }

        @media (max-width: 850px) { .product-page-container { grid-template-columns: 1fr; } .product-image-container { position: static; } }
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
                        <p style="color: #e74c3c;">This download link has expired (<?php echo date('M d, Y', strtotime($download_info['expiry_date'])); ?>).</p>
                    <?php elseif ($no_limit): ?>
                        <p style="color: #e74c3c;">Download limit reached (<?php echo $download_info['max_limit']; ?>/<?php echo $download_info['max_limit']; ?>).</p>
                    <?php else: ?>
                        <p>Remaining: <?php echo ($download_info['max_limit'] - $download_info['download_count']); ?> downloads</p>
                        <a href="download.php?id=<?php echo $product['product_id']; ?>" class="btn-add-cart" style="background: var(--success);">
                            Download Now
                        </a>
                        <p style="font-size: 0.8rem; margin-top:10px; color: var(--text-muted);">Expires: <?php echo date('M d, Y', strtotime($download_info['expiry_date'])); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="purchase-box">
                <?php if ($product['stock_quantity'] > 0 || $product['product_type'] == 'Digital'): ?>
                    <div style="color: var(--success); font-weight: 600; margin-bottom: 15px;">
                        <i class="fa-solid fa-circle-check"></i> <?php echo ($product['product_type'] == 'Digital') ? 'Instant Digital Delivery' : $product['stock_quantity'] . ' in stock'; ?>
                    </div>
                    <form action="cart.php" method="POST" style="display: flex; gap: 10px;">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <?php if($product['product_type'] !== 'Digital'): ?>
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" style="width: 70px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <?php else: ?>
                            <input type="hidden" name="quantity" value="1">
                        <?php endif; ?>
                        <button type="submit" name="add_to_cart" class="btn-add-cart">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <p style="color: #e74c3c; font-weight: 700;">Out of Stock</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="review-section">
        <h2>Customer Reviews</h2>

        <?php if ($has_purchased): ?>
            <div class="review-form">
                <h3>Write a Review</h3>
                <form action="submit_review.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <label style="font-weight:600;">Your Rating:</label>
                    <div class="stars-container">
                        <input type="radio" id="star5" name="rating" value="5" required /><label for="star5"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1"><i class="fa-solid fa-star"></i></label>
                    </div>
                    <textarea name="comment" rows="4" placeholder="How was your experience with this product?" required></textarea>
                    <button type="submit" name="submit_review" class="btn-add-cart">Submit Review</button>
                </form>
            </div>
        <?php elseif(isset($_SESSION['user_id'])): ?>
            <div style="background: #fff4e5; padding: 15px; border-radius: 10px; margin-bottom: 30px; border-left: 5px solid var(--warning);">
                <p style="margin: 0; color: #663c00;"><i class="fa-solid fa-info-circle"></i> Once your order status is marked as 'Delivered', you can leave a review here.</p>
            </div>
        <?php else: ?>
            <p><a href="login.php" style="color: var(--accent); font-weight:600;">Login</a> to write a review.</p>
        <?php endif; ?>

        <div class="reviews-list">
            <?php
            $review_sql = "SELECT r.*, u.full_name FROM reviews r 
                           JOIN users u ON r.user_id = u.user_id 
                           WHERE r.product_id = '$product_id' ORDER BY r.created_at DESC";
            $reviews = $conn->query($review_sql);

            if ($reviews->num_rows > 0):
                while($row = $reviews->fetch_assoc()): ?>
                    <div class="review-card">
                        <div class="stars">
                            <?php for($i=1; $i<=5; $i++) {
                                echo ($i <= $row['rating']) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                            } ?>
                        </div>
                        <strong style="display: block; margin-bottom: 5px;"><?php echo htmlspecialchars($row['full_name']); ?></strong>
                        <p style="margin: 5px 0;"><?php echo nl2br(htmlspecialchars($row['comment'])); ?></p>
                        <small style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                    </div>
                <?php endwhile;
            else: ?>
                <p>No reviews yet. Be the first to share your experience!</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>