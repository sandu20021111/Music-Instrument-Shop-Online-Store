<?php
ob_start();
include 'db_connect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: my_orders.php");
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

$order_query = "SELECT * FROM orders WHERE order_id = '$order_id' AND user_id = '$user_id'";
$order_result = $conn->query($order_query);

if ($order_result->num_rows == 0) {
    echo "<div style='text-align:center; padding:100px; font-family:sans-serif;'>
            <i class='fa-solid fa-circle-exclamation' style='font-size:3rem; color:#e74c3c;'></i>
            <h3 style='margin-top:20px;'>Invalid Order Request</h3>
            <p style='color:#666;'>We couldn't find the invoice you're looking for.</p>
            <a href='my_orders.php' style='color:#3498db; text-decoration:none; font-weight:bold;'>← Go Back to My Orders</a>
          </div>";
    exit();
}

$order = $order_result->fetch_assoc();

$items_query = "SELECT * FROM order_items WHERE order_id = '$order_id'";
$items_result = $conn->query($items_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order_id; ?> | Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #0f172a;
            --accent: #2563eb;
            --success: #10b981;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-light); 
            color: var(--primary); 
            margin: 0;
            -webkit-print-color-adjust: exact;
        }

        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        
        /* --- Navigation Buttons --- */
        .no-print { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn-back { text-decoration: none; color: #64748b; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
        .btn-back:hover { color: var(--accent); }

        /* --- Invoice Card --- */
        .invoice-card {
            background: white;
            padding: 60px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }

        /* Top Accent Bar */
        .invoice-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 8px;
            background: linear-gradient(to right, var(--primary), var(--accent));
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 50px;
        }

        .brand h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -1px; margin: 0; color: var(--primary); }
        .brand p { color: #64748b; margin: 5px 0; font-size: 0.9rem; }

        .order-meta { text-align: right; }
        .order-meta h2 { font-size: 1.2rem; font-weight: 700; margin: 0; color: var(--accent); }
        .order-meta p { color: #64748b; font-size: 0.85rem; margin: 4px 0; }

        .status-badge {
            display: inline-block;
            background: #ecfdf5;
            color: var(--success);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 10px;
        }

        /* --- Billing Info Grid --- */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
            padding: 25px;
            background: #f8fafc;
            border-radius: 16px;
        }

        .info-block h4 { font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 10px; }
        .info-block p { font-size: 0.95rem; line-height: 1.6; margin: 0; font-weight: 500; }

        /* --- Table Styling --- */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th { 
            text-align: left; 
            padding: 15px; 
            border-bottom: 2px solid var(--border); 
            color: #64748b; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            font-weight: 700;
        }
        .items-table td { padding: 20px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        
        .product-name { font-weight: 700; color: var(--primary); display: block; }
        
        /* --- Summary Section --- */
        .invoice-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
        }

        .summary-box { width: 300px; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.95rem; }
        .summary-row.total { 
            margin-top: 15px; 
            padding-top: 15px; 
            border-top: 2px solid var(--border);
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--accent);
        }

        /* --- Buttons --- */
        .actions { text-align: center; margin-top: 40px; }
        .btn-print {
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px 40px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        }
        .btn-print:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(15, 23, 42, 0.3); }

        /* --- PRINT STYLES --- */
        @media print {
            body { background: white; }
            .no-print, nav, footer { display: none !important; }
            .container { margin: 0; max-width: 100%; width: 100%; }
            .invoice-card { box-shadow: none; border: 1px solid #eee; padding: 40px; border-radius: 0; }
            .info-grid { background: #fff !important; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="no-print">
        <a href="my_orders.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to My Orders</a>
    </div>

    <div class="invoice-card" id="printableInvoice">
        <div class="invoice-header">
            <div class="brand">
                <h1>MELODY MASTERS</h1>
                <p>Premium Instruments & Pro Audio</p>
                <p style="font-size: 0.8rem;"><i class="fa-solid fa-location-dot"></i> Colombo, Sri Lanka</p>
            </div>
            <div class="order-meta">
                <h2>INVOICE</h2>
                <p>Order ID: #ORD-<?php echo $order['order_id']; ?></p>
                <p>Date: <?php echo date('d M, Y', strtotime($order['order_date'])); ?></p>
                <div class="status-badge"><?php echo $order['order_status']; ?></div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-block">
                <h4>Customer Details</h4>
                <p><?php echo $_SESSION['user_name'] ?? 'Valued Customer'; ?></p>
                <p style="color:#64748b; font-weight:400; font-size:0.85rem;"><?php echo $_SESSION['user_email'] ?? ''; ?></p>
            </div>
            <div class="info-block">
                <h4>Shipping Address</h4>
                <p><?php echo !empty($order['shipping_address']) ? $order['shipping_address'] : "Standard Delivery - Address on Record"; ?></p>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: center;">Unit Price</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Total</th>
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
                    <td><span class="product-name"><?php echo $item['product_name']; ?></span></td>
                    <td style="text-align: center;">Rs. <?php echo number_format($item['price'], 2); ?></td>
                    <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                    <td style="text-align: right; font-weight:600;">Rs. <?php echo number_format($line_total, 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="invoice-footer">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span style="color:var(--success); font-weight:600;">
                        <?php echo ($order['shipping_cost'] == 0) ? "FREE" : "Rs. ".number_format($order['shipping_cost'], 2); ?>
                    </span>
                </div>
                <div class="summary-row total">
                    <span>Grand Total</span>
                    <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>

        <div style="margin-top: 60px; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 30px;">
            <p style="color:#94a3b8; font-size:0.85rem; margin:0;">
                Thank you for choosing <strong>Melody Masters</strong>. We appreciate your business!<br>
                This is a digital invoice. For support, contact us at support@melodymasters.lk
            </p>
        </div>
    </div>

    <div class="actions no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print" style="margin-right:8px;"></i> Print or Download PDF
        </button>
        <p style="color: #94a3b8; font-size: 0.8rem; margin-top: 15px;">
            <i class="fa-solid fa-circle-info"></i> To save as PDF, select "Save as PDF" in the Print Destination.
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>