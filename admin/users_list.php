<?php
session_start();
include('connect.php');


if (!isset($_SESSION['Email'])) {
    header('Location: index.php');
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
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
        <h1>User List</h1>
        
        <table border="1">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Profile Picture</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($user = mysqli_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['Fname']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['Lname']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['Email']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($user['profile_pic']) . "' alt='Profile Pic' width='50' height='50'></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="admin_homepage.php">Back to Homepage</a>
    </div>

</body>
</html>
