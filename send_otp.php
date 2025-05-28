<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli("localhost", "root", "", "final_db"); // update credentials if needed

// Get email and password from login form
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Sanitize input
$email = trim($email);

// Check if user is blocked (email exists in failed_logins table)
$blockCheck = $conn->prepare("SELECT * FROM failed_logins WHERE email = ?");
$blockCheck->bind_param("s", $email);
$blockCheck->execute();
$blockResult = $blockCheck->get_result();

if ($blockResult->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'blocked' => true,
        'message' => 'Your account is blocked. You may request access below.'
    ]);
    exit;
}


// Check if user with that email exists
$query = $conn->prepare("SELECT * FROM users WHERE Email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No account found with this email.']);
    exit;
}

$user = $result->fetch_assoc();

// Check password
if (!password_verify($password, $user['Password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit;
}

// Generate OTP and save to session
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['email'] = $email;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sherwinlumakang827@gmail.com'; // use your Gmail
    $mail->Password   = 'smwc owkl xpto aaic';          // Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    $mail->setFrom('sherwinlumakang827@gmail.com', 'Niwresh_');
    $mail->addAddress($email, $user['Fname'] . ' ' . $user['Lname']);

    $mail->isHTML(true);
    $mail->Subject = 'Your One-Time Password (OTP)';
    $mail->Body    = "Hello <b>{$user['Fname']}</b>,<br><br>Your OTP code is: <h2>$otp</h2><br>Please enter this to continue login.";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'OTP has been sent to your email.']);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => "Failed to send OTP. Mailer Error: {$mail->ErrorInfo}"
    ]);
}
?>
