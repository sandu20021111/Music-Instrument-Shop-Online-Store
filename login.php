<?php
include 'db_connect.php'; // මෙහි session_start() තිබිය යුතුය

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            if ($user['role'] == 'Admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] == 'Staff') {
                header("Location: staff_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --success: #27ae60;
            --error: #e74c3c;
            --text-main: #2d3436;
            --text-muted: #636e72;
            --bg: #f8f9fa;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            background-image: radial-gradient(#dcdde1 0.5px, transparent 0.5px);
            background-size: 20px 20px;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        .brand-logo {
            text-align: center;
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 8px;
            display: block;
            text-decoration: none;
        }

        h2 {
            text-align: center;
            font-weight: 500;
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--primary);
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #edf2f7;
            border-radius: 10px;
            font-size: 0.95rem;
            background: #fdfdfd;
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background: #1e2b37;
            box-shadow: 0 5px 15px rgba(44, 62, 80, 0.3);
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            text-align: center;
            border: 1px solid transparent;
        }

        .alert-error { background: #fff5f5; color: var(--error); border-color: #feb2b2; }
        .alert-success { background: #f0fff4; color: var(--success); border-color: #9ae6b4; }

        .footer-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .footer-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <a href="index.php" class="brand-logo">Melody Masters</a>
        <h2>Welcome back!</h2>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div class="alert alert-success">Registration successful! Please login.</div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="email@example.com" required>
            </div>

            <div class="form-group">
    <label>Password</label>
    <input type="password" name="password" placeholder="••••••••" required>
    
    <div style="text-align: right; margin-top: 8px;">
        <a href="forgot-password.php" style="color: var(--accent); font-size: 0.8rem; text-decoration: none; font-weight: 500;">Forgot Password?</a>
    </div>
</div>

            <button type="submit">Login</button>
        </form>
        
        <p class="footer-link">Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>

</body>
</html>