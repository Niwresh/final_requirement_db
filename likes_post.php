<?php
session_start();
include('connect.php'); 

if (!isset($_SESSION['Email'])) {
    echo "You must be logged in to like posts.";
    exit();
}

$stmt = $conn->prepare("SELECT id FROM users WHERE Email = ?");
$stmt->bind_param("s", $_SESSION['Email']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$userId = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = intval($_POST['post_id']);

    $checkLikeStmt = $conn->prepare("SELECT * FROM post_likes WHERE userId = ? AND postId = ?");
    $checkLikeStmt->bind_param("ii", $userId, $postId);
    $checkLikeStmt->execute();
    $result = $checkLikeStmt->get_result();

    if ($result->num_rows > 0) {
        $unlikeStmt = $conn->prepare("DELETE FROM post_likes WHERE userId = ? AND postId = ?");
        $unlikeStmt->bind_param("ii", $userId, $postId);
        $unlikeStmt->execute();
    } else {
        $likeStmt = $conn->prepare("INSERT INTO post_likes (userId, postId) VALUES (?, ?)");
        $likeStmt->bind_param("ii", $userId, $postId);
        $likeStmt->execute();
    }
    header("Location: homepage.php");
    exit();
} else {
    echo "Invalid request.";
    exit();
}
?>
