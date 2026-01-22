<?php
session_start();
include 'db_connect.php';

// 1. ආරක්ෂාව: Admin පමණක් ඇතුල් කර ගැනීම
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php"); exit();
}

// 2. Staff සාමාජිකයෙකු ඉවත් කිරීමේ (Delete) Logic එක
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // වැදගත්: Admin තමන්වම Delete කර ගැනීම වැළැක්වීම වඩාත් සුදුසුයි
    $delete_query = "DELETE FROM users WHERE user_id = '$id' AND role = 'Staff'";
    if ($conn->query($delete_query)) {
        $msg = "Staff member removed successfully!";
    }
}

// 3. Staff ඇතුළත් කිරීමේ Logic එක
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_staff'])) {
    $s_name = mysqli_real_escape_string($conn, $_POST['staff_name']);
    $s_email = mysqli_real_escape_string($conn, $_POST['staff_email']);
    $s_pass = password_hash($_POST['staff_password'], PASSWORD_DEFAULT);
    
    $check = $conn->query("SELECT email FROM users WHERE email = '$s_email'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO users (full_name, email, password, role) VALUES ('$s_name', '$s_email', '$s_pass', 'Staff')");
        $msg = "Staff member added successfully!";
    } else {
        $error = "Email already exists!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff - Melody Masters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; display: flex; background: #f4f7f6; }
        .sidebar { width: 250px; height: 100vh; background: #2c3e50; color: white; position: fixed; padding-top: 20px; }
        .sidebar h2 { text-align: center; font-size: 1.2rem; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 15px 25px; color: #ecf0f1; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover { background: #34495e; padding-left: 35px; }
        .active-link { background: #3498db !important; }

        .main-content { margin-left: 250px; padding: 40px; width: 100%; }
        .container { display: flex; gap: 30px; align-items: flex-start; }
        .form-box { background: white; padding: 25px; border-radius: 12px; flex: 1; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #3498db; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 5px; width: 100%; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }

        /* Delete Button Style */
        .btn-delete { color: #e74c3c; text-decoration: none; font-size: 1.1rem; transition: 0.2s; }
        .btn-delete:hover { color: #c0392b; }
        
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2><i class="fa fa-users-cog"></i> Staff Management</h2>
        
        <?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
        <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>

        <div class="container">
            <div class="form-box">
                <h3>Add New Staff Member</h3>
                <form method="POST">
                    <input type="text" name="staff_name" placeholder="Full Name" required>
                    <input type="email" name="staff_email" placeholder="Email Address" required>
                    <input type="password" name="staff_password" placeholder="Temporary Password" required>
                    <button type="submit" name="add_staff">Register Staff</button>
                </form>
            </div>
            
            <div class="form-box" style="flex: 1.5;">
                <h3>Current Staff Members</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $staff_res = $conn->query("SELECT user_id, full_name, email FROM users WHERE role = 'Staff'");
                        if ($staff_res->num_rows > 0) {
                            while($row = $staff_res->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td style='text-align: center;'>
                                            <a href='add_staff.php?delete_id={$row['user_id']}' 
                                               class='btn-delete' 
                                               onclick=\"return confirm('Are you sure you want to delete this staff member?');\">
                                                <i class='fa fa-trash-alt'></i>
                                            </a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center;'>No staff members found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>