<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php"); exit();
}

$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

$filter_condition = "WHERE order_status = 'Delivered' 
                     AND MONTH(order_date) = '$selected_month' 
                     AND YEAR(order_date) = '$selected_year'";

$revenue_res = $conn->query("SELECT SUM(total_amount) as total FROM orders $filter_condition");
$total_revenue = $revenue_res->fetch_assoc()['total'] ?? 0;

$sales_count = $conn->query("SELECT COUNT(*) as total FROM orders $filter_condition")->fetch_assoc()['total'];

$sales_details = $conn->query("SELECT orders.*, users.full_name 
                               FROM orders 
                               JOIN users ON orders.user_id = users.user_id 
                               $filter_condition 
                               ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report | Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --success: #10b981;
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

        /* --- Sidebar (Global Style) --- */
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

        .header-flex { 
            display: flex; justify-content: space-between; align-items: flex-end; 
            margin-bottom: 40px;
        }

        .header-section h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; }
        .header-section p { color: #64748b; margin-top: 5px; }

        /* --- Filters --- */
        .filter-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
            margin-bottom: 30px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .filter-group { display: flex; flex-direction: column; gap: 8px; }
        .filter-group label { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; }

        select {
            padding: 12px 20px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            font-weight: 600;
            font-family: inherit;
            outline: none;
            cursor: pointer;
        }

        .btn-generate {
            background: var(--primary);
            color: white;
            border: none;
            padding: 13px 25px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 22px;
        }
        .btn-generate:hover { background: var(--accent); transform: translateY(-2px); }

        .btn-print {
            background: var(--success);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; gap: 10px;
            transition: var(--transition);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
        }
        .btn-print:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(16, 185, 129, 0.3); }

        /* --- Stats Grid --- */
        .stats-grid { 
            display: grid; grid-template-columns: repeat(2, 1fr); 
            gap: 25px; margin-bottom: 40px; 
        }

        .stat-card {
            background: white;
            padding: 35px;
            border-radius: 24px;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .stat-icon {
            width: 70px; height: 70px;
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
        }

        .stat-info p { color: #64748b; font-size: 0.9rem; font-weight: 600; margin-bottom: 5px; }
        .stat-info h2 { font-size: 2rem; font-weight: 800; color: var(--primary); }

        /* --- Table Styling --- */
        .report-card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            border: 1px solid #f1f5f9;
        }

        .report-card h3 { margin-bottom: 25px; font-weight: 800; }

        .sales-table { width: 100%; border-collapse: collapse; }
        .sales-table th { 
            text-align: left; padding: 15px; color: #64748b; 
            font-size: 0.8rem; text-transform: uppercase; font-weight: 700;
            border-bottom: 2px solid #f1f5f9;
        }
        .sales-table td { padding: 20px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }

        .order-id { font-weight: 800; color: var(--accent); }
        .amount { font-weight: 800; color: var(--primary); }

        /* --- Print Media Query --- */
        @media print {
            .sidebar, .filter-card, .btn-print, .btn-generate { display: none !important; }
            .main-content { margin-left: 0; padding: 0; width: 100%; }
            .stat-card { border: 2px solid #eee; box-shadow: none; }
            body { background: white; }
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
        <a href="orders_management.php">
            <i class="fa fa-shopping-cart"></i> <span>Manage Orders</span>
        </a>
        <a href="sales_report.php" class="active-link">
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
        <div class="header-flex">
            <div class="header-section">
                <h1>Sales Intelligence</h1>
                <p>Analyzing revenue and performance for <strong><?php echo date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)); ?></strong></p>
            </div>
            <button class="btn-print" onclick="window.print()">
                <i class="fa fa-print"></i> Export Report
            </button>
        </div>

        <form method="GET" class="filter-card">
            <div class="filter-group">
                <label>Select Month</label>
                <select name="month">
                    <?php
                    for ($m = 1; $m <= 12; $m++) {
                        $monthValue = sprintf("%02d", $m);
                        $monthName = date('F', mktime(0, 0, 0, $m, 1));
                        $selected = ($monthValue == $selected_month) ? 'selected' : '';
                        echo "<option value='$monthValue' $selected>$monthName</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Select Year</label>
                <select name="year">
                    <?php
                    $start_year = 2024; 
                    $end_year = date('Y');
                    for ($y = $end_year; $y >= $start_year; $y--) {
                        $selected = ($y == $selected_year) ? 'selected' : '';
                        echo "<option value='$y' $selected>$y</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn-generate">Generate</button>
        </form>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fa fa-coins"></i>
                </div>
                <div class="stat-info">
                    <p>Monthly Revenue</p>
                    <h2>Rs. <?php echo number_format($total_revenue, 2); ?></h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                    <i class="fa fa-bag-shopping"></i>
                </div>
                <div class="stat-info">
                    <p>Total Sales</p>
                    <h2><?php echo $sales_count; ?> Orders</h2>
                </div>
            </div>
        </div>

        <div class="report-card">
            <h3>Transaction History</h3>
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Completion Date</th>
                        <th>Revenue Generated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($sales_details->num_rows > 0): ?>
                        <?php while($row = $sales_details->fetch_assoc()): ?>
                        <tr>
                            <td><span class="order-id">#ORD-<?php echo $row['order_id']; ?></span></td>
                            <td style="font-weight: 600;"><?php echo $row['full_name']; ?></td>
                            <td style="color: #64748b;"><?php echo date('d M, Y', strtotime($row['order_date'])); ?></td>
                            <td><span class="amount">Rs. <?php echo number_format($row['total_amount'], 2); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 50px; color: #94a3b8;">
                                <i class="fa-solid fa-chart-pie" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                No completed sales records for this period.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>