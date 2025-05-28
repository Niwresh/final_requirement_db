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
    <title>Admin Homepage</title>
    <link rel="stylesheet" href="style/homepage.css">
    <style>
        .request-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .request-table th, .request-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .request-table th {
            background-color: #f2f2f2;
        }
    </style>
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
        <a href="Blocked_users.php">Blocked</a>
    </div>
</div>

<div class="container">
    <h1>Welcome, <?php echo $user['Fname'] . ' ' . $user['Lname']; ?>!</h1>
    <p>You are now logged in as Admin.</p>
    <a href="logout.php">Logout</a> 

    <!-- Unblock Request Table -->
    <h2>Unblock Requests</h2>
    <?php
    $conn = new mysqli("localhost", "root", "", "final_db");

    $result = $conn->query("SELECT ur.id, u.Email, ur.request_date, ur.status 
                            FROM unblock_requests ur 
                            JOIN users u ON ur.user_id = u.id 
                            ORDER BY ur.request_date DESC");

    if ($result->num_rows > 0) {
        echo "<table class='request-table'>";
        echo "<tr><th>Email</th><th>Date Requested</th><th>Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Email']}</td>
                    <td>{$row['request_date']}</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No unblock requests at the moment.</p>";
    }

    $conn->close();
    ?>
</div>

</body>
</html>
