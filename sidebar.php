<div class="sidebar">
    <h2>Melody Masters</h2>
    
    <?php if ($_SESSION['role'] == 'Admin'): ?>
        <a href="admin_dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php') ? 'active-link' : '' ?>">
            <i class="fa fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="add_staff.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'add_staff.php') ? 'active-link' : '' ?>">
            <i class="fa fa-user-plus"></i> Add Staff
        </a>
        <a href="orders_management.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'orders_management.php') ? 'active-link' : '' ?>">
            <i class="fa fa-shopping-cart"></i> Manage Orders
        </a>

        <a href="sales_report.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'sales_report.php') ? 'active-link' : '' ?>">
            <i class="fa fa-chart-line"></i> Sales Reports
        </a>
    <?php endif; ?>

    <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Staff'): ?>
        <a href="shop.php">
            <i class="fa fa-eye"></i> View Shop
        </a>
    <?php endif; ?>

    <a href="logout.php" style="color: #e74c3c; margin-top: 20px;">
        <i class="fa fa-sign-out-alt"></i> Logout
    </a>
</div>