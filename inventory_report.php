<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php"); exit();
}

$sql = "SELECT p.*, c.category_name FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        ORDER BY c.category_name ASC";
$result = $conn->query($sql);

$total_inventory_value = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report - Melody Masters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --accent: #3498db;
            --sidebar-width: 260px;
        }

        body { font-family: 'Inter', 'Segoe UI', sans-serif; margin: 0; display: flex; background: #f9fafb; }
        
        /* Main Content Adjustment */
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            width: 100%; 
            transition: 0.3s;
        }

        .report-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .report-header { 
            text-align: center; 
            margin-bottom: 40px; 
            border-bottom: 2px solid #eee; 
            padding-bottom: 20px; 
        }

        .report-header h1 { margin: 0; color: var(--sidebar-bg); }
        .report-header p { margin: 5px 0; color: #666; font-size: 0.9rem; }

        /* Table Styles */
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8fafc; color: #333; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; }
        
        .low-stock { color: #e74c3c; font-weight: bold; background: #fff5f5; padding: 2px 5px; border-radius: 4px; }
        
        .summary-box { 
            margin-top: 30px; 
            text-align: right; 
            font-size: 1.2rem; 
            font-weight: bold; 
            color: var(--sidebar-bg);
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .no-print-btn { 
            background: #27ae60; color: white; padding: 12px 25px; border: none; 
            border-radius: 8px; cursor: pointer; font-size: 1rem; margin-bottom: 20px; 
            font-weight: bold; transition: 0.3s;
        }
        .no-print-btn:hover { background: #219150; transform: translateY(-2px); }

        /* --- RESPONSIVE QUERIES --- */

        @media (max-width: 992px) {
            :root { --sidebar-width: 80px; }
            .main-content { padding: 20px; }
        }

        @media (max-width: 768px) {
            :root { --sidebar-width: 0px; }
            .main-content { margin-left: 0; margin-bottom: 80px; }
            
            /* Table to Mobile List */
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            tr { border: 1px solid #eee; margin-bottom: 10px; border-radius: 8px; background: #fff; }
            td { border: none; position: relative; padding-left: 50%; text-align: right; font-size: 0.9rem; }
            td:before { 
                content: attr(data-label); position: absolute; left: 15px; 
                width: 45%; text-align: left; font-weight: bold; color: #666; 
            }
            .summary-box { text-align: center; font-size: 1rem; }
        }

        /* --- PRINT STYLES --- */
        @media print {
            .sidebar, .no-print-btn, .sidebar-footer { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
            .report-card { box-shadow: none; padding: 0; }
            body { background: white; }
            th { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact; }
            .low-stock { color: #e74c3c !important; }
        }
    </style>
</head>
<body>

    <?php include 'sidebar_staff.php'; ?>

    <div class="main-content">
        <button class="no-print-btn" onclick="window.print()">
            <i class="fa fa-file-pdf"></i> Print / Download PDF Report
        </button>

        <div class="report-card">
            <div class="report-header">
                <h1>MELODY MASTERS</h1>
                <h3>Inventory Status Report</h3>
                <p><i class="fa fa-calendar"></i> Date: <?php echo date('F j, Y, g:i a'); ?></p>
                <p><i class="fa fa-user"></i> Prepared By: <?php echo $_SESSION['full_name']; ?> (<?php echo $_SESSION['role']; ?>)</p>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Unit Price</th>
                            <th>In Stock</th>
                            <th>Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): 
                            $line_total = $row['price'] * $row['stock_quantity'];
                            $total_inventory_value += $line_total;
                        ?>
                        <tr>
                            <td data-label="ID">#<?php echo $row['product_id']; ?></td>
                            <td data-label="Product"><strong><?php echo $row['product_name']; ?></strong></td>
                            <td data-label="Category"><?php echo $row['category_name']; ?></td>
                            <td data-label="Brand"><?php echo $row['brand']; ?></td>
                            <td data-label="Unit Price">Rs. <?php echo number_format($row['price'], 2); ?></td>
                            <td data-label="In Stock" class="<?php echo ($row['stock_quantity'] < 5) ? 'low-stock' : ''; ?>">
                                <?php echo $row['stock_quantity']; ?> Units
                                <?php echo ($row['stock_quantity'] < 5) ? ' (Low)' : ''; ?>
                            </td>
                            <td data-label="Total Value">Rs. <?php echo number_format($line_total, 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="summary-box">
                Total Estimated Inventory Value: Rs. <?php echo number_format($total_inventory_value, 2); ?>
            </div>
        </div>
    </div>

</body>
</html>