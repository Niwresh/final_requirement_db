<?php
class Ingredient {
    private $recipeId;

    public function __construct($recipeId) {
        $this->recipeId = $recipeId;
    }

    public function getIngredients($conn) {
        $query = "SELECT ingredients FROM recipes WHERE id='$this->recipeId'";
        return mysqli_query($conn, $query);
    }
}
?>
