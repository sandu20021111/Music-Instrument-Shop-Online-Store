<?php
// Output buffering ආරම්භ කිරීම (Headers already sent error එක වැළැක්වීමට)
ob_start();
include 'db_connect.php';
include 'navbar.php';

// පාරිභෝගිකයා ලොග් වී නැතිනම් පලවා හැරීම
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// URL එකෙන් Order ID එක ලබා ගැනීම
if (!isset($_GET['id'])) {
    header("Location: my_orders.php");
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// ඇණවුමේ ප්‍රධාන තොරතුරු ලබා ගැනීම
$order_query = "SELECT * FROM orders WHERE order_id = '$order_id' AND user_id = '$user_id'";
$order_result = $conn->query($order_query);

if ($order_result->num_rows == 0) {
    echo "<div style='text-align:center; padding:50px;'><h3>Invalid Order Request.</h3><a href='my_orders.php'>Go Back</a></div>";
    exit();
}

$order = $order_result->fetch_assoc();

// භාණ්ඩ ලැයිස්තුව ලබා ගැනීම
$items_query = "SELECT * FROM order_items WHERE order_id = '$order_id'";
$items_result = $conn->query($items_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Invoice - #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --bg-light: #f8f9fa;
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg-light); color: var(--primary); }
        .container { max-width: 800px; margin: 50px auto; padding: 0 20px; }
        
        .detail-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th { text-align: left; padding: 12px; border-bottom: 2px solid #eee; color: #888; font-size: 0.8rem; text-transform: uppercase; }
        .items-table td { padding: 15px 12px; border-bottom: 1px solid #f9f9f9; }

        .summary { margin-top: 20px; text-align: right; line-height: 2; }
        .total-price { font-size: 1.5rem; font-weight: 700; color: var(--accent); }

        .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #666; font-size: 0.9rem; }

        /* --- PRINT STYLES --- */
        @media print {
            nav, footer, .btn-back, .download-section, .no-print {
                display: none !important;
            }
            body { background: white !important; }
            .container { margin: 0; width: 100%; max-width: 100%; }
            .detail-card { box-shadow: none !important; border: 1px solid #eee; border-radius: 0; }
            .total-price { color: #000 !important; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>

<div class="container">
    <a href="my_orders.php" class="btn-back no-print">← Back to My Orders</a>

    <div class="detail-card" id="invoice">
        <div class="status-header">
            <div>
                <h1 style="margin:0; color:var(--primary);">MELODY MASTERS</h1>
                <p style="color:#888; margin:5px 0;">Official Purchase Invoice</p>
                <h3 style="margin:10px 0 0;">Order #ORD-<?php echo $order['order_id']; ?></h3>
                <p style="color:#666; font-size:0.9rem;">Date: <?php echo date('M d, Y', strtotime($order['order_date'])); ?></p>
            </div>
            <div style="text-align: right;">
                <span style="background:#e6f9ed; color:#2ecc71; padding:8px 15px; border-radius:20px; font-weight:600; font-size:0.8rem; text-transform:uppercase;">
                    <?php echo $order['order_status']; ?>
                </span>
            </div>
        </div>

        <h3>Items Ordered</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                while($item = $items_result->fetch_assoc()): 
                    $line_total = $item['price'] * $item['quantity'];
                    $subtotal += $line_total;
                ?>
                <tr>
                    <td><strong><?php echo $item['product_name']; ?></strong></td>
                    <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td style="text-align:right;">Rs. <?php echo number_format($line_total, 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="summary">
            <p>Subtotal: <strong>Rs. <?php echo number_format($subtotal, 2); ?></strong></p>
            <p>Shipping Fee: <strong><?php echo ($order['shipping_cost'] == 0) ? "Free" : "Rs. ".number_format($order['shipping_cost'], 2); ?></strong></p>
            <p class="total-price">Total Amount: Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
        </div>

        <hr style="border:1px solid #eee; margin:30px 0;">
        
        <h3>Shipping Information</h3>
        <p style="color: #555; line-height: 1.6;">
            <?php echo isset($order['shipping_address']) ? $order['shipping_address'] : "Standard Delivery to your registered address."; ?>
        </p>

        <p style="text-align: center; color: #888; font-size: 0.8rem; margin-top: 40px;" class="print-only">
            Thank you for shopping with Melody Masters!<br>
            This is a computer-generated invoice.
        </p>
    </div>

    <div class="download-section" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="background: #27ae60; color: white; padding: 15px 35px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 1rem; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3); transition: 0.3s;">
            <i class="fa-solid fa-file-arrow-down"></i> Download Invoice (PDF)
        </button>
        <p style="color: #888; font-size: 0.8rem; margin-top: 10px;">Click the button and select "Save as PDF" in the print window.</p>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>