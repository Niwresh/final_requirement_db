<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Fname = trim($_POST['Fname']);
    $Lname = trim($_POST['Lname']);
    $Email = trim($_POST['email']);
    $Password = $_POST['password'];
    $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE Email=?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email already exists."]);
        exit();
    }

    // Insert user
    $insertQuery = "INSERT INTO users (Fname, Lname, Email, Password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $Fname, $Lname, $Email, $hashedPassword);

    if ($stmt->execute()) {
        // ✅ Start session after registration and set variables
        $newUserId = $stmt->insert_id;

        $_SESSION['user_id'] = $newUserId;
        $_SESSION['user_email'] = $Email;
        $_SESSION['user_name'] = $Fname . " " . $Lname;

        echo json_encode(["success" => true, "message" => "Registration successful!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }
}
?>
