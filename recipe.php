<?php
class Recipe {
    private $userId;
    private $title;
    private $description;
    private $ingredients;
    private $steps;

    public function __construct($userId, $title, $description, $ingredients, $steps) {
        $this->userId = $userId;
        $this->title = $title;
        $this->description = $description;
        $this->ingredients = $ingredients;
        $this->steps = $steps;
    }

    public function addRecipe($conn) {
        $query = "INSERT INTO recipes (userId, title, description, ingredients, steps) VALUES ('$this->userId', '$this->title', '$this->description', '$this->ingredients', '$this->steps')";
        return mysqli_query($conn, $query);
    }
    
    public function getRecipes($conn) {
        $query = "SELECT * FROM recipes WHERE userId='$this->userId'";
        return mysqli_query($conn, $query);
    }
}
?>
