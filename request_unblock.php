<?php
session_start();
$conn = new mysqli("localhost", "root", "", "final_db");

$email = $_POST['email'] ?? '';

if (!$email) {
    echo "Invalid request.";
    exit;
}

// Check if user exists
$query = $conn->prepare("SELECT id FROM users WHERE Email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "No user found with that email.";
    exit;
}

$user = $result->fetch_assoc();
$user_id = $user['id'];

// Insert request
$stmt = $conn->prepare("INSERT INTO unblock_requests (user_id, request_date, status) VALUES (?, NOW(), 'pending')");
$stmt->bind_param("i", $user_id);
$stmt->execute();

echo "Your request to unblock has been submitted. Please wait for admin approval.";
?>
