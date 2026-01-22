<?php 
include 'db_connect.php'; 
include 'navbar.php'; 

$msg = "";
if(isset($_POST['send_message'])) {
    
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

     $sql = "INSERT INTO contacts (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    $conn->query($sql);

    $msg = "<div class='alert-success'>Thank you! Your message has been sent successfully.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Melody Masters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --bg-light: #f8f9fa;
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg-light); margin: 0; color: var(--primary); }

        .contact-wrapper {
            max-width: 1100px;
            margin: 60px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
        }

        /* --- Left Side: Info --- */
        .contact-info {
            background: var(--primary);
            color: white;
            padding: 40px;
            border-radius: 20px;
        }

        .contact-info h2 { font-size: 1.8rem; margin-bottom: 20px; }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .info-item i {
            font-size: 1.2rem;
            background: rgba(255,255,255,0.1);
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--accent);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 40px;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            transition: 0.3s;
        }

        .social-links a:hover { color: var(--accent); }

        /* --- Right Side: Form --- */
        .contact-form-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .form-group { margin-bottom: 20px; }
        
        label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; }

        input, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid #eee;
            border-radius: 10px;
            font-family: inherit;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus, textarea:focus { outline: none; border-color: var(--accent); }

        .btn-send {
            background: var(--accent);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }

        .btn-send:hover { background: #2980b9; transform: translateY(-2px); }

        .alert-success {
            background: #e6f9ed;
            color: #2ecc71;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }

        /* --- Map --- */
        .map-container {
            max-width: 1100px;
            margin: 0 auto 60px;
            padding: 0 20px;
        }

        iframe {
            width: 100%;
            height: 350px;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        @media (max-width: 850px) {
            .contact-wrapper { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div style="text-align: center; padding-top: 60px;">
        <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0;">Get In Touch</h1>
        <p style="color: #888; margin-top: 10px;">We’d love to hear from you. Let’s talk about your music needs.</p>
    </div>

    <div class="contact-wrapper">
        <div class="contact-info">
            <h2>Contact Information</h2>
            <p style="opacity: 0.8; margin-bottom: 40px;">Contact us for any problems regarding musical instruments.</p>

            <div class="info-item">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <strong>Location</strong>
                    <p style="margin: 5px 0 0; font-size: 0.9rem;">123 Melody Lane, Colombo 07, Sri Lanka</p>
                </div>
            </div>

            <div class="info-item">
                <i class="fa-solid fa-phone"></i>
                <div>
                    <strong>Phone</strong>
                    <p style="margin: 5px 0 0; font-size: 0.9rem;">+94 11 234 5678</p>
                </div>
            </div>

            <div class="info-item">
                <i class="fa-solid fa-envelope"></i>
                <div>
                    <strong>Email</strong>
                    <p style="margin: 5px 0 0; font-size: 0.9rem;">support@melodymasters.lk</p>
                </div>
            </div>

            <div class="social-links">
                <a href="#"><i class="fa-brands fa-facebook"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>

        <div class="contact-form-card">
            <?php echo $msg; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="Inquiry about Guitars" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" placeholder="Write your message here..." required></textarea>
                </div>
                <button type="submit" name="send_message" class="btn-send">Send Message</button>
            </form>
        </div>
    </div>

    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.5827906224!2d79.78616403310087!3d6.921833535287611!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae253d10f7a70ad%3A0x3964c841d7e19ed2!2sColombo!5e0!3m2!1sen!2slk!4v1700000000000!5m2!1sen!2slk" allowfullscreen="" loading="lazy"></iframe>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>