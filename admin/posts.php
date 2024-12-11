<?php
session_start();
include('connect.php');


if (!isset($_SESSION['Email'])) {
    header('Location: index.php');
    exit();
}


$query = mysqli_query($conn, "SELECT r.*, u.Fname, u.Lname FROM recipes r JOIN users u ON r.userId = u.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts List</title>
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
        <h1>Posts/Recipes List</h1>

        <table border="1">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($post = mysqli_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($post['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($post['Fname']) . " " . htmlspecialchars($post['Lname']) . "</td>";
                    echo "<td>" . htmlspecialchars($post['description']) . "</td>";
                    echo "<td>
                            <a href='manage_posts.php?action=edit_post&id=" . $post['id'] . "'>Edit</a>
                            <a href='manage_posts.php?action=delete_post&id=" . $post['id'] . "'>Delete</a>
                            <a href='manage_posts.php?action=mute_post&id=" . $post['id'] . "'>Mute</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="admin_homepage.php">Back to Homepage</a>
    </div>

</body>
</html>
