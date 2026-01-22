<?php
include 'db_connect.php';

// දැනටමත් Session එකක් නැත්නම් පමණක් ආරම්භ කරන්න
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // දත්ත පරීක්ෂාව
    $sql = "SELECT dd.*, p.download_file 
            FROM digital_downloads dd
            JOIN products p ON dd.product_id = p.product_id
            WHERE dd.user_id = '$user_id' AND dd.product_id = '$product_id' 
            LIMIT 1";
    
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $current_time = date('Y-m-d H:i:s');

        // Expiry Check
        if ($current_time > $data['expiry_date']) {
            die("Error: This download link has expired.");
        }

        // Limit Check
        if ($data['download_count'] >= $data['max_limit']) {
            die("Error: You have reached the maximum download limit.");
        }

        $fileName = $data['download_file'];
        $filepath = "uploads/digital_files/" . $fileName;

        if (!empty($fileName) && file_exists($filepath)) {
            // Update Count
            $conn->query("UPDATE digital_downloads SET download_count = download_count + 1 WHERE download_id = " . $data['download_id']);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            readfile($filepath);
            exit;
        } else {
            die("Error: File not found in folder. Check: uploads/digital_files/" . $fileName);
        }
    } else {
        // --- Debugging Information (ප්‍රශ්නය සෙවීමට පමණයි) ---
        echo "<h3>Access Denied!</h3>";
        echo "Your User ID: " . $user_id . "<br>";
        echo "Product ID requested: " . $product_id . "<br>";
        echo "Possible reasons: <br>";
        echo "1. The order status is not 'Delivered' yet.<br>";
        echo "2. The record is missing in 'digital_downloads' table.<br>";
        exit();
    }
} else {
    header("Location: shop.php");
    exit();
}