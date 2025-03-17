<?php
include 'connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ REGISTER USER
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signUp'])) {
    $Fname = trim($_POST['Fname']);
    $Lname = trim($_POST['Lname']);
    $Email = trim($_POST['email']);
    $Password = $_POST['password'];

    $hashedPassword = password_hash($Password, PASSWORD_DEFAULT); 

    // Validate reCAPTCHA
    if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
        echo "<script>alert('Please complete the reCAPTCHA verification.'); window.location.href='index.php';</script>";
        exit();
    }

    $recaptchaSecretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $recaptchaVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecretKey&response=$recaptchaResponse";

    $verifyResponse = file_get_contents($recaptchaVerifyUrl);
    $responseData = json_decode($verifyResponse, true);

    if (!$responseData['success']) {
        echo "<script>alert('reCAPTCHA verification failed. Please try again.'); window.location.href='index.php';</script>";
        exit();
    }

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE Email=?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Email Address Already Exists! Try logging in.'); window.location.href='index.php';</script>";
        exit();
    } 

    // Insert user into database
    $insertQuery = "INSERT INTO users (Fname, Lname, Email, Password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $Fname, $Lname, $Email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now log in.'); window.location.href='index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}

// ✅ LOGIN USER
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signIn'])) {
    $Email = trim($_POST['email']);
    $Password = $_POST['password'];

    $checkUser = "SELECT * FROM users WHERE Email=?";
    $stmt = $conn->prepare($checkUser);
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($Password, $user['Password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_name'] = $user['Fname'] . " " . $user['Lname'];

            // Redirect to homepage.php
            header("Location: homepage.php");
            exit();
        } else {
            echo "<script>alert('Invalid Password. Please try again.'); window.location.href='index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No account found with this email.'); window.location.href='index.php';</script>";
        exit();
    }
}
?>
