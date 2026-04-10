<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $cat_id = $_POST['category_id'];
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = $_POST['price'];
    $type = $_POST['product_type'];
    $specs = mysqli_real_escape_string($conn, $_POST['specifications']);
    
    // Digital නම් stock එක ඉතා ඉහළ අගයක් (Unlimited) ලෙස සලකමු
    $stock = ($type == 'Digital') ? 999999 : $_POST['stock'];
    
    $digital_file_name = null;
    $target_dir = "uploads/";
    $digital_dir = "uploads/digital_files/";
    
    if (!is_dir($target_dir)) { mkdir($target_dir); }
    if (!is_dir($digital_dir)) { mkdir($digital_dir, 0777, true); }

    $image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
    $target_image = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_image)) {
        $upload_ok = true;
        
        if ($type == 'Digital' && isset($_FILES['digital_file']) && $_FILES['digital_file']['error'] == 0) {
            $digital_file_name = time() . "_file_" . basename($_FILES["digital_file"]["name"]);
            $target_digital = $digital_dir . $digital_file_name;
            
            if (!move_uploaded_file($_FILES["digital_file"]["tmp_name"], $target_digital)) {
                $upload_ok = false;
                $error = "Digital file upload failed!";
            }
        }

        if ($upload_ok) {
            $sql = "INSERT INTO products (category_id, product_name, brand, price, stock_quantity, product_image, product_type, specifications, download_file) 
                    VALUES ('$cat_id', '$name', '$brand', '$price', '$stock', '$image_name', '$type', '$specs', '$digital_file_name')";
            
            if ($conn->query($sql)) { 
                $msg = "Product successfully added!"; 
            } else {
                $error = "Database Error: " . $conn->error;
            }
        }
    } else {
        $error = "Image upload failed!";
    }
}

$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Staff Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root { 
            --primary: #0f172a;
            --accent: #3b82f6;
            --success: #10b981;
            --bg-body: #f8fafc;
        }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; display: flex; margin: 0; background: var(--bg-body); }
        
        /* Sidebar container should match your dashboard style */
        .main-content { margin-left: 280px; padding: 40px; width: 100%; }
        
        .form-container { 
            background: white; 
            padding: 40px; 
            border-radius: 24px; 
            max-width: 800px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); 
            margin: 0 auto;
            border: 1px solid #f1f5f9;
        }

        h3 { font-weight: 800; font-size: 1.5rem; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        h3 i { color: var(--accent); }

        label { font-weight: 700; font-size: 0.8rem; color: #64748b; text-transform: uppercase; display: block; margin-top: 20px; }
        
        input, select, textarea { 
            width: 100%; padding: 12px 16px; margin-top: 8px; 
            border: 1.5px solid #e2e8f0; border-radius: 12px; 
            font-family: inherit; font-size: 0.95rem; outline: none; transition: 0.3s;
        }
        
        input:focus, select:focus, textarea:focus { border-color: var(--accent); background: #fff; }

        .form-row { display: flex; gap: 20px; }
        .form-group { flex: 1; }

        button { 
            background: var(--primary); color: white; border: none; padding: 16px; 
            cursor: pointer; border-radius: 12px; width: 100%; font-weight: 700; 
            margin-top: 30px; transition: 0.3s; font-size: 1rem;
        }
        
        button:hover { background: var(--accent); transform: translateY(-2px); }

        .alert { padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 600; }
        .alert-success { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
        .alert-error { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }

        #digital_file_section { 
            display: none; background: #f0f7ff; padding: 20px; 
            border-radius: 15px; margin-top: 20px; border: 2px dashed #3b82f6; 
        }
        
        /* Stock field wrapper for toggling */
        #stock_section { transition: 0.3s; }
    </style>
</head>
<body>

    <?php include 'sidebar_staff.php'; ?>

    <div class="main-content">
        <div class="form-container">
            <h3><i class="fa fa-plus-circle"></i> Add New Product</h3>
            
            <?php if(isset($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
            <?php if(isset($error)): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <label>Product Name</label>
                <input type="text" name="product_name" placeholder="e.g. Yamaha F310 Acoustic Guitar" required>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php while($row = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" placeholder="e.g. Yamaha" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" step="0.01" name="price" placeholder="0.00" required>
                    </div>
                    <div class="form-group" id="stock_section">
                        <label id="stock_label">Stock Quantity</label>
                        <input type="number" name="stock" id="stock_input" placeholder="e.g. 10" required>
                    </div>
                </div>

                <label>Product Type</label>
                <select name="product_type" id="product_type" onchange="toggleDigitalSection()">
                    <option value="Physical">Physical Instrument (Requires Stock)</option>
                    <option value="Digital">Digital Product (PDF/Lessons/Software)</option>
                </select>

                <div id="digital_file_section">
                    <label style="color: var(--accent); margin-top: 0;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload Digital File
                    </label>
                    <input type="file" name="digital_file" id="digital_input">
                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 8px;">
                        This file will be delivered to the customer's dashboard after a successful payment.
                    </p>
                </div>

                <label>Specifications</label>
                <textarea name="specifications" rows="4" placeholder="Describe the key features..." required></textarea>

                <label>Product Thumbnail Image</label>
                <input type="file" name="product_image" accept="image/*" required>

                <button type="submit" name="add_product">Publish Product</button>
            </form>
        </div>
    </div>

    <script>
    function toggleDigitalSection() {
        var type = document.getElementById("product_type").value;
        var digitalSection = document.getElementById("digital_file_section");
        var digitalInput = document.getElementById("digital_input");
        var stockSection = document.getElementById("stock_section");
        var stockInput = document.getElementById("stock_input");

        if (type === "Digital") {
            // Digital settings
            digitalSection.style.display = "block";
            digitalInput.required = true;
            
            // Hide and disable stock
            stockSection.style.visibility = "hidden"; // hidden keeps the layout intact
            stockInput.required = false;
            stockInput.value = "999999"; 
        } else {
            // Physical settings
            digitalSection.style.display = "none";
            digitalInput.required = false;
            
            // Show and enable stock
            stockSection.style.visibility = "visible";
            stockInput.required = true;
            stockInput.value = "";
        }
    }
    </script>

</body>
</html>