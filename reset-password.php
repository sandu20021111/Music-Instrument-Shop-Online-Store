<?php
include 'db_connect.php';

if (!isset($_GET["token"])) {
    die("Access denied. No token provided.");
}

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Invalid token.");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired. Please request a new password reset.");
}

$success = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users 
                SET password = ?, 
                    reset_token_hash = NULL, 
                    reset_token_expires_at = NULL 
                WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $password_hash, $user["user_id"]);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - Melody Masters</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
:root {
    --primary: #2c3e50;
    --accent: #3498db;
    --success: #27ae60;
    --error: #e74c3c;
    --bg: #f8f9fa;
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
    margin: 0;
}

.reset-card {
    background: #ffffff;
    width: 100%;
    max-width: 420px;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
}

h2 {
    text-align: center;
    color: var(--primary);
    margin-bottom: 10px;
}

p {
    text-align: center;
    color: #636e72;
    font-size: 0.9rem;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--primary);
}

.password-wrapper {
    position: relative;
}

.password-wrapper input {
    width: 100%;
    padding: 12px 45px 12px 16px;
    border: 1.5px solid #edf2f7;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: 0.2s;
}

.password-wrapper input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
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
}

button:hover {
    background: #1e2b37;
}

.alert {
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    font-size: 0.9rem;
    margin-bottom: 20px;
}

.alert-error {
    background: #fff5f5;
    color: var(--error);
    border: 1px solid #fed7d7;
}

.alert-success {
    background: #f0fff4;
    color: var(--success);
    border: 1px solid #c6f6d5;
}

.login-btn {
    display: block;
    text-align: center;
    background: var(--primary);
    color: white;
    padding: 14px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
}
</style>
</head>

<body>

<div class="reset-card">
    <h2>New Password</h2>

<?php if ($success): ?>
    <div class="alert alert-success">
        Your password has been successfully updated!
    </div>
    <a href="login.php" class="login-btn">Login Now</a>
<?php else: ?>

    <p>Please enter a strong password for your Melody Masters account.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>New Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" required placeholder="••••••••">
                <span class="toggle-password" onclick="togglePassword('password','eye1')">
                    <i id="eye1" class="fa fa-eye"></i>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label>Confirm New Password</label>
            <div class="password-wrapper">
                <input type="password" name="confirm_password" id="confirm_password" required placeholder="••••••••">
                <span class="toggle-password" onclick="togglePassword('confirm_password','eye2')">
                    <i id="eye2" class="fa fa-eye"></i>
                </span>
            </div>
        </div>

        <button type="submit">Update Password</button>
    </form>

<?php endif; ?>
</div>

<script>
function togglePassword(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);

    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>