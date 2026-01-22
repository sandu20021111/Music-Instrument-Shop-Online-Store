<?php
session_start();
include 'db_connect.php';

// ආරක්ෂාව: Admin පමණක් ඇතුල් කර ගැනීම
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php"); exit();
}

// Default අගයන් (දැනට පවතින වසර සහ මාසය)
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// SQL Filter Condition
$filter_condition = "WHERE order_status = 'Delivered' 
                     AND MONTH(order_date) = '$selected_month' 
                     AND YEAR(order_date) = '$selected_year'";

// 1. තෝරාගත් කාලයට අදාළ මුළු ආදායම
$revenue_res = $conn->query("SELECT SUM(total_amount) as total FROM orders $filter_condition");
$total_revenue = $revenue_res->fetch_assoc()['total'] ?? 0;

// 2. සාර්ථකව නිමකළ ඇණවුම් ගණන
$sales_count = $conn->query("SELECT COUNT(*) as total FROM orders $filter_condition")->fetch_assoc()['total'];

// 3. ඇණවුම් ලැයිස්තුව
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
    <title>Sales Report - Melody Masters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; display: flex; background: #f4f7f6; }
        .sidebar { width: 250px; height: 100vh; background: #2c3e50; color: white; position: fixed; padding-top: 20px; }
        .sidebar h2 { text-align: center; font-size: 1.2rem; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 15px 25px; color: #ecf0f1; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover { background: #34495e; padding-left: 35px; }
        .active-link { background: #3498db !important; }

        .main-content { margin-left: 250px; padding: 40px; width: 100%; }
        
        /* Filter Form Style */
        .filter-section { background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; display: flex; gap: 15px; align-items: flex-end; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .filter-section select, .filter-section button { padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        .filter-btn { background: #3498db; color: white; border: none !important; cursor: pointer; font-weight: bold; }
        .print-btn { background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }

        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .stat-card h2 { color: #2ecc71; margin: 10px 0; font-size: 2rem; }
        
        table { width: 100%; background: white; border-collapse: collapse; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #3498db; color: white; }

        @media print {
            .sidebar, .filter-section, .print-btn { display: none; }
            .main-content { margin-left: 0; padding: 0; width: 100%; }
            body { background: white; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Melody Masters</h2>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
        <a href="add_staff.php"><i class="fa fa-user-plus"></i> Add Staff</a>
        <a href="order_management.php"><i class="fa fa-shopping-cart"></i> Manage Orders</a>
        <a href="sales_report.php" class="active-link"><i class="fa fa-chart-line"></i> Sales Reports</a>
        <a href="shop.php"><i class="fa fa-eye"></i> View Shop</a>
        <a href="logout.php" style="color: #e74c3c;"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="report-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Monthly Sales Report</h1>
            <button class="print-btn" onclick="window.print()"><i class="fa fa-print"></i> Print Report</button>
        </div>

        <form method="GET" class="filter-section">
            <div>
                <label>Month:</label><br>
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
            <div>
                <label>Year:</label><br>
                <select name="year">
                    <?php
                    $start_year = 2024; // ඔබේ ව්‍යාපාරය ආරම්භ කළ වසර
                    $end_year = date('Y');
                    for ($y = $end_year; $y >= $start_year; $y--) {
                        $selected = ($y == $selected_year) ? 'selected' : '';
                        echo "<option value='$y' $selected>$y</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="filter-btn">Generate Report</button>
        </form>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa fa-money-bill-wave" style="font-size: 2rem; color: #2ecc71;"></i>
                <p>Total Revenue (<?php echo date('F', mktime(0, 0, 0, $selected_month, 1)) . " " . $selected_year; ?>)</p>
                <h2>Rs. <?php echo number_format($total_revenue, 2); ?></h2>
            </div>
            <div class="stat-card">
                <i class="fa fa-check-circle" style="font-size: 2rem; color: #3498db;"></i>
                <p>Successful Deliveries</p>
                <h2><?php echo $sales_count; ?> Orders</h2>
            </div>
        </div>

        <h3>Order Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Date</th>
                    <th>Total (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <?php if($sales_details->num_rows > 0): ?>
                    <?php while($row = $sales_details->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo date('Y-M-d', strtotime($row['order_date'])); ?></td>
                        <td><?php echo number_format($row['total_amount'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">No completed sales found for this period.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>