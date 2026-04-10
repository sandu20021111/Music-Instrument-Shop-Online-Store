<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php");
    exit();
}

// Logic for Status Update
if (isset($_POST['update_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_query = "UPDATE orders SET order_status = '$new_status' WHERE order_id = '$order_id'";
    
    if ($conn->query($update_query)) {
        $msg = "Order #$order_id status updated to $new_status!";

        if ($new_status == 'Delivered') {
            $items_sql = "SELECT oi.product_id, oi.order_id, o.user_id, p.product_type 
                          FROM order_items oi
                          JOIN orders o ON oi.order_id = o.order_id
                          JOIN products p ON oi.product_id = p.product_id
                          WHERE oi.order_id = '$order_id'";
            
            $items_result = $conn->query($items_sql);

            if ($items_result && $items_result->num_rows > 0) {
                while ($item = $items_result->fetch_assoc()) {
                    if ($item['product_type'] == 'Digital') {
                        $p_id = $item['product_id'];
                        $u_id = $item['user_id'];
                        $o_id = $item['order_id'];
                        $expiry_period = date('Y-m-d H:i:s', strtotime('+7 days'));
                        $max_limit = 5;

                        $check_exists = "SELECT * FROM digital_downloads WHERE order_id = '$o_id' AND product_id = '$p_id'";
                        $check_res = $conn->query($check_exists);

                        if ($check_res && $check_res->num_rows == 0) {
                            $sql_dl = "INSERT INTO digital_downloads (order_id, product_id, user_id, max_limit, expiry_date) 
                                       VALUES ('$o_id', '$p_id', '$u_id', '$max_limit', '$expiry_period')";
                            $conn->query($sql_dl);
                        }
                    }
                }
            }
        }
    }
}

$orders_query = "SELECT orders.*, users.full_name 
                 FROM orders 
                 JOIN users ON orders.user_id = users.user_id 
                 ORDER BY order_date DESC";
$orders = $conn->query($orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --bg-body: #f8fafc;
            --sidebar-width: 280px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-body); 
            color: var(--primary);
            display: flex;
        }

        /* --- Sidebar (Global Admin Style) --- */
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            background: var(--primary); 
            color: white; 
            position: fixed; 
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar h2 { 
            font-size: 1.4rem; font-weight: 800; margin-bottom: 40px; 
            display: flex; align-items: center; gap: 12px; padding-left: 10px;
        }
        .sidebar h2 i { color: var(--accent); }

        .sidebar a { 
            display: flex; align-items: center; gap: 15px;
            padding: 14px 18px; color: #94a3b8; text-decoration: none; 
            border-radius: 12px; margin-bottom: 8px; font-weight: 500;
            transition: var(--transition);
        }

        .sidebar a:hover { background: rgba(255, 255, 255, 0.05); color: white; }
        .sidebar a.active-link { background: var(--accent); color: white; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2); }

        /* --- Main Content --- */
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px 50px; 
            width: 100%; 
        }

        .header-section { margin-bottom: 30px; }
        .header-section h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; }
        .header-section p { color: #64748b; margin-top: 5px; font-size: 0.95rem; }

        /* --- Table Card --- */
        .card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
        }

        .order-table { width: 100%; border-collapse: collapse; }
        .order-table th { 
            text-align: left; padding: 15px; color: #64748b; 
            font-size: 0.8rem; text-transform: uppercase; font-weight: 700;
            border-bottom: 2px solid #f1f5f9;
        }
        .order-table td { padding: 20px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }

        .order-id { font-weight: 800; color: var(--accent); }
        .customer-name { font-weight: 700; display: block; }
        .order-date { color: #94a3b8; font-size: 0.85rem; }

        /* --- Status Badges --- */
        .badge {
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pending { background: #fff7ed; color: #f97316; }
        .status-shipped { background: #eff6ff; color: #3b82f6; }
        .status-delivered { background: #ecfdf5; color: #10b981; }
        .status-cancelled { background: #fef2f2; color: #ef4444; }

        /* --- Form Elements --- */
        .update-form { display: flex; gap: 8px; align-items: center; }
        
        select {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 600;
            outline: none;
            transition: var(--transition);
        }
        select:focus { border-color: var(--accent); }

        .btn-update {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-update:hover { transform: translateY(-2px); background: var(--accent); }

        /* --- Alert --- */
        .alert {
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            background: #ecfdf5;
            color: #10b981;
            border: 1px solid #d1fae5;
            font-weight: 600;
            display: flex;
            align-items: center; gap: 12px;
        }

        @media (max-width: 1200px) {
            .sidebar { width: 80px; padding: 30px 10px; }
            .sidebar h2 span, .sidebar a span { display: none; }
            .main-content { margin-left: 80px; padding: 30px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-compact-disc"></i> <span>Admin Panel</span></h2>
        
        <a href="admin_dashboard.php">
            <i class="fa fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>
        <a href="add_staff.php">
            <i class="fa fa-user-plus"></i> <span>Add Staff</span>
        </a>
        <a href="orders_management.php" class="active-link">
            <i class="fa fa-shopping-cart"></i> <span>Manage Orders</span>
        </a>
        <a href="sales_report.php">
            <i class="fa fa-chart-line"></i> <span>Sales Reports</span>
        </a>
        <a href="shop.php">
            <i class="fa fa-eye"></i> <span>View Shop</span>
        </a>
        
        <div style="margin-top: auto;">
            <a href="logout.php" style="color: #ef4444; background: rgba(239, 68, 68, 0.05);">
                <i class="fa fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header-section">
            <h1>Order Management</h1>
            <p>Monitor customer purchases and track delivery logistics.</p>
        </div>

        <?php if(isset($msg)): ?>
            <div class="alert">
                <i class="fa-solid fa-circle-check"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order Details</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($orders->num_rows > 0): ?>
                        <?php while($row = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <span class="order-id">#ORD-<?php echo $row['order_id']; ?></span>
                                <span class="order-date"><?php echo date('M d, Y', strtotime($row['order_date'])); ?></span>
                            </td>
                            <td>
                                <span class="customer-name"><?php echo $row['full_name']; ?></span>
                            </td>
                            <td style="font-weight: 700; color: var(--primary);">
                                Rs. <?php echo number_format($row['total_amount'], 2); ?>
                            </td>
                            <td>
                                <span class="badge status-<?php echo strtolower($row['order_status']); ?>">
                                    <?php echo $row['order_status']; ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="update-form">
                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                    <select name="status">
                                        <option value="Pending" <?php if($row['order_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Shipped" <?php if($row['order_status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                        <option value="Delivered" <?php if($row['order_status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                        <option value="Cancelled" <?php if($row['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-update">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 50px; color: #94a3b8;">
                                <i class="fa-solid fa-box-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                No orders found in the system.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>