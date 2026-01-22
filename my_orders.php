<?php
include 'db_connect.php';
include 'navbar.php';

// පාරිභෝගිකයා ලොග් වී නැතිනම් පලවා හැරීම
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ඇණවුම් ලබා ගැනීම
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --bg-light: #f8f9fa;
            --white: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            color: var(--primary);
        }

        .orders-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .header-section {
            margin-bottom: 30px;
        }

        .header-section h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        /* --- Table Styling --- */
        .table-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #fdfdfd;
            padding: 18px 20px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            border-bottom: 1px solid #eee;
        }

        td {
            padding: 18px 20px;
            border-bottom: 1px solid #f9f9f9;
            font-size: 0.95rem;
        }

        tr:last-child td { border-bottom: none; }

        .order-id {
            font-weight: 700;
            color: var(--accent);
        }

        /* --- Status Badges --- */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-pending { background: #fff9e6; color: #f1c40f; }
        .status-shipped { background: #e1f0fa; color: #3498db; }
        .status-delivered { background: #e6f9ed; color: #2ecc71; }
        .status-default { background: #f4f4f4; color: #888; }

        .btn-view {
            text-decoration: none;
            color: var(--accent);
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .btn-view:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        /* --- Empty State --- */
        .empty-orders {
            text-align: center;
            padding: 80px 20px;
            background: var(--white);
            border-radius: 15px;
        }

        .empty-orders p { color: #888; margin-bottom: 20px; }

        .btn-shop {
            background: var(--accent);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        @media (max-width: 768px) {
            table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body>

<div class="orders-container">
    <div class="header-section">
        <h2>My Order History</h2>
        <p style="color: #888;">Track and manage your musical instrument purchases.</p>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Shipping</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="order-id">#ORD-<?php echo $row['order_id']; ?></td>
                        <td style="color: #666;"><?php echo date('M d, Y', strtotime($row['order_date'])); ?></td>
                        <td style="font-weight: 600;">Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                        <td>
                            <?php echo ($row['shipping_cost'] == 0) ? "<span style='color:#2ecc71; font-weight:600;'>Free</span>" : "Rs. ".number_format($row['shipping_cost'], 2); ?>
                        </td>
                        <td>
                            <?php 
                                $statusClass = 'status-default';
                                if($row['order_status'] == 'Pending') $statusClass = 'status-pending';
                                elseif($row['order_status'] == 'Shipped') $statusClass = 'status-shipped';
                                elseif($row['order_status'] == 'Delivered') $statusClass = 'status-delivered';
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo $row['order_status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="order_details.php?id=<?php echo $row['order_id']; ?>" class="btn-view">Details →</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-orders">
            <img src="https://cdn-icons-png.flaticon.com/512/3500/3500833.png" width="80" style="opacity: 0.2; margin-bottom: 20px;">
            <h3>No orders found</h3>
            <p>You haven't placed any orders with Melody Masters yet.</p>
            <br>
            <a href="shop.php" class="btn-shop">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>