<?php
include 'db_connect.php';
include 'navbar.php';

// Filter සහ Sort අගයන් URL එකෙන් ලබා ගැනීම
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
$cat_id = isset($_GET['cat_id']) ? mysqli_real_escape_string($conn, $_GET['cat_id']) : "";
$brand = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : "";
$sort = isset($_GET['sort']) ? $_GET['sort'] : "newest";

// මූලික SQL Query එක
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

// Sorting logic
switch ($sort) {
    case 'price_low': $sql .= " ORDER BY price ASC"; break;
    case 'price_high': $sql .= " ORDER BY price DESC"; break;
    case 'oldest': $sql .= " ORDER BY product_id ASC"; break;
    default: $sql .= " ORDER BY product_id DESC"; break;
}

$result = $conn->query($sql);
$categories = $conn->query("SELECT * FROM categories");
$brands_result = $conn->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != ''");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --text-muted: #636e72;
            --bg-light: #f8f9fa;
            --white: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            color: var(--primary);
        }

        /* Container Layout */
        .shop-container {
            display: grid;
            /* Sidebar එක 250px කට ස්ථාවර කළා, Card ප්‍රමාණයට බලපෑමක් නොවන ලෙස */
            grid-template-columns: 250px 1fr; 
            gap: 30px;
            padding: 0 10% 60px 10%;
            align-items: start;
        }

        /* Sidebar Styling */
        .filter-sidebar {
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .filter-sidebar h3 { font-size: 1rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .filter-group { margin-bottom: 15px; }
        .filter-group label { font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 5px; }

        .filter-select, .search-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        /* Product Card */
        .product-card {
            background: var(--white);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            transition: 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-card:hover { transform: translateY(-5px); }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .price { font-size: 1.1rem; font-weight: 700; color: var(--accent); margin: 5px 0; }

        .btn-view {
            background: var(--primary);
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.85rem;
            display: block;
        }

        /* --- RESPONSIVE BREAKPOINTS --- */

        /* Tablets (iPad etc.) */
        @media (max-width: 1024px) {
            .shop-container {
                grid-template-columns: 240px 1fr;
                gap: 20px;
            }
        }

        /* Mobile Devices */
        @media (max-width: 768px) {
            .shop-container {
                grid-template-columns: 1fr; /* එක පේළියට ගෙන එයි */
                padding: 10px 20px;
            }

            .filter-sidebar {
                position: relative; /* Sticky ඉවත් කරයි */
                top: 0;
                margin-bottom: 20px;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); /* කුඩා තිර වල කාඩ් 2ක් පෙන්වීමට */
                gap: 15px;
            }
            
            .product-card {
                padding: 10px;
            }
            
            .product-card img {
                height: 120px;
            }
        }

        /* Very Small Phones */
        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr; /* එක පේළියට එක කාඩ් එක බැගින් */
            }
        }
    </style>
</head>
<body>
    <div>
        <h1 style="text-align:center; margin:40px 0 20px;"></h1>
    </div>

    <div class="shop-container">
        <aside class="filter-sidebar">
            <form method="GET" action="shop.php">
                <h3><i class="fa fa-search"></i> Search</h3>
                <div class="filter-group">
                    <input type="text" name="search" class="search-input" placeholder="Search product..." value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <hr style="border:0; border-top:1px solid #eee; margin:15px 0;">

                <h3><i class="fa fa-filter"></i> Filters</h3>
                
                <div class="filter-group">
                    <label>Sort By</label>
                    <select name="sort" class="filter-select" onchange="this.form.submit()">
                        <option value="newest" <?php if($sort == 'newest') echo 'selected'; ?>>Newest</option>
                        <option value="price_low" <?php if($sort == 'price_low') echo 'selected'; ?>>Price: Low-High</option>
                        <option value="price_high" <?php if($sort == 'price_high') echo 'selected'; ?>>Price: High-Low</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Category</label>
                    <select name="cat_id" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php if($cat_id == $cat['category_id']) echo 'selected'; ?>>
                                <?php echo $cat['category_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" style="width:100%; background:var(--accent); color:white; border:none; padding:10px; border-radius:8px; cursor:pointer; font-weight:600;">Apply</button>
                <a href="shop.php" style="display:block; text-align:center; margin-top:10px; color:#e74c3c; text-decoration:none; font-size:0.8rem;">Reset All</a>
            </form>
        </aside>

        <main class="product-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div>
                            <img src="uploads/<?php echo $row['product_image']; ?>" alt="Product">
                            <small style="color:var(--text-muted)"><?php echo $row['brand']; ?></small>
                            <h3 style="font-size: 0.95rem; margin: 5px 0;"><?php echo $row['product_name']; ?></h3>
                            <p class="price">Rs. <?php echo number_format($row['price'], 2); ?></p>
                        </div>
                        
                        <?php if($row['stock_quantity'] > 0): ?>
                            <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn-view">View Details</a>
                        <?php else: ?>
                            <p style="color:#e74c3c; font-size:0.8rem; font-weight:600;">Out of Stock</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; padding: 50px; color: #999;">No products found.</p>
            <?php endif; ?>
        </main>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>