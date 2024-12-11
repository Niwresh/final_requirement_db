<?php
include 'connect.php';

if (isset($_POST['signUp'])) {
    $Fname = $_POST['fName'];
    $Lname = $_POST['lName'];
    $Email = $_POST['email'];
    $Password = $_POST['password'];
    $Password = md5($Password); 
    $role = isset($_POST['isAdmin']) && $_POST['isAdmin'] == 'yes' ? 'admin' : 'user';

    $checkEmail = "SELECT * FROM users WHERE Email='$Email'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        $insertQuery = "INSERT INTO users (Fname, Lname, Email, Password, role)
                        VALUES ('$Fname', '$Lname', '$Email', '$Password', '$role')";
        if ($conn->query($insertQuery) === TRUE) {
            header("Location: index.php");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if (isset($_POST['signIn'])) {
    $Email = $_POST['email'];
    $Password = $_POST['password'];
    $Password = md5($Password); 

    $sql = "SELECT * FROM users WHERE email='$Email' and password='$Password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['Email'] = $row['Email'];
        $_SESSION['role'] = $row['role']; 

        if ($row['role'] === 'admin') {
            header("Location: admin_homepage.php");
        } else {
            header("Location: homepage.php");
        }
        exit();
    } else {
        echo "Not Found, Incorrect Email or Password";
    }
}
?>
