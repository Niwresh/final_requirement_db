<?php
session_start();
include('connect.php');

if (!isset($_SESSION['Email'])) {
    echo "Session not found. Please log in.";
    exit();
}

$email = $_SESSION['Email'];

$stmt = $conn->prepare("SELECT * FROM users WHERE Email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

if (!$userData) {
    echo "User not found!";
    exit();
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES['profile_pic']['name']);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if (getimagesize($_FILES['profile_pic']['tmp_name']) && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
            $updateStmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE Email=?");
            $updateStmt->bind_param("ss", $targetFile, $email);
            $updateStmt->execute();
            echo "Profile picture updated successfully!";
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file type.";
    }
}

// Handle new recipe addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['description'], $_POST['ingredients'], $_POST['steps'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $steps = $_POST['steps'];
    $imagePath = null;

    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/recipes/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $targetFile = $targetDir . basename($_FILES['recipe_image']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (getimagesize($_FILES['recipe_image']['tmp_name']) && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES['recipe_image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                echo "Error uploading recipe image.";
            }
        } else {
            echo "Invalid recipe image file type.";
        }
    }

    $insertRecipeStmt = $conn->prepare("INSERT INTO recipes (userId, title, description, ingredients, steps, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $insertRecipeStmt->bind_param("isssss", $userData['id'], $title, $description, $ingredients, $steps, $imagePath);
    if ($insertRecipeStmt->execute()) {
        echo "Recipe added successfully!";
    } else {
        echo "Error adding recipe.";
    }
}

// Handle recipe editing
if (isset($_GET['edit_id'])) {
    $editId = $_GET['edit_id'];

    $recipeStmt = $conn->prepare("SELECT * FROM recipes WHERE id=? AND userId=?");
    $recipeStmt->bind_param("ii", $editId, $userData['id']);
    $recipeStmt->execute();
    $recipeData = $recipeStmt->get_result()->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_title'], $_POST['edit_description'], $_POST['edit_ingredients'], $_POST['edit_steps'])) {
        $title = $_POST['edit_title'];
        $description = $_POST['edit_description'];
        $ingredients = $_POST['edit_ingredients'];
        $steps = $_POST['edit_steps'];

        $updateRecipeStmt = $conn->prepare("UPDATE recipes SET title=?, description=?, ingredients=?, steps=? WHERE id=? AND userId=?");
        $updateRecipeStmt->bind_param("ssssii", $title, $description, $ingredients, $steps, $editId, $userData['id']);
        if ($updateRecipeStmt->execute()) {
            echo "Recipe updated successfully!";
            header("Location: profile.php");
            exit();
        } else {
            echo "Error updating recipe.";
        }
    }
}

// Handle recipe deletion
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    $deleteCommentsStmt = $conn->prepare("DELETE FROM comments WHERE postId=?");
    $deleteCommentsStmt->bind_param("i", $deleteId);
    if ($deleteCommentsStmt->execute()) {
        $deleteLikesStmt = $conn->prepare("DELETE FROM post_likes WHERE postId=?");
        $deleteLikesStmt->bind_param("i", $deleteId);
        if ($deleteLikesStmt->execute()) {
            $deleteStmt = $conn->prepare("DELETE FROM recipes WHERE id=? AND userId=?");
            $deleteStmt->bind_param("ii", $deleteId, $userData['id']);
            if ($deleteStmt->execute()) {
                echo "Recipe and its associated data deleted successfully!";
                header("Location: profile.php");
                exit();
            } else {
                echo "Error deleting recipe.";
            }
        } else {
            echo "Error deleting associated likes.";
        }
    } else {
        echo "Error deleting associated comments.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style/profile.css"> 
</head>
<body>
    <div class="container">
        <h1>Your Profile</h1>
        <img src="<?php echo $userData['profile_pic'] ?? 'uploads/default.jpg'; ?>" alt="Profile Picture" width="150">

        <form method="POST" enctype="multipart/form-data">
            <label for="profile_pic">Change Profile Picture:</label>
            <input type="file" name="profile_pic" id="profile_pic" required>
            <button type="submit">Upload</button>
        </form>

        <?php if (isset($_GET['edit_id']) && $recipeData): ?>
            <h2>Edit Recipe</h2>
            <form method="POST">
                <label for="edit_title">Recipe Title:</label>
                <input type="text" name="edit_title" id="edit_title" value="<?php echo htmlspecialchars($recipeData['title']); ?>" required>

                <label for="edit_description">Description:</label>
                <textarea name="edit_description" id="edit_description" required><?php echo htmlspecialchars($recipeData['description']); ?></textarea>

                <label for="edit_ingredients">Ingredients:</label>
                <textarea name="edit_ingredients" id="edit_ingredients" required><?php echo htmlspecialchars($recipeData['ingredients']); ?></textarea>

                <label for="edit_steps">Steps:</label>
                <textarea name="edit_steps" id="edit_steps" required><?php echo htmlspecialchars($recipeData['steps']); ?></textarea>

                <button type="submit">Update Recipe</button>
            </form>
        <?php else: ?>
            <h2>Add a New Recipe</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="title">Recipe Title:</label>
                <input type="text" name="title" id="title" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" required></textarea>

                <label for="ingredients">Ingredients:</label>
                <textarea name="ingredients" id="ingredients" required></textarea>

                <label for="steps">Steps:</label>
                <textarea name="steps" id="steps" required></textarea>

                <label for="recipe_image">Add an Image:</label>
                <input type="file" name="recipe_image" id="recipe_image" accept="image/*">

                <button type="submit">Add Recipe</button>
            </form>
        <?php endif; ?>

        <h2>Your Recipes</h2>
        <?php
        $recipesStmt = $conn->prepare("SELECT * FROM recipes WHERE userId=?");
        $recipesStmt->bind_param("i", $userData['id']);
        $recipesStmt->execute();
        $recipesResult = $recipesStmt->get_result();

        if ($recipesResult->num_rows > 0) {
            while ($recipe = $recipesResult->fetch_assoc()) {
                echo "<div class='recipe-post'>";
                echo "<h3>" . htmlspecialchars($recipe['title']) . "</h3>";
                if (!empty($recipe['image_path'])) {
                    echo "<img src='" . htmlspecialchars($recipe['image_path']) . "' alt='Recipe Image' style='width:300px;'>";
                }
                echo "<p>" . htmlspecialchars($recipe['description']) . "</p>";
                echo "<p><strong>Ingredients:</strong> " . htmlspecialchars($recipe['ingredients']) . "</p>";
                echo "<p><strong>Steps:</strong> " . htmlspecialchars($recipe['steps']) . "</p>";
                echo "<p><a href='profile.php?edit_id=" . $recipe['id'] . "'>Edit</a> | <a href='profile.php?delete_id=" . $recipe['id'] . "'>Delete</a></p>";
                echo "</div>";
            }
        } else {
            echo "<p>You have not added any recipes yet.</p>";
        }
        ?>
        <a href="homepage.php">Back to Homepage</a>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
