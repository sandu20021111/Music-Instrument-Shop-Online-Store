<?php
include 'db_connect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Orders ලබා ගැනීම
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
$result = $conn->query($sql);

// Digital Products ලබා ගැනීම
$digital_sql = "SELECT oi.*, o.order_status, o.order_date, p.product_type, p.download_file 
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.order_id
                JOIN products p ON oi.product_id = p.product_id
                WHERE o.user_id = '$user_id' AND p.product_type = 'Digital'
                ORDER BY o.order_date DESC";
$digital_result = $conn->query($digital_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Library - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --bg-light: #f8f9fa;
            --white: #ffffff;
            --text: #333;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-light); 
            color: var(--text);
            margin: 0;
            line-height: 1.6;
        }

        .container { max-width: 1100px; margin: 50px auto; padding: 0 20px; }

        /* --- Section Titles --- */
        .section-header {
            border-left: 5px solid var(--accent);
            padding-left: 15px;
            margin-bottom: 30px;
        }

        .section-header h2 { 
            color: var(--primary); 
            margin: 0; 
            font-size: 1.8rem; 
            font-weight: 700;
        }

        
        /* --- Order History Table --- */
        .table-container {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        table { width: 100%; border-collapse: collapse; }
        th { background: var(--primary); color: white; padding: 18px; text-align: left; font-size: 0.9rem; }
        td { padding: 18px; border-bottom: 1px solid #f2f2f2; font-size: 0.95rem; }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .delivered { background: #d4edda; color: #155724; }
        .pending { background: #fff3cd; color: #856404; }

        .btn-details {
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
        }

        

    </style>
</head>
<body>

<div class="container">
    
    

    <div class="section-header">
        <h2>My Order History</h2>
    </div>

    <div class="table-container">
        <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight: 600;">#ORD-<?php echo $row['order_id']; ?></td>
                    <td><?php echo date('M d, Y', strtotime($row['order_date'])); ?></td>
                    <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                    <td>
                        <?php 
                            $st = strtolower($row['order_status']);
                            echo "<span class='status-badge $st'>{$row['order_status']}</span>";
                        ?>
                    </td>
                    <td>
                        <a href="order_details.php?id=<?php echo $row['order_id']; ?>" class="btn-details">
                            View Details
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div style="padding: 40px; text-align: center;">
                <p>You haven't placed any orders yet.</p>
                <a href="shop.php" style="color: var(--accent); font-weight: 700;">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>