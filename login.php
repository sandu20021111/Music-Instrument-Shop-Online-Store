<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement (more secure)
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

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
            $error = "Invalid email or password!";
        }

    } else {
        $error = "Invalid email or password!";
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Inter', sans-serif;
    background-color: var(--bg);
    background-image: radial-gradient(#dcdde1 0.5px, transparent 0.5px);
    background-size: 20px 20px;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-card {
    background: #ffffff;
    width: 100%;
    max-width: 400px;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.05);
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

.form-group { margin-bottom: 20px; }

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
    transition: 0.2s;
}

input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 4px rgba(52,152,219,0.1);
}

.password-wrapper {
    position: relative;
}

.password-wrapper input {
    padding-right: 45px;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #636e72;
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
    transition: 0.3s;
    margin-top: 10px;
}

button:hover {
    background: #1e2b37;
}

.alert {
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.85rem;
    text-align: center;
}

.alert-error {
    background: #fff5f5;
    color: var(--error);
    border: 1px solid #feb2b2;
}

.alert-success {
    background: #f0fff4;
    color: var(--success);
    border: 1px solid #9ae6b4;
}

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
        <div class="alert alert-success">
            Registration successful! Please login.
        </div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="email@example.com" required>
        </div>

        <div class="form-group">
            <label>Password</label>

            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="••••••••" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i id="eyeIcon" class="fa fa-eye"></i>
                </span>
            </div>

            <div style="text-align:right; margin-top:8px;">
                <a href="forgot-password.php" style="color: var(--accent); font-size: 0.8rem; text-decoration: none; font-weight: 500;">
                    Forgot Password?
                </a>
            </div>
        </div>

        <button type="submit">Login</button>
    </form>

    <p class="footer-link">
        Don't have an account? <a href="register.php">Sign Up</a>
    </p>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>