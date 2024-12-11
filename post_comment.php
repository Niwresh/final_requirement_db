<?php
include('connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['Email'])) {
        echo "Please log in to comment.";
        exit();
    }

    $email = $_SESSION['Email'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE Email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();

    if (!$userData) {
        echo "User not found!";
        exit();
    }

    $userId = $userData['id']; 
    $postId = $_POST['recipe_id']; 
    $commentText = $_POST['comment']; 

    $stmt = $conn->prepare("INSERT INTO comments (postId, userId, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postId, $userId, $commentText); 
    if ($stmt->execute()) {
        header('Location: homepage.php'); 
        exit();
    } else {
        echo "Error posting comment.";
    }
} else {
    echo "Invalid request.";
}
?>
