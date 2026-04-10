<?php

$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    :root {
        --sidebar-bg: #2c3e50;
        --sidebar-hover: #34495e;
        --accent-blue: #3498db;
        --text-light: #ecf0f1;
        --danger-red: #ff7675;
        --sidebar-width: 260px;
    }

    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--sidebar-bg);
        color: white;
        position: fixed;
        left: 0;
        top: 0;
        box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .sidebar-header {
        padding: 25px 20px;
        text-align: center;
        background: rgba(0,0,0,0.1);
    }

    .sidebar-header h2 {
        margin: 0;
        font-size: 1.2rem;
        letter-spacing: 1px;
        color: var(--accent-blue);
        text-transform: uppercase;
        white-space: nowrap;
    }

    .sidebar-menu {
        flex: 1;
        padding: 20px 0;
        overflow-y: auto;
    }

   
    .sidebar-menu::-webkit-scrollbar { width: 5px; }
    .sidebar-menu::-webkit-scrollbar-thumb { background: var(--sidebar-hover); }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 14px 25px;
        color: var(--text-light);
        text-decoration: none;
        transition: 0.3s;
        font-size: 0.95rem;
        border-left: 4px solid transparent;
        white-space: nowrap;
    }

    .sidebar-menu a i {
        margin-right: 15px;
        width: 25px;
        font-size: 1.1rem;
        text-align: center;
    }

    .sidebar-menu a:hover {
        background: var(--sidebar-hover);
        padding-left: 30px;
        color: white;
    }

    .sidebar-menu a.active {
        background: var(--sidebar-hover);
        border-left: 4px solid var(--accent-blue);
        color: white;
        font-weight: 600;
    }

    .sidebar-footer {
        padding: 15px 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: var(--danger-red);
        text-decoration: none;
        border-radius: 8px;
        transition: 0.3s;
        font-weight: 500;
    }

    .logout-btn:hover {
        background: rgba(255, 118, 117, 0.1);
    }

    @media (max-width: 992px) {
        :root { --sidebar-width: 80px; }
        .sidebar-header h2, .sidebar-menu span, .logout-btn span {
            display: none;
        }
        .sidebar-menu a {
            justify-content: center;
            padding: 20px 0;
            border-left: none;
            border-bottom: 2px solid transparent;
        }
        .sidebar-menu a i { margin: 0; font-size: 1.3rem; }
        .sidebar-menu a.active {
            border-left: none;
            border-bottom: 2px solid var(--accent-blue);
            background: rgba(52, 152, 219, 0.1);
        }
        .logout-btn { justify-content: center; }
        .sidebar-menu a:hover { padding-left: 0; background: var(--sidebar-hover); }
    }

    @media (max-width: 576px) {
        .sidebar {
            width: 100%;
            height: 65px;
            top: auto;
            bottom: 0;
            flex-direction: row;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
        }
        .sidebar-header, .sidebar-footer { display: none; }
        .sidebar-menu {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            padding: 0;
            width: 100%;
        }
        .sidebar-menu a {
            flex: 1;
            flex-direction: column;
            padding: 10px 0;
            font-size: 0.7rem;
            border-bottom: none !important;
        }
        .sidebar-menu a i { margin-bottom: 4px; font-size: 1.1rem; }
        .sidebar-menu a.active {
            border-top: 3px solid var(--accent-blue);
            background: transparent;
            color: var(--accent-blue);
        }
        
    }
</style>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>Melody Staff</h2>
    </div>

    <div class="sidebar-menu">
        <a href="staff_dashboard.php" class="<?php echo ($current_page == 'staff_dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Overview</span>
        </a>

        <a href="inventory_management.php" class="<?php echo ($current_page == 'inventory_management.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Inventory</span>
        </a>

        <a href="add_product.php" class="<?php echo ($current_page == 'add_product.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-circle-plus"></i>
            <span>Add New</span>
        </a>

        <a href="inventory_report.php" class="<?php echo ($current_page == 'inventory_report.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>Reports</span>  
        </a>

        <a href="shop.php">
            <i class="fa-solid fa-store"></i>
            <span>Live Shop</span>
        </a>
    </div>

    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
</div>