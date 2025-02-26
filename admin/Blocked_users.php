<?php
session_start();
include('connect.php'); // Ensure the correct database connection

// Redirect if not logged in
if (!isset($_SESSION['Email'])) {
    header('Location: index.php');
    exit();
}

// Handle Unblock Request
if (isset($_GET['action']) && $_GET['action'] === 'unblock' && isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input

    // Delete the user from the failed_logins table
    $deleteQuery = "DELETE FROM failed_logins WHERE id = $id";
    mysqli_query($conn, $deleteQuery);

    // Redirect back to blocked_users.php after unblocking
    header('Location: blocked_users.php');
    exit();
}

// Fetch blocked users
$query = mysqli_query($conn, "SELECT * FROM failed_logins ORDER BY attempt_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blocked Users</title>
    <link rel="stylesheet" href="../style/homepage.css"> 
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <h1>Admin Panel</h1>
        </div>
        <div class="nav-links">
            <a href="admin_homepage.php">Home</a>
            <a href="users_list.php">Users</a>
            <a href="blocked_users.php">Blocked Users</a>
        </div>
    </div>

    <!-- Blocked Users Table -->
    <div class="container">
        <h1>Blocked Users</h1>

        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>IP Address</th>
                    <th>Email</th>
                    <th>Blocked Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ip_address']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['attempt_time']) . "</td>";
                    echo "<td>
                            <a href='blocked_users.php?action=unblock&id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to unblock this user?\");'>Unblock</a>
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
