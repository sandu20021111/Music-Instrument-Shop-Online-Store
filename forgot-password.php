<?php
// Token එකක් සාදා Database එකට ඇතුළත් කිරීමේ Logic එක
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $token = bin2hex(random_bytes(16)); // අහඹු අකුරු වැලක්
    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // විනාඩි 30ක් වලංගුයි

    include 'db_connect.php';
    $sql = "UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $token_hash, $expiry, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $reset_link = "localhost/melody/reset-password.php?token=$token";
        $success_msg = "Please check your email. Reset link has been sent.";
        // සටහන: ඇත්තටම වැඩ කරන විට මෙහිදී echo කරනවා වෙනුවට email එකක් යැවිය යුතුය.
    } else {
        $error_msg = "No account found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --bg: #f8f9fa;
            --text-muted: #636e72;
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
            max-width: 400px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        h2 {
            margin-bottom: 10px;
            color: var(--primary);
            font-weight: 600;
        }

        p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--primary);
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #edf2f7;
            border-radius: 10px;
            font-size: 0.95rem;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
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
            transition: 0.3s;
        }

        button:hover {
            background: #1e2b37;
            transform: translateY(-1px);
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }
        .alert-success { background: #f0fff4; color: #27ae60; border: 1px solid #c6f6d5; }
        .alert-error { background: #fff5f5; color: #e74c3c; border: 1px solid #fed7d7; }

        .back-link {
            margin-top: 25px;
            display: block;
            text-decoration: none;
            color: var(--accent);
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="reset-card">
        <h2>Forgot Password?</h2>
        <p>Enter your email address and we'll send you a link to reset your password.</p>

        <?php if(isset($success_msg)): ?>
            <div class="alert alert-success">
                <?php echo $success_msg; ?><br>
                <small style="word-break: break-all; color: #666;"><?php echo $reset_link; ?></small>
            </div>
        <?php endif; ?>

        <?php if(isset($error_msg)): ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter your registered email">
            </div>
            <button type="submit">Send Reset Link</button>
        </form>

        <a href="login.php" class="back-link">← Back to Login</a>
    </div>

</body>
</html>