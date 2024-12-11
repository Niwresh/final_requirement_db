<?php
session_start();
include('connect.php');

if (!isset($_SESSION['Email']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'delete_post' && isset($_GET['id'])) {
        $post_id = $_GET['id'];

        $delete_likes_query = "DELETE FROM post_likes WHERE postId = '$post_id'";
        if (!mysqli_query($conn, $delete_likes_query)) {
            echo "Error deleting likes: " . mysqli_error($conn);
            exit();
        }

        $delete_comments_query = "DELETE FROM comments WHERE postId = '$post_id'";
        if (!mysqli_query($conn, $delete_comments_query)) {
            echo "Error deleting comments: " . mysqli_error($conn);
            exit();
        }

        $delete_post_query = "DELETE FROM recipes WHERE id = '$post_id'";
        if (mysqli_query($conn, $delete_post_query)) {
            header('Location: posts.php');
            exit();
        } else {
            echo "Error deleting post: " . mysqli_error($conn);
        }
    }

    if ($action == 'mute_post' && isset($_GET['id'])) {
        $post_id = $_GET['id'];
        $query = "UPDATE recipes SET is_muted = 1 WHERE id = '$post_id'";
        if (mysqli_query($conn, $query)) {
            header('Location: posts.php');
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    if ($action == 'edit_post' && isset($_GET['id'])) {
        $post_id = $_GET['id'];
        $query = "SELECT * FROM recipes WHERE id = '$post_id'";
        $result = mysqli_query($conn, $query);
        $post = mysqli_fetch_assoc($result);

        if (isset($_POST['update'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];

            $query = "UPDATE recipes SET title = '$title', description = '$description' WHERE id = '$post_id'";
            if (mysqli_query($conn, $query)) {
                header('Location: posts.php');
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts</title>
</head>
<body>

<?php if (isset($post)): ?>
    <h2>Edit Post</h2>
    <form method="post">
        <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        <textarea name="description" required><?php echo htmlspecialchars($post['description']); ?></textarea>
        <button type="submit" name="update">Update Post</button>
    </form>
<?php endif; ?>

</body>
</html>
