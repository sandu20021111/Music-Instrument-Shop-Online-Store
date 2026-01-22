<?php
include 'db_connect.php';
session_start();

if (isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    $sql = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES ('$product_id', '$user_id', '$rating', '$comment')";

    if ($conn->query($sql)) {
        header("Location: product_details.php?id=$product_id&msg=Review added!");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>