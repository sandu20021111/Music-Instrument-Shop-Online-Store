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
    
    // Security check: Digital product එකක්දැයි පරීක්ෂා කරයි
    $check_type = $conn->query("SELECT product_type FROM products WHERE product_id = '$p_id'")->fetch_assoc();
    if ($check_type['product_type'] !== 'Digital') {
        $update_sql = "UPDATE products SET stock_quantity = '$new_qty' WHERE product_id = '$p_id'";
        $conn->query($update_sql);
        header("Location: staff_dashboard.php?success=Stock Updated");
    } else {
        header("Location: staff_dashboard.php?error=Cannot update digital stock");
    }
    exit();
}

// --- 2. DATA FETCHING ---
$total_p_res = $conn->query("SELECT COUNT(*) as total FROM products");
$total_products = $total_p_res->fetch_assoc()['total'];

// Low stock ගණනය කරන්නේ Physical products සඳහා පමණි
$low_stock_res = $conn->query("SELECT COUNT(*) as low_count FROM products WHERE stock_quantity < 5 AND product_type != 'Digital'");
$low_stock_count = $low_stock_res->fetch_assoc()['low_count'];

$low_stock_list = $conn->query("SELECT product_name, stock_quantity FROM products WHERE stock_quantity < 5 AND product_type != 'Digital' LIMIT 3");

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
            --text-muted: #64748b;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); margin: 0; display: flex; }

        .main-content { margin-left: var(--sidebar-width); width: 100%; padding: 30px; min-height: 100vh; transition: 0.3s; }

        /* Stats Cards */
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
        .stat-card { 
            flex: 1; min-width: 200px; background: white; padding: 20px; border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }

        /* Alerts */
        .low-stock-alert { 
            background: #fff5f5; border-left: 5px solid var(--danger-red); padding: 15px; 
            border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;
        }

        /* Inventory Table */
        .inventory-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px; text-align: left; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 16px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }

        /* Status Badges */
        .status-badge { padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
        .badge-digital { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-low { background: #fee2e2; color: var(--danger-red); border: 1px solid #fecaca; }
        .badge-ok { background: #dcfce7; color: var(--success-green); border: 1px solid #bbf7d0; }

        /* Buttons */
        .btn-update { background: var(--primary-dark); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-update:hover { background: var(--accent-blue); }
        .btn-disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; border: 1px solid #e2e8f0; }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 100px auto; padding: 30px; border-radius: 15px; width: 100%; max-width: 400px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }

        @media (max-width: 768px) {
            :root { --sidebar-width: 0px; }
            .main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

<?php include 'sidebar_staff.php'; ?>

<div class="main-content">
    
    <div class="header-section" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin:0;"><i class="fa-solid fa-gauge"></i> Staff Dashboard</h2>
        <div style="background: white; padding: 8px 15px; border-radius: 30px; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
             Hello, <?php echo explode(' ', $_SESSION['full_name'] ?? 'Staff')[0]; ?>
        </div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--accent-blue);"><i class="fa fa-guitar"></i></div>
            <div><small style="color: var(--text-muted);">Total Products</small><h3 style="margin:0;"><?php echo $total_products; ?></h3></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--danger-red);"><i class="fa fa-triangle-exclamation"></i></div>
            <div><small style="color: var(--text-muted);">Low Stock Items</small><h3 style="margin:0;"><?php echo $low_stock_count; ?></h3></div>
        </div>
    </div>

    <?php if ($low_stock_count > 0): ?>
    <div class="low-stock-alert">
        <div>
            <h4 style="margin:0; color: var(--danger-red);"><i class="fa fa-bell"></i> Critical Stock Alert!</h4>
            <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #742a2a;">
                Items needing restock: <strong><?php 
                    $names = [];
                    while($l = $low_stock_list->fetch_assoc()) { $names[] = $l['product_name']; }
                    echo implode(', ', $names);
                ?></strong>
            </p>
        </div>
        <a href="#inventory-section" style="background: var(--danger-red); color: white; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-size: 0.85rem; font-weight: bold;">Restock Now</a>
    </div>
    <?php endif; ?>

    <div class="inventory-card" id="inventory-section">
        <h4 style="padding: 20px; margin: 0; background: #fff; border-bottom: 1px solid #edf2f7;">Inventory Overview</h4>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th>Category</th>
                        <th>Stock Status</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;"><?php echo $row['product_name']; ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $row['brand']; ?></div>
                        </td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td>
                            <?php if($row['product_type'] == 'Digital'): ?>
                                <span class="status-badge badge-digital"><i class="fa fa-cloud-download"></i> Unlimited</span>
                            <?php else: ?>
                                <?php $is_low = ($row['stock_quantity'] < 5); ?>
                                <span class="status-badge <?php echo $is_low ? 'badge-low' : 'badge-ok'; ?>">
                                    <i class="fa <?php echo $is_low ? 'fa-triangle-exclamation' : 'fa-check'; ?>"></i>
                                    <?php echo $row['stock_quantity']; ?> Units
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight:700; color: #1e293b;">Rs. <?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <?php if($row['product_type'] == 'Digital'): ?>
                                <button class="btn-update btn-disabled" disabled><i class="fa fa-lock"></i> Locked</button>
                            <?php else: ?>
                                <button class="btn-update" onclick="openModal('<?php echo $row['product_id']; ?>', '<?php echo addslashes($row['product_name']); ?>', '<?php echo $row['stock_quantity']; ?>')">
                                    <i class="fa fa-pen-to-square"></i> Update
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
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
            <label style="font-size: 0.9rem; color: #666; display: block; margin-bottom: 8px;">New Quantity in Hand:</label>
            <input type="number" name="stock_qty" id="m_qty" style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px;" required min="0">
            <button type="submit" name="update_stock_btn" style="width:100%; padding: 12px; background: var(--primary-dark); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Save Changes</button>
            <button type="button" onclick="closeModal()" style="width:100%; background:none; border:none; margin-top:15px; color:#666; cursor:pointer;">Cancel</button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('stockModal');
    function openModal(id, name, qty) {
        document.getElementById('m_id').value = id;
        document.getElementById('m_title').innerText = "Update: " + name;
        document.getElementById('m_qty').value = qty;
        modal.style.display = 'block';
    }
    function closeModal() { modal.style.display = 'none'; }
    window.onclick = function(event) { if (event.target == modal) closeModal(); }
</script>

</body>
</html>