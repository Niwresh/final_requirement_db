<?php
session_start();
include('connect.php');

// Redirect if not logged in or not admin
if (!isset($_SESSION['Email']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Fetch user info
$email = $_SESSION['Email'];
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE Email='$email'");
$user = mysqli_fetch_assoc($userQuery);

// Handle unblock request
if (isset($_GET['action']) && $_GET['action'] === 'unblock' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM failed_logins WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: blocked_users.php");
    exit();
}

// Handle export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="blocked_users.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'IP Address', 'Email', 'Blocked Time']);
    $exportQuery = $conn->query("SELECT id, ip_address, email, attempt_time FROM failed_logins ORDER BY attempt_time DESC");
    while ($row = $exportQuery->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['ip_address'], $row['email'], $row['attempt_time']]);
    }
    fclose($output);
    exit();
}

// Fetch blocked users
$query = $conn->query("SELECT * FROM failed_logins ORDER BY attempt_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blocked Users</title>
    <link rel="stylesheet" href="style/homepage.css">
    <style>
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        table th {
            background-color: #f4f4f4;
        }

        table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 10px 5px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #1abc9c;
        }

        .navbar {
            background-color: #2c3e50;
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            margin: 0;
        }

        .nav-links a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
        }

        .nav-links a:hover {
            text-decoration: underline;
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
        <a href="blocked_users.php">Blocked</a>
    </div>
</div>

<div class="container">
    <h1>Blocked Users</h1>
    <a class="btn" href="blocked_users.php?export=csv">Export to CSV</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>IP Address</th>
                <th>Email</th>
                <th>Blocked Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($query->num_rows > 0): ?>
                <?php while ($row = $query->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['ip_address']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['attempt_time']) ?></td>
                        <td>
                            <a class="btn" href="blocked_users.php?action=unblock&id=<?= $row['id'] ?>" onclick="return confirm('Unblock this user?');">Unblock</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No blocked users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a class="btn" href="admin_homepage.php">Back to Homepage</a>
</div>

</body>
</html>
