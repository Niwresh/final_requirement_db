<?php
session_start();
include('connect.php');

if (!isset($_SESSION['Email'])) {
    header('Location: index.php');
    exit();
}

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM users WHERE id = $delete_id";
    mysqli_query($conn, $delete_sql);
    header("Location: users_list.php?message=User+deleted+successfully");
    exit();
}

// Initialize variables for editing
$edit_user = null;
$edit_mode = false;

// Handle Edit Form Submission
if (isset($_POST['update_user'])) {
    $id = intval($_POST['id']);
    $Fname = mysqli_real_escape_string($conn, $_POST['Fname']);
    $Lname = mysqli_real_escape_string($conn, $_POST['Lname']);
    $Email = mysqli_real_escape_string($conn, $_POST['Email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $update_sql = "UPDATE users SET Fname='$Fname', Lname='$Lname', Email='$Email', role='$role' WHERE id=$id";
    if (mysqli_query($conn, $update_sql)) {
        header("Location: users_list.php?message=User+updated+successfully");
        exit();
    } else {
        echo "Error updating user: " . mysqli_error($conn);
    }
}

// Handle Edit Request (show edit form)
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $res = mysqli_query($conn, "SELECT * FROM users WHERE id = $edit_id");
    if ($res) {
        $edit_user = mysqli_fetch_assoc($res);
        if ($edit_user) {
            $edit_mode = true;
        }
    }
}

// Fetch all users
$query = mysqli_query($conn, "SELECT * FROM users");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>User List</title>
<link rel="stylesheet" href="style/homepage.css" />
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
    }
    img {
        border-radius: 50%;
    }
    form.edit-form {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #ccc;
        background: #f9f9f9;
    }
    form.edit-form label {
        display: block;
        margin-top: 10px;
    }
    form.edit-form input[type="text"],
    form.edit-form input[type="email"],
    form.edit-form select {
        width: 100%;
        padding: 6px;
        margin-top: 4px;
        box-sizing: border-box;
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
    <h1>User List</h1>

    <!-- Show messages -->
    <?php if (isset($_GET['message'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_GET['message']) ?></p>
    <?php endif; ?>

    <!-- Edit form if in edit mode -->
    <?php if ($edit_mode && $edit_user): ?>
        <form method="post" class="edit-form">
            <h2>Edit User: <?= htmlspecialchars($edit_user['Fname'] . ' ' . $edit_user['Lname']) ?></h2>
            <input type="hidden" name="id" value="<?= $edit_user['id'] ?>" />
            
            <label>First Name:</label>
            <input type="text" name="Fname" value="<?= htmlspecialchars($edit_user['Fname']) ?>" required />
            
            <label>Last Name:</label>
            <input type="text" name="Lname" value="<?= htmlspecialchars($edit_user['Lname']) ?>" required />
            
            <label>Email:</label>
            <input type="email" name="Email" value="<?= htmlspecialchars($edit_user['Email']) ?>" required />
            
            <label>Role:</label>
            <select name="role" required>
                <option value="user" <?= $edit_user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $edit_user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            
            <br><br>
            <button type="submit" name="update_user">Update User</button>
            <a href="users_list.php" style="margin-left: 10px;">Cancel</a>
        </form>
    <?php endif; ?>

    <!-- Users Table -->
    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Profile Picture</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($user = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= htmlspecialchars($user['Fname']) ?></td>
                <td><?= htmlspecialchars($user['Lname']) ?></td>
                <td><?= htmlspecialchars($user['Email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Pic" width="50" height="50"></td>
                <td>
                    <a href="users_list.php?edit_id=<?= $user['id'] ?>">Edit</a> |
                    <a href="users_list.php?delete_id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <a href="admin_homepage.php">Back to Homepage</a>
</div>
</body>
</html>
