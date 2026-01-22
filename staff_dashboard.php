<?php
include 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Staff' && $_SESSION['role'] !== 'Admin')) {
    header("Location: login.php");
    exit();
}

// --- 1. STOCK UPDATE LOGIC ---
if (isset($_POST['update_stock_btn'])) {
    $p_id = mysqli_real_escape_string($conn, $_POST['p_id']);
    $new_qty = mysqli_real_escape_string($conn, $_POST['stock_qty']);
    
    $update_sql = "UPDATE products SET stock_quantity = '$new_qty' WHERE product_id = '$p_id'";
    if ($conn->query($update_sql)) {
        header("Location: staff_dashboard.php?success=1");
        exit();
    }
}

// --- 2. DATA FETCHING ---
$total_p_res = $conn->query("SELECT COUNT(*) as total FROM products");
$total_products = $total_p_res->fetch_assoc()['total'];

$low_stock_res = $conn->query("SELECT COUNT(*) as low_count FROM products WHERE stock_quantity < 5");
$low_stock_count = $low_stock_res->fetch_assoc()['low_count'];

$low_stock_list = $conn->query("SELECT product_name, stock_quantity FROM products WHERE stock_quantity < 5 LIMIT 3");

$sql = "SELECT p.*, c.category_name FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        ORDER BY p.product_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Melody Masters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-dark: #2c3e50;
            --accent-blue: #3498db;
            --bg-light: #f4f7f6;
            --success-green: #27ae60;
            --danger-red: #e74c3c;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); margin: 0; display: flex; }

        /* Main Content */
        .main-content { 
            margin-left: var(--sidebar-width); 
            width: 100%;
            padding: 30px;
            transition: all 0.3s;
            min-height: 100vh;
        }

        /* Stats Cards - Responsive Flex */
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
        .stat-card { 
            flex: 1; min-width: 200px; background: white; padding: 20px; border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;
        }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }

        /* Alerts */
        .low-stock-alert { 
            background: #fff5f5; border-left: 5px solid var(--danger-red); padding: 15px; 
            border-radius: 10px; margin-bottom: 30px; display: flex; flex-direction: column; gap: 15px;
        }
        @media (min-width: 768px) { .low-stock-alert { flex-direction: row; justify-content: space-between; align-items: center; } }

        /* Inventory Table */
        .inventory-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 0.9rem; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }

        .stock-badge { padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; white-space: nowrap; }
        .stock-low { background: #fee2e2; color: var(--danger-red); }
        .stock-ok { background: #dcfce7; color: var(--success-green); }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); padding: 20px; box-sizing: border-box; }
        .modal-content { background: white; margin: 50px auto; padding: 30px; border-radius: 15px; width: 100%; max-width: 400px; }

        /* --- RESPONSIVE MEDIA QUERIES --- */
        @media (max-width: 992px) {
            :root { --sidebar-width: 80px; }
            .main-content { padding: 20px; }
        }

        @media (max-width: 576px) {
            :root { --sidebar-width: 0px; }
            .main-content { margin-left: 0; margin-bottom: 70px; padding: 15px; }
            .header-section { flex-direction: column; align-items: flex-start; gap: 10px; }
            .stat-card { flex: none; width: 100%; }
        }
    </style>
</head>
<body>

<?php include 'sidebar_staff.php'; ?>

<div class="main-content">
    
    <div class="header-section" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin:0;"><i class="fa-solid fa-gauge"></i> Staff Dashboard</h2>
        <div style="background: white; padding: 8px 15px; border-radius: 30px; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.05); font-size: 0.9rem;">
             Hello, <?php echo explode(' ', $_SESSION['full_name'] ?? 'Staff')[0]; ?>
        </div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--accent-blue);"><i class="fa fa-guitar"></i></div>
            <div><small style="color: #64748b;">Total Products</small><h3 style="margin:0;"><?php echo $total_products; ?></h3></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--danger-red);"><i class="fa fa-triangle-exclamation"></i></div>
            <div><small style="color: #64748b;">Low Stock Items</small><h3 style="margin:0;"><?php echo $low_stock_count; ?></h3></div>
        </div>
    </div>

    <?php if ($low_stock_count > 0): ?>
    <div class="low-stock-alert">
        <div>
            <h4 style="margin:0; color: var(--danger-red);"><i class="fa fa-bell"></i> Critical Stock Alert!</h4>
            <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #742a2a;">
                Items running low: 
                <strong>
                <?php 
                $names = [];
                while($l = $low_stock_list->fetch_assoc()) { $names[] = $l['product_name'] . " (".$l['stock_quantity'].")"; }
                echo implode(', ', $names);
                ?>
                </strong>
            </p>
        </div>
        <a href="#inventory-section" style="background: var(--danger-red); color: white; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; text-align: center;">Restock Now</a>
    </div>
    <?php endif; ?>

    <div class="inventory-card" id="inventory-section">
        <h4 style="padding: 20px; margin: 0; border-bottom: 1px solid #eee; background: #fff;">Inventory Overview</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight:600; color: var(--primary-dark);"><?php echo $row['product_name']; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td>
                                <?php $is_low = ($row['stock_quantity'] < 5); ?>
                                <span class="stock-badge <?php echo $is_low ? 'stock-low' : 'stock-ok'; ?>">
                                    <?php echo $row['stock_quantity']; ?> Units
                                </span>
                            </td>
                            <td style="font-weight:700;">Rs. <?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <button class="btn-update" style="background: var(--accent-blue); color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;" 
                                        onclick="openModal('<?php echo $row['product_id']; ?>', '<?php echo addslashes($row['product_name']); ?>', '<?php echo $row['stock_quantity']; ?>')">
                                     Update
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="stockModal" class="modal">
    <div class="modal-content">
        <h3 id="m_title" style="margin-top:0; color: var(--primary-dark);">Update Stock</h3>
        <form method="POST">
            <input type="hidden" name="p_id" id="m_id">
            <label style="font-size: 0.9rem; color: #666;">Quantity in Hand:</label>
            <input type="number" name="stock_qty" id="m_qty" style="width: 100%; padding: 12px; margin: 10px 0 20px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;" required min="0">
            <button type="submit" name="update_stock_btn" class="save-btn" style="width:100%; padding: 12px; background: var(--primary-dark); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Save Changes</button>
            <button type="button" onclick="closeModal()" style="width:100%; background:none; border:none; margin-top:15px; color:#666; cursor:pointer; font-size: 0.9rem;">Cancel</button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('stockModal');
    function openModal(id, name, qty) {
        document.getElementById('m_id').value = id;
        document.getElementById('m_title').innerText = name;
        document.getElementById('m_qty').value = qty;
        modal.style.display = 'block';
    }
    function closeModal() { modal.style.display = 'none'; }
    window.onclick = function(event) { if (event.target == modal) closeModal(); }
</script>

</body>
</html>