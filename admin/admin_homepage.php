<?php
session_start();
include('connect.php');


if (!isset($_SESSION['Email'])) {
    header('Location: index.php');
    exit();
}

$email = $_SESSION['Email'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE Email='$email'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="style/homepage.css">
</head>
<body>


    <div class="navbar">
        <div class="logo">
            <h1>FoodLovers</h1> 
        </div>
        <div class="nav-links">
    <a href="admin_homepage.php">Home</a>
    <a href="users_list.php">Users</a> 
    <a href="posts.php">Posts</a>
</div>

    </div>

    <div class="container">
        <h1>Welcome, <?php echo $user['Fname'] . ' ' . $user['Lname']; ?>!</h1>
        <p>You are now logged in.</p>
        <a href="logout.php">Logout</a> 
    </div>

</body>
</html>
