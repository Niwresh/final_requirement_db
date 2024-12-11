<?php
include('User.php');
include('Recipe.php');
include('Ingredient.php');
include('Review.php');
include('comment.php')

$user = new User("user@example.com", "John", "Doe", "password123");

$recipe = new Recipe($user->getId(), "Pasta", "Delicious pasta recipe", "Tomatoes, Garlic, Pasta", "1. Boil water, 2. Cook pasta...");

$recipe->addRecipe($conn);

$ingredient = new Ingredient($recipe->getId(), "Tomato", "2 cups");
$ingredient->addIngredient($conn);

$review = new Review($recipe->getId(), $user->getId(), 5, "Excellent recipe!");
$review->addReview($conn);
?>
