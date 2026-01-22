<?php
session_start();
include 'db_connect.php';

// ආරක්ෂාව: Admin හෝ Staff පමණයි
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php"); exit();
}

// 1. භාණ්ඩයක් Delete කිරීමේ Logic එක
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $conn->query("DELETE FROM products WHERE product_id = '$id'");
    header("Location: inventory_management.php?msg=Product Deleted");
    exit();
}

// 2. Category එකක් Delete කිරීමේ Logic එක
if (isset($_GET['delete_category_id'])) {
    $cat_id = mysqli_real_escape_string($conn, $_GET['delete_category_id']);
    
    // මෙම Category එකට අදාළ Products තිබේදැයි බැලීම (Safety Check)
    $check_products = $conn->query("SELECT * FROM products WHERE category_id = '$cat_id'");
    
    if ($check_products->num_rows > 0) {
        header("Location: inventory_management.php?error=Cannot delete category. It contains products!");
    } else {
        $conn->query("DELETE FROM categories WHERE category_id = '$cat_id'");
        header("Location: inventory_management.php?msg=Category Deleted Successfully");
    }
    exit();
}

// 3. Category එකක් එකතු කිරීමේ Logic එක
if (isset($_POST['add_category_btn'])) {
    $cat_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $check = $conn->query("SELECT * FROM categories WHERE category_name = '$cat_name'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO categories (category_name) VALUES ('$cat_name')");
        header("Location: inventory_management.php?msg=Category Added Successfully");
    } else {
        header("Location: inventory_management.php?msg=Category Already Exists");
    }
    exit();
}

// 4. Product එකක් Update කිරීමේ Logic එක
if (isset($_POST['update_product_btn'])) {
    $id = $_POST['product_id'];
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $cat_id = $_POST['category_id'];
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $specs = mysqli_real_escape_string($conn, $_POST['specifications']);

    $img_query = "";
    if (!empty($_FILES["product_image"]["name"])) {
        $file_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        move_uploaded_file($_FILES["product_image"]["tmp_name"], "uploads/" . $file_name);
        $img_query = ", product_image = '$file_name'";
    }

    $sql_update = "UPDATE products SET 
                   product_name='$name', category_id='$cat_id', brand='$brand', 
                   price='$price', stock_quantity='$stock', specifications='$specs' $img_query 
                   WHERE product_id='$id'";

    if ($conn->query($sql_update)) {
        header("Location: inventory_management.php?msg=Product Updated Successfully");
        exit();
    }
}

