<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Data Fetching
$product_count = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$staff_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'Staff'")->fetch_assoc()['total'];
$customer_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'Customer'")->fetch_assoc()['total'];
$order_count = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$pending_count = $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'Pending'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Melody Masters</title>
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

        /* --- Sidebar --- */
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
            font-size: 1.4rem; 
            font-weight: 800; 
            margin-bottom: 40px; 
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 10px;
        }

        .sidebar h2 i { color: var(--accent); }

        .sidebar a { 
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 18px; 
            color: #94a3b8; 
            text-decoration: none; 
            border-radius: 12px;
            margin-bottom: 8px;
            font-weight: 500;
            transition: var(--transition);
        }

        .sidebar a i { font-size: 1.1rem; }

        .sidebar a:hover { 
            background: rgba(255, 255, 255, 0.05); 
            color: white; 
        }

        .sidebar a.active-link { 
            background: var(--accent); 
            color: white; 
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
        }

        /* --- Main Content --- */
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px 50px; 
            width: 100%; 
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .header-section h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; }
        .header-section p { color: #64748b; margin-top: 5px; }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            background: white;
            padding: 8px 20px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        /* --- Stats Grid --- */
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 25px; 
        }

        .stat-card { 
            background: white; 
            padding: 30px; 
            border-radius: 24px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); 
            transition: var(--transition);
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
            border-color: var(--accent);
        }

        .stat-card i { 
            font-size: 1.5rem; 
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            margin-bottom: 20px;
        }

        .stat-card h3 { font-size: 2rem; font-weight: 800; margin-bottom: 5px; }
        .stat-card p { color: #64748b; font-weight: 600; font-size: 0.9rem; }

        /* Icon Variants */
        .bg-products { background: #eff6ff; color: #3b82f6; }
        .bg-orders { background: #fff7ed; color: #f97316; }
        .bg-pending { background: #fef2f2; color: #ef4444; }
        .bg-staff { background: #f5f3ff; color: #8b5cf6; }
        .bg-users { background: #ecfdf5; color: #10b981; }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 30px 10px; }
            .sidebar h2, .sidebar a span { display: none; }
            .main-content { margin-left: 80px; padding: 30px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-compact-disc"></i> <span>Admin Panel</span></h2>
        
        <a href="admin_dashboard.php" class="active-link">
            <i class="fa fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>
        <a href="add_staff.php">
            <i class="fa fa-user-plus"></i> <span>Add Staff</span>
        </a>
        <a href="orders_management.php">
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
            <div>
                <h1>Welcome Back, Admin!</h1>
                <p>Here's what's happening at Melody Masters today.</p>
            </div>
            <div class="admin-profile">
                <i class="fa-solid fa-circle-user" style="font-size: 1.5rem; color: #cbd5e1;"></i>
                <span style="font-weight: 600; font-size: 0.9rem;">Administrator</span>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa fa-guitar bg-products"></i>
                <h3><?php echo $product_count; ?></h3>
                <p>Live Products</p>
            </div>

            <div class="stat-card">
                <i class="fa fa-shopping-bag bg-orders"></i>
                <h3><?php echo $order_count; ?></h3>
                <p>Total Orders</p>
            </div>

            <div class="stat-card">
                <i class="fa fa-clock bg-pending"></i>
                <h3 style="color: #ef4444;"><?php echo $pending_count; ?></h3>
                <p>Pending Review</p>
            </div>

            <div class="stat-card">
                <i class="fa fa-users-cog bg-staff"></i>
                <h3><?php echo $staff_count; ?></h3>
                <p>Staff Members</p>
            </div>

            <div class="stat-card">
                <i class="fa fa-users bg-users"></i>
                <h3><?php echo $customer_count; ?></h3>
                <p>Registered Users</p>
            </div>
        </div>

        </div>

</body>
</html>