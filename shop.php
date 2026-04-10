<?php
include 'db_connect.php';
include 'navbar.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
$cat_id = isset($_GET['cat_id']) ? mysqli_real_escape_string($conn, $_GET['cat_id']) : "";
$brand = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : "";
$sort = isset($_GET['sort']) ? $_GET['sort'] : "newest";

$sql = "SELECT * FROM products WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (product_name LIKE '%$search%' OR brand LIKE '%$search%')";
}
if (!empty($cat_id)) {
    $sql .= " AND category_id = '$cat_id'";
}
if (!empty($brand)) {
    $sql .= " AND brand = '$brand'";
}

switch ($sort) {
    case 'price_low': $sql .= " ORDER BY price ASC"; break;
    case 'price_high': $sql .= " ORDER BY price DESC"; break;
    case 'oldest': $sql .= " ORDER BY product_id ASC"; break;
    default: $sql .= " ORDER BY product_id DESC"; break;
}

$result = $conn->query($sql);
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --text-muted: #7f8c8d;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            color: var(--primary);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .shop-header {
            padding: 30px 0;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }

        .shop-container {
            display: grid;
            grid-template-columns: 280px 1fr; 
            gap: 40px;
            padding: 0 8% 80px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .filter-sidebar {
            background: var(--white);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            position: sticky;
            top: 100px;
            height: fit-content;
            animation: fadeInUp 0.8s ease-out;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .filter-sidebar h3 { font-size: 1.1rem; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; font-weight: 700; }
        .filter-group { margin-bottom: 25px; }
        .filter-group label { font-size: 0.85rem; font-weight: 700; display: block; margin-bottom: 10px; color: var(--text-muted); }

        .filter-select, .search-input {
            width: 100%; padding: 12px 15px; border: 1.5px solid #eee; border-radius: 12px;
            outline: none; font-family: inherit; transition: var(--transition); background: #fdfdfd;
        }

        .filter-select:focus, .search-input:focus {
            border-color: var(--accent); box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            transition: var(--transition);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            border: 1px solid rgba(0,0,0,0.03);
            animation: fadeInUp 0.8s ease-out backwards;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        /* Digital Badge Styling */
        .digital-badge {
            position: absolute;
            top: 15px;
            right: 5px;
            background: rgba(52, 152, 219, 0.1);
            color: var(--accent);
            padding: 5px 7px;
            border-radius: 20px;
            font-size: 0.6rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 5px;
            border: 1px solid rgba(52, 152, 219, 0.2);
            backdrop-filter: blur(5px);
            z-index: 2;
        }

        .product-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-color: var(--accent);
        }

        .img-container {
            height: 180px; display: flex; align-items: center; justify-content: center;
            margin-bottom: 15px; border-radius: 15px; background: #f9f9f9; padding: 10px;
        }

        .product-card img { max-width: 100%; max-height: 100%; object-fit: contain; transition: var(--transition); }
        .product-card:hover img { transform: scale(1.1); }

        .brand-tag {
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            color: var(--accent); font-weight: 800; margin-bottom: 8px; display: block;
        }

        .price { font-size: 1.2rem; font-weight: 800; color: var(--primary); margin: 12px 0; }

        .btn-view {
            background: var(--primary); color: white; padding: 12px; text-decoration: none;
            border-radius: 12px; font-size: 0.9rem; font-weight: 700; transition: var(--transition);
            margin-top: auto; border: none; cursor: pointer;
        }

        .btn-view:hover {
            background: var(--accent); box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .stock-badge {
            color: #e74c3c; font-size: 0.85rem; font-weight: 700; padding: 10px;
            background: #fff5f5; border-radius: 12px; margin-top: auto;
        }

        @media (max-width: 1024px) {
            .shop-container { grid-template-columns: 1fr; padding: 0 5% 60px; }
            .filter-sidebar { position: relative; top: 0; }
        }

        @media (max-width: 768px) {
            .product-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; }
        }
    </style>
</head>
<body>

    <header class="shop-header">
        <h1 style="font-weight: 800; font-size: 2.5rem; margin: 0;">Our Collection</h1>
    </header>

    <div class="shop-container">
        <aside class="filter-sidebar">
            <form method="GET" action="shop.php">
                <h3><i class="fa fa-search"></i> Search</h3>
                <div class="filter-group">
                    <input type="text" name="search" class="search-input" placeholder="Search instruments..." value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <hr style="border:0; border-top:1px solid #eee; margin:25px 0;">

                <h3><i class="fa fa-sliders"></i> Filter By</h3>
                
                <div class="filter-group">
                    <label>Sort Result</label>
                    <select name="sort" class="filter-select" onchange="this.form.submit()">
                        <option value="newest" <?php if($sort == 'newest') echo 'selected'; ?>>Latest Arrivals</option>
                        <option value="price_low" <?php if($sort == 'price_low') echo 'selected'; ?>>Price: Low to High</option>
                        <option value="price_high" <?php if($sort == 'price_high') echo 'selected'; ?>>Price: High to Low</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Category</label>
                    <select name="cat_id" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Instruments</option>
                        <?php 
                        $categories->data_seek(0);
                        while($cat = $categories->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php if($cat_id == $cat['category_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" style="width:100%; background:var(--accent); color:white; border:none; padding:14px; border-radius:12px; cursor:pointer; font-weight:700; transition: var(--transition);">Apply Filters</button>
                <a href="shop.php" style="display:block; text-align:center; margin-top:15px; color:#e74c3c; text-decoration:none; font-size:0.85rem; font-weight:600;">Clear All</a>
            </form>
        </aside>

        <main class="product-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        
                        <?php if(isset($row['product_type']) && $row['product_type'] == 'Digital'): ?>
                            <div class="digital-badge">
                                <i class="fa-solid fa-bolt"></i> DIGITAL
                            </div>
                        <?php endif; ?>

                        <span class="brand-tag"><?php echo htmlspecialchars($row['brand']); ?></span>
                        <div class="img-container">
                            <img src="uploads/<?php echo $row['product_image']; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" onerror="this.src='assets/no-image.png'">
                        </div>
                        
                        <h3 style="font-size: 1rem; margin: 5px 0; height: 40px; overflow: hidden;"><?php echo htmlspecialchars($row['product_name']); ?></h3>
                        <p class="price">Rs. <?php echo number_format($row['price'], 2); ?></p>
                        
                        <?php 
                        // Digital products are always available
                        if(isset($row['product_type']) && $row['product_type'] == 'Digital'): 
                        ?>
                            <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn-view" style="background: var(--accent);">
                                <i class="fa-solid fa-download"></i> Instant Access
                            </a>
                        <?php elseif($row['stock_quantity'] > 0): ?>
                            <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn-view">View Details</a>
                        <?php else: ?>
                            <div class="stock-badge">Sold Out</div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 100px 0;">
                    <i class="fa-solid fa-face-frown" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
                    <p style="color: #999; font-size: 1.1rem;">No products match your criteria.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>