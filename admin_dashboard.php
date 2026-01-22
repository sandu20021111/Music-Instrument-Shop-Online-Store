<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// 1. Stats ලබා ගැනීම
$product_count = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$staff_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'Staff'")->fetch_assoc()['total'];
$customer_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'Customer'")->fetch_assoc()['total'];

// 2. මුළු ඇණවුම් (Total Orders)
$order_count = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];

// 3. Pending ඇණවුම් පමණක් ලබා ගැනීම (order_status column එක භාවිතා කර)
$pending_count = $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'Pending'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Melody Masters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; display: flex; background: #f4f7f6; }
        .sidebar { width: 250px; height: 100vh; background: #2c3e50; color: white; position: fixed; padding-top: 20px; }
        .sidebar h2 { text-align: center; font-size: 1.2rem; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 15px 25px; color: #ecf0f1; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover { background: #34495e; padding-left: 35px; }
        .active-link { background: #3498db !important; }

        .main-content { margin-left: 250px; padding: 40px; width: 100%; }
        
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
            gap: 20px; 
        }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .stat-card i { font-size: 2rem; margin-bottom: 10px; }
        
        /* Icon Colors */
        .icon-products { color: #3498db; }
        .icon-orders { color: #e67e22; }
        .icon-pending { color: #e74c3c; } /* Pending සඳහා රතු පැහැය */
        .icon-staff { color: #9b59b6; }
        .icon-users { color: #2ecc71; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php" class="active-link"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
        <a href="add_staff.php"><i class="fa fa-user-plus"></i> Add Staff</a>
        <a href="orders_management.php"><i class="fa fa-shopping-cart"></i> Manage Orders</a>
        <a href="sales_report.php"><i class="fa fa-chart-line"></i> Sales Reports</a>
        <a href="shop.php"><i class="fa fa-eye"></i> View Shop</a>
        <a href="logout.php" style="color: #e74c3c;"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1>Welcome, Admin!</h1>
        <p>Real-time Overview of Melody Masters</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa fa-guitar icon-products"></i>
                <h3><?php echo $product_count; ?></h3>
                <p>Products</p>
            </div>

            <div class="stat-card">
                <i class="fa fa-shopping-bag icon-orders"></i>
                <h3><?php echo $order_count; ?></h3>
                <p>Total Orders</p>
            </div>

            <div class="stat-card">
                <i class="fa fa-clock icon-pending"></i>
                <h3 style="color: #e74c3c;"><?php echo $pending_count; ?></h3>
                <p>Pending Orders</p>
            </div>

            <div class="stat-card">
                <i class="fa fa-users-cog icon-staff"></i>
                <h3><?php echo $staff_count; ?></h3>
                <p>Staff</p>
            </div>

            <div class="stat-card">
                <i class="fa fa-users icon-users"></i>
                <h3><?php echo $customer_count; ?></h3>
                <p>Customers</p>
            </div>
        </div>
    </div>

</body>
</html>