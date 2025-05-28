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

    // Fetch email from failed_logins using ID
    $getEmail = mysqli_query($conn, "SELECT email FROM failed_logins WHERE id = $id");
    if ($getEmail && mysqli_num_rows($getEmail) > 0) {
        $emailRow = mysqli_fetch_assoc($getEmail);
        $email = $emailRow['email'];

        // Delete from failed_logins
        $deleteQuery = "DELETE FROM failed_logins WHERE id = $id";
        mysqli_query($conn, $deleteQuery);

        // Update unblock_requests status to 'Approved' for this user if pending
        $updateRequest = mysqli_query($conn, "
            UPDATE unblock_requests 
            SET status = 'Approved' 
            WHERE user_id = (SELECT id FROM users WHERE Email = '$email') 
            AND status = 'Pending'
        ");
    }

    // Redirect back to blocked_users.php after unblocking
    header('Location: blocked_users.php');
    exit();
}

// Handle CSV Download
if (isset($_GET['action']) && $_GET['action'] === 'download_csv') {
    $result = mysqli_query($conn, "SELECT * FROM failed_logins ORDER BY attempt_time DESC");

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="blocked_users_report.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['ID', 'IP Address', 'Email', 'Blocked Time']);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id'],
            $row['ip_address'],
            $row['email'],
            $row['attempt_time']
        ]);
    }

    fclose($output);
    exit();
}

// Fetch blocked users for display
$query = mysqli_query($conn, "SELECT * FROM failed_logins ORDER BY attempt_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Blocked Users</title>
    <link rel="stylesheet" href="../style/homepage.css" />
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
            <a href="posts.php">Posts</a>
            <a href="blocked_users.php">Blocked</a>
        </div>
    </div>

    <!-- Blocked Users Table -->
    <div class="container">
        <h1>Blocked Users</h1>

        <!-- Download CSV Button -->
        <p>
            <a href="blocked_users.php?action=download_csv" style="padding: 8px 15px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">
                Download CSV Report
            </a>
        </p>

        <table border="1" cellpadding="8" cellspacing="0" width="100%">
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
