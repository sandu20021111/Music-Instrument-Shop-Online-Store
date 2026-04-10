<?php
session_start();
include 'db_connect.php';

// Admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php"); exit();
}

// Handle Delete Staff
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $delete_query = "DELETE FROM users WHERE user_id = '$id' AND role = 'Staff'";
    if ($conn->query($delete_query)) {
        $msg = "Staff member removed successfully!";
    }
}

// Handle Add Staff
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_staff'])) {
    $s_name = mysqli_real_escape_string($conn, $_POST['staff_name']);
    $s_email = mysqli_real_escape_string($conn, $_POST['staff_email']);
    $s_pass = password_hash($_POST['staff_password'], PASSWORD_DEFAULT);
    
    $check = $conn->query("SELECT email FROM users WHERE email = '$s_email'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO users (full_name, email, password, role) VALUES ('$s_name', '$s_email', '$s_pass', 'Staff')");
        $msg = "Staff member added successfully!";
    } else {
        $error = "Email address already exists!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff | Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --bg-body: #f8fafc;
            --sidebar-width: 280px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-body); 
            color: var(--primary);
            display: flex;
        }

        /* --- Sidebar (Same as Dashboard) --- */
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            background: var(--primary); 
            color: white; 
            position: fixed; 
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar h2 { 
            font-size: 1.4rem; 
            font-weight: 800; 
            margin-bottom: 40px; 
            display: flex; gap: 12px; padding-left: 10px;
        }

        .sidebar h2 i { color: var(--accent); }

        .sidebar a { 
            display: flex; align-items: center; gap: 15px;
            padding: 14px 18px; color: #94a3b8; text-decoration: none; 
            border-radius: 12px; margin-bottom: 8px; font-weight: 500;
            transition: var(--transition);
        }

        .sidebar a:hover { background: rgba(255, 255, 255, 0.05); color: white; }
        .sidebar a.active-link { background: var(--accent); color: white; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2); }

        /* --- Main Content --- */
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px 50px; 
            width: 100%; 
        }

        .header-section { margin-bottom: 40px; }
        .header-section h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; }
        .header-section p { color: #64748b; margin-top: 5px; }

        /* --- Layout Grid --- */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 30px;
            align-items: flex-start;
        }

        .card {
            background: white;
            padding: 35px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
        }

        .card h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: 25px; color: var(--primary); }

        /* --- Form Elements --- */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 8px; }

        input {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        input:focus { outline: none; border-color: var(--accent); background: #f0f7ff; }

        .password-wrapper { position: relative; }
        .toggle-password {
            position: absolute;
            right: 15px; top: 50%; transform: translateY(-50%);
            cursor: pointer; color: #94a3b8;
        }

        button {
            background: var(--accent);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            width: 100%;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.2);
            margin-top: 10px;
        }

        button:hover { transform: translateY(-3px); box-shadow: 0 12px 20px rgba(59, 130, 246, 0.3); background: #2563eb; }

        /* --- Table Styling --- */
        .staff-table { width: 100%; border-collapse: collapse; }
        .staff-table th { 
            text-align: left; padding: 15px; color: #64748b; 
            font-size: 0.8rem; text-transform: uppercase; font-weight: 700;
            border-bottom: 2px solid #f1f5f9;
        }
        .staff-table td { padding: 18px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        
        .staff-info span { display: block; font-weight: 700; color: var(--primary); }
        .staff-info small { color: #94a3b8; }

        .btn-delete {
            width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            background: #fef2f2; color: #ef4444;
            border-radius: 10px; text-decoration: none;
            transition: var(--transition);
        }
        .btn-delete:hover { background: #ef4444; color: white; transform: scale(1.1); }

        /* --- Alerts --- */
        .alert { 
            padding: 15px 20px; border-radius: 12px; margin-bottom: 30px; 
            font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
        .alert-error { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }

        @media (max-width: 1100px) {
            .content-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-compact-disc"></i> <span>Admin Panel</span></h2>
        
        <a href="admin_dashboard.php">
            <i class="fa fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>
        <a href="add_staff.php" class="active-link">
            <i class="fa fa-user-plus"></i> <span>Add Staff</span>
        </a>
        <a href="orders_management.php">
            <i class="fa fa-shopping-cart"></i> <span>Manage Orders</span>
        </a>
        <a href="sales_report.php">
            <i class="fa fa-chart-line"></i> <span>Sales Reports</span>
        </a>
        <a href="shop.php">
            <i class="fa fa-eye"></i> <span>View Shop</span>
        </a>
        
        <div style="margin-top: auto;">
            <a href="logout.php" style="color: #ef4444; background: rgba(239, 68, 68, 0.05);">
                <i class="fa fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header-section">
            <h1>Staff Management</h1>
            <p>Onboard and manage your team members at Melody Masters.</p>
        </div>

        <?php if(isset($msg)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $msg; ?></div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-error"><i class="fa-solid fa-circle-xmark"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="content-grid">
            <div class="card">
                <h3>Add New Member</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="staff_name" placeholder="Enter name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="staff_email" placeholder="email@melodymasters.lk" required>
                    </div>
                    <div class="form-group">
                        <label>Temporary Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="staff_password" id="staff_password" placeholder="••••••••" required>
                            <span class="toggle-password" onclick="togglePassword()">
                                <i id="eyeIcon" class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" name="add_staff">Register Staff Member</button>
                </form>
            </div>

            <div class="card">
                <h3>Current Staff Team</h3>
                <table class="staff-table">
                    <thead>
                        <tr>
                            <th>Member Details</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $staff_res = $conn->query("SELECT user_id, full_name, email FROM users WHERE role = 'Staff' ORDER BY user_id DESC");
                        if ($staff_res->num_rows > 0) {
                            while($row = $staff_res->fetch_assoc()) {
                                echo "<tr>
                                        <td>
                                            <div class='staff-info'>
                                                <span>{$row['full_name']}</span>
                                                <small>{$row['email']}</small>
                                            </div>
                                        </td>
                                        <td style='display: flex; justify-content: center;'>
                                            <a href='add_staff.php?delete_id={$row['user_id']}' 
                                               class='btn-delete' 
                                               onclick=\"return confirm('Are you sure you want to remove this team member?');\"
                                               title='Delete Staff'>
                                                <i class='fa fa-trash-can'></i>
                                            </a>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' style='text-align:center; color:#94a3b8; padding:40px;'>No staff members found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const passwordField = document.getElementById("staff_password");
        const eyeIcon = document.getElementById("eyeIcon");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
    </script>

</body>
</html>