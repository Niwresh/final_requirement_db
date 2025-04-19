<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

$enteredOtp = $data['otp'];
$sessionOtp = $_SESSION['otp'] ?? '';
$sessionEmail = $_SESSION['email'] ?? '';

if ($enteredOtp == $sessionOtp) {
    $_SESSION['Email'] = $sessionEmail;
    echo json_encode(['success' => true, 'message' => 'OTP Verified. Logged in!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP. Try again.']);
}
?>
