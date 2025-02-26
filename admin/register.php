<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['unauthorizedAccess'])) {
    $_SESSION['unauthorizedAccess'] = false;
}

if (isset($_POST['signUp'])) {
    $Fname = $_POST['fName'];
    $Lname = $_POST['lName'];
    $Email = $_POST['email'];
    $Password = md5($_POST['password']); 
    $role = isset($_POST['isAdmin']) && $_POST['isAdmin'] == 'yes' ? 'admin' : 'user';

    $checkEmail = "SELECT * FROM users WHERE Email='$Email'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "<script>alert('Email Address Already Exists!'); window.location.href='index.php';</script>";
        exit();
    } else {
        $insertQuery = "INSERT INTO users (Fname, Lname, Email, Password, role)
                        VALUES ('$Fname', '$Lname', '$Email', '$Password', '$role')";
        if ($conn->query($insertQuery) === TRUE) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
            exit();
        }
    }
}

if (isset($_POST['signIn'])) {
    $Email = $_POST['email'];
    $Password = md5($_POST['password']);
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $sql = "SELECT * FROM users WHERE Email='$Email' AND Password='$Password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['Email'] = $row['Email'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: admin_homepage.php");
            exit();
        }
    }

    // If user is not admin OR email does not exist, log it and show modal
    $failedEmail = $conn->real_escape_string($Email);
    $insertFailedAttempt = "INSERT INTO failed_logins (ip_address, email) VALUES ('$ip_address', '$failedEmail')";
    
    if (!$conn->query($insertFailedAttempt)) {
        die("Error logging unauthorized access: " . $conn->error);
    }

    // Set session flag for modal display
    $_SESSION['unauthorizedAccess'] = true;
    header("Location: index.php");
    exit();
}
?>