// 5. Search කිරීමේ පහසුකම
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sql = "SELECT p.*, c.category_name FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        WHERE p.product_name LIKE '%$search%' 
        ORDER BY p.product_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory - Melody Masters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --accent: #3498db;
            --success: #27ae60;
            --light-bg: #f4f7f6;
            --sidebar-width: 260px;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; display: flex; background: var(--light-bg); min-height: 100vh; }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; width: 100%; transition: all 0.3s; }

        .action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 15px; flex-wrap: wrap; }
        .search-container { display: flex; flex: 1; min-width: 250px; }
        .search-box { flex: 1; padding: 12px; border-radius: 5px 0 0 5px; border: 1px solid #ddd; outline: none; }
        .search-btn { background: var(--sidebar-bg); color: white; border: none; padding: 0 20px; border-radius: 0 5px 5px 0; cursor: pointer; }
        
        .btn-group { display: flex; gap: 10px; }
        .add-new-btn { background: var(--success); color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }

        .table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: var(--sidebar-bg); color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #f1f1f1; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }

        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); overflow-y: auto; }
        .modal-content { background: white; margin: 30px auto; padding: 30px; border-radius: 15px; width: 90%; max-width: 600px; }
        .modal-form label { display: block; margin-top: 15px; font-weight: 600; color: #444; }
        .modal-form input, .modal-form textarea, .modal-form select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }

        .alert-error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }

        @media (max-width: 768px) {
            :root { --sidebar-width: 0px; }
            .main-content { margin-left: 0; padding: 15px; }
            .action-bar { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>

    <?php include 'sidebar_staff.php'; ?>

    <div class="main-content">
        <h2 style="color: var(--sidebar-bg); margin-bottom: 25px;">
            <i class="fa fa-boxes-stacked"></i> Inventory Management
        </h2>
        
        <?php if(isset($_GET['msg'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert-error">
                <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="action-bar">
            <form method="GET" class="search-container">
                <input type="text" name="search" class="search-box" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
            </form>
            
            <div class="btn-group">
                <button onclick="openManageCatModal()" class="add-new-btn" style="background: #9b59b6;">
                    <i class="fa fa-tasks"></i> Manage Categories
                </button>
                <button onclick="openCatModal()" class="add-new-btn" style="background: var(--accent);">
                    <i class="fa fa-folder-plus"></i> Add Category
                </button>
                <a href="add_product.php" class="add-new-btn">
                    <i class="fa fa-plus"></i> Add Product
                </a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Image"><img src="uploads/<?php echo $row['product_image']; ?>" class="product-img" onerror="this.src='assets/no-image.png'"></td>
                        <td data-label="Product"><strong><?php echo $row['product_name']; ?></strong><br><small><?php echo $row['brand']; ?></small></td>
                        <td data-label="Category"><?php echo $row['category_name']; ?></td>
                        <td data-label="Price">Rs. <?php echo number_format($row['price'], 2); ?></td>
                        <td data-label="Stock"><?php echo $row['stock_quantity']; ?></td>
                        <td data-label="Actions">
                            <a href="javascript:void(0)" onclick="openEditModal('<?php echo $row['product_id']; ?>', '<?php echo addslashes($row['product_name']); ?>', '<?php echo $row['category_id']; ?>', '<?php echo addslashes($row['brand']); ?>', '<?php echo $row['price']; ?>', '<?php echo $row['stock_quantity']; ?>', '<?php echo addslashes($row['specifications']); ?>')" style="color: var(--accent); margin-right: 15px;"><i class="fa fa-edit fa-lg"></i></a>
                            <a href="inventory_management.php?delete_id=<?php echo $row['product_id']; ?>" style="color: #e74c3c;" onclick="return confirm('Delete this product?')"><i class="fa fa-trash fa-lg"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="manageCatModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <h2 style="margin-top:0; color:var(--sidebar-bg); border-bottom: 2px solid #eee; padding-bottom:10px;">Manage Categories</h2>
            <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead><tr style="background:#f8f9fa;">
                        <th style="padding:10px; text-align:left;">Category Name</th>
                        <th style="padding:10px; text-align:center;">Action</th>
                    </tr></thead>
                    <tbody>
                        <?php 
                        $cats = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
                        while($c = $cats->fetch_assoc()): ?>
                        <tr>
                            <td style="padding:10px; border-bottom:1px solid #eee;"><?php echo $c['category_name']; ?></td>
                            <td style="padding:10px; border-bottom:1px solid #eee; text-align:center;">
                                <a href="inventory_management.php?delete_category_id=<?php echo $c['category_id']; ?>" 
                                   style="color: #e74c3c;" onclick="return confirm('Delete this category? This cannot be undone.')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <button onclick="closeManageCatModal()" class="add-new-btn" style="width:100%; background:#95a5a6; justify-content:center;">Close</button>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2 style="margin-top:0; color:var(--sidebar-bg); border-bottom: 2px solid #eee; padding-bottom:10px;">Edit Product Info</h2>
            <form method="POST" enctype="multipart/form-data" class="modal-form">
                <input type="hidden" name="product_id" id="edit_id">
                <label>Product Name</label><input type="text" name="product_name" id="edit_name" required>
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1;"><label>Category ID</label><input type="number" name="category_id" id="edit_cat" required></div>
                    <div style="flex: 1;"><label>Brand</label><input type="text" name="brand" id="edit_brand" required></div>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1;"><label>Price (Rs.)</label><input type="number" step="0.01" name="price" id="edit_price" required></div>
                    <div style="flex: 1;"><label>Stock</label><input type="number" name="stock" id="edit_stock" required></div>
                </div>
                <label>Specifications</label><textarea name="specifications" id="edit_specs" style="height: 80px;"></textarea>
                <label>Change Image</label><input type="file" name="product_image" accept="image/*">
                <div style="margin-top:25px; display:flex; gap:10px;">
                    <button type="submit" name="update_product_btn" class="add-new-btn" style="flex:2; justify-content: center;">Save Changes</button>
                    <button type="button" onclick="closeEditModal()" class="add-new-btn" style="flex:1; background:#95a5a6; justify-content: center;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="catModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <h2 style="margin-top:0; color:var(--sidebar-bg); border-bottom: 2px solid #eee; padding-bottom:10px;">Add New Category</h2>
            <form method="POST" class="modal-form">
                <label>Category Name</label><input type="text" name="category_name" required>
                <div style="margin-top:25px; display:flex; gap:10px;">
                    <button type="submit" name="add_category_btn" class="add-new-btn" style="flex:2; justify-content: center;">Add Category</button>
                    <button type="button" onclick="closeCatModal()" class="add-new-btn" style="flex:1; background:#95a5a6; justify-content: center;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, cat, brand, price, stock, specs) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_cat').value = cat;
            document.getElementById('edit_brand').value = brand;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_specs').value = specs;
            document.getElementById('editModal').style.display = 'block';
        }
        function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
        function openCatModal() { document.getElementById('catModal').style.display = 'block'; }
        function closeCatModal() { document.getElementById('catModal').style.display = 'none'; }
        function openManageCatModal() { document.getElementById('manageCatModal').style.display = 'block'; }
        function closeManageCatModal() { document.getElementById('manageCatModal').style.display = 'none'; }

        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) closeEditModal();
            if (event.target == document.getElementById('catModal')) closeCatModal();
            if (event.target == document.getElementById('manageCatModal')) closeManageCatModal();
        }
    </script>
</body>
</html>