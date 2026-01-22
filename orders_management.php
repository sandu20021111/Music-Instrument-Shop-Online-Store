<?php
session_start();
include 'db_connect.php';

// 1. ආරක්ෂාව: Admin හෝ Staff පමණක් ඇතුල් කර ගැනීම
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php");
    exit();
}

// 2. Order Status එක Update කිරීමේ Logic එක
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    // Status එක Update කිරීම
    $update_query = "UPDATE orders SET order_status = '$new_status' WHERE order_id = '$order_id'";
    
    if ($conn->query($update_query)) {
        $msg = "Order #$order_id status updated to $new_status!";

        // --- ඩිජිටල් නිෂ්පාදන සඳහා බාගත කිරීමේ අවසරය ලබා දීම (නව කොටස) ---
        if ($new_status == 'Delivered') {
            
            // Order එකේ ඇති භාණ්ඩ සහ ඒවායේ Product Type එක ලබා ගැනීම
            $items_sql = "SELECT oi.product_id, oi.order_id, o.user_id, p.product_type 
                          FROM order_items oi
                          JOIN orders o ON oi.order_id = o.order_id
                          JOIN products p ON oi.product_id = p.product_id
                          WHERE oi.order_id = '$order_id'";
            
            $items_result = $conn->query($items_sql);

            if ($items_result && $items_result->num_rows > 0) {
                while ($item = $items_result->fetch_assoc()) {
                    
                    // භාණ්ඩය 'Digital' වර්ගයේ එකක් නම් පමණක් දත්ත ඇතුළත් කිරීම
                    if ($item['product_type'] == 'Digital') {
                        $p_id = $item['product_id'];
                        $u_id = $item['user_id'];
                        $o_id = $item['order_id'];
                        
                        // කල් ඉකුත් වන දිනය (අද සිට දින 7ක්) සහ උපරිම වාර ගණන
                        $expiry_period = date('Y-m-d H:i:s', strtotime('+7 days'));
                        $max_limit = 5;

                        // දැනටමත් record එකක් ඇත්දැයි බැලීම (Duplicate වැළැක්වීමට)
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
        // --- ඩිජිටල් නිෂ්පාදන කොටස අවසන් ---

    } else {
        $error = "Update failed: " . $conn->error;
    }
}

// 3. Orders සියල්ල ලබා ගැනීම
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
    <title>Manage Orders - Melody Masters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; display: flex; background: #f4f7f6; }
        .sidebar { width: 250px; height: 100vh; background: #2c3e50; color: white; position: fixed; padding-top: 20px; }
        .sidebar h2 { text-align: center; font-size: 1.2rem; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 15px 25px; color: #ecf0f1; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover { background: #34495e; padding-left: 35px; }
        .active-link { background: #3498db !important; }
        .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); }
        .order-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #333; font-weight: 600; }
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: capitalize; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-shipped { background: #cfe2ff; color: #084298; }
        .status-delivered { background: #d1e7dd; color: #0f5132; }
        .status-cancelled { background: #f8d7da; color: #842029; }
        .update-btn { background: #3498db; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; transition: 0.2s; }
        .update-btn:hover { background: #2980b9; }
        select { padding: 7px; border-radius: 5px; border: 1px solid #ddd; outline: none; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

   <div class="sidebar">
        <h2>Melody Masters</h2>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
        <a href="add_staff.php"><i class="fa fa-user-plus"></i> Add Staff</a>
        <a href="orders_management.php" class="active-link"><i class="fa fa-shopping-cart"></i> Manage Orders</a>
        <a href="sales_report.php"><i class="fa fa-chart-line"></i> Sales Reports</a>
        <a href="shop.php"><i class="fa fa-eye"></i> View Shop</a>
        <a href="logout.php" style="color: #e74c3c;"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1>Order Management</h1>
        <p>Update order status to 'Delivered' to enable digital downloads for customers.</p>

        <?php if(isset($msg)): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div class="order-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $row['order_id']; ?></strong></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['order_date'])); ?></td>
                        <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                        <td>
                            <span class="badge status-<?php echo strtolower($row['order_status']); ?>">
                                <?php echo $row['order_status']; ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display: flex; gap: 10px;">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <select name="status">
                                    <option value="Pending" <?php if($row['order_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Shipped" <?php if($row['order_status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="Delivered" <?php if($row['order_status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                    <option value="Cancelled" <?php if($row['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="update-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>