<?php
include 'db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = $_POST['contact_number'];

    if (!preg_match('/^[0-9]+$/', $contact)) {
        $error = "Contact number must contain only numbers.";
    } else {
        $sql = "INSERT INTO users (full_name, email, password, role, contact_number) 
                VALUES ('$full_name', '$email', '$password', 'Customer', '$contact')";
        
        if ($conn->query($sql) === TRUE) {
            // Redirect to login.php immediately
            header("Location: login.php?msg=success");
            exit(); // Always call exit after a header redirect
        } else {
            $error = "Error: " . $conn->error;
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
            color: var(--text-main);
        }

        .register-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
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

        .form-group { margin-bottom: 18px; }

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

        button:hover { background: #1e2b37; box-shadow: 0 5px 15px rgba(44, 62, 80, 0.3); }

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

        .footer-link a { color: var(--accent); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

    <div class="register-card">
        <a href="index.php" class="brand-logo">Melody Masters</a>
        <h2>Create your account</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="email@example.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Min. 8 characters" required>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact_number" placeholder="07XXXXXXXX" required>
            </div>

            <button type="submit">Sign Up</button>
        </form>
        
        <p class="footer-link">Already have an account? <a href="login.php">Login</a></p>
    </div>

</body>
</html>