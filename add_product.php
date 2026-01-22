<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// ආරක්ෂාව
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php"); 
    exit();
}

// භාණ්ඩයක් ඇතුළත් කිරීමේ Logic එක
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $cat_id = $_POST['category_id'];
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $type = $_POST['product_type'];
    $specs = mysqli_real_escape_string($conn, $_POST['specifications']);
    $digital_file_name = null;

    $target_dir = "uploads/";
    $digital_dir = "uploads/digital_files/";
    
    if (!is_dir($target_dir)) { mkdir($target_dir); }
    if (!is_dir($digital_dir)) { mkdir($digital_dir, 0777, true); }

    // 1. Image එක Upload කිරීම
    $image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
    $target_image = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_image)) {
        
        // 2. භාණ්ඩය Digital නම් ගොනුව Upload කිරීම
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
            // Database Insert (download_file column එක සමඟ)
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root { --sidebar-bg: #2c3e50; --accent: #3498db; --light-bg: #f4f7f6; --success: #27ae60; }
        body { font-family: 'Inter', sans-serif; display: flex; margin: 0; background: var(--light-bg); }
        .main-content { margin-left: 250px; padding: 20px; width: 100%; }
        .form-container { background: white; padding: 30px; border-radius: 15px; max-width: 700px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin: 20px auto; }
        label { font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-top: 15px; }
        input, select, textarea { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .form-row { display: flex; gap: 20px; }
        .form-group { flex: 1; }
        button { background: var(--success); color: white; border: none; padding: 15px; cursor: pointer; border-radius: 8px; width: 100%; font-weight: 700; margin-top: 25px; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        /* Digital File Section හංගන්න */
        #digital_file_section { display: none; background: #eef9ff; padding: 15px; border-radius: 10px; margin-top: 15px; border: 1px dashed var(--accent); }
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
                <input type="text" name="product_name" required>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" required>
                            <option value="">-- Select --</option>
                            <?php while($row = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" required>
                    </div>
                </div>

                <label>Product Type</label>
                <select name="product_type" id="product_type" onchange="toggleDigitalSection()">
                    <option value="Physical">Physical Instrument</option>
                    <option value="Digital">Digital Product (PDF/MP3/ZIP)</option>
                </select>

                <div id="digital_file_section">
                    <label style="color: var(--accent);"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Digital File (Target for download)</label>
                    <input type="file" name="digital_file" id="digital_input">
                    <small style="color: #555;">This file will be given to the customer after purchase.</small>
                </div>

                <label>Specifications</label>
                <textarea name="specifications" required></textarea>

                <label>Product Image (Display Thumbnail)</label>
                <input type="file" name="product_image" accept="image/*" required>

                <button type="submit" name="add_product">Save Product</button>
            </form>
        </div>
    </div>

    <script>
    function toggleDigitalSection() {
        var type = document.getElementById("product_type").value;
        var section = document.getElementById("digital_file_section");
        var input = document.getElementById("digital_input");

        if (type === "Digital") {
            section.style.display = "block";
            input.required = true;
        } else {
            section.style.display = "none";
            input.required = false;
        }
    }
    </script>

</body>
</html>