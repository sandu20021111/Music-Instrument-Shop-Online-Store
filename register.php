<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contact = $_POST['contact_number'];

    // Validate contact number
    if (!preg_match('/^[0-9]+$/', $contact)) {
        $error = "Contact number must contain only numbers.";

    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";

    } else {

        // Check if email already exists
        $check_sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $check_result = $stmt->get_result();

        if ($check_result->num_rows > 0) {

            $error = "Email is already registered.";

        } else {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (full_name, email, password, role, contact_number) 
                    VALUES (?, ?, ?, 'Customer', ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $full_name, $email, $password_hash, $contact);

            if ($stmt->execute()) {
                header("Location: login.php?msg=success");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Melody Masters</title>

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

.register-card {
    background: #ffffff;
    width: 100%;
    max-width: 420px;
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

<div class="register-card">
    <a href="index.php" class="brand-logo">Melody Masters</a>
    <h2>Create your account</h2>

    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" required placeholder="Enter your full name">
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="email@example.com">
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" required placeholder="Minimum 8 characters">
                <span class="toggle-password" onclick="togglePassword()">
                    <i id="eyeIcon" class="fa fa-eye"></i>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="contact_number" required placeholder="07XXXXXXXX">
        </div>

        <button type="submit">Sign Up</button>
    </form>

    <p class="footer-link">
        Already have an account? <a href="login.php">Login</a>
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