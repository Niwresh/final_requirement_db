<?php
class Review {
    private $conn; 
    private $recipeId;
    private $userId;
    private $rating;
    private $comment;

    public function __construct($conn, $recipeId, $userId, $rating, $comment) {
        $this->conn = $conn; 
        $this->recipeId = $recipeId;
        $this->userId = $userId;
        $this->rating = $rating;
        $this->comment = $comment;
    }

    public function addReview() {
        $query = "INSERT INTO reviews (recipeId, userId, rating, comment) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiis", $this->recipeId, $this->userId, $this->rating, $this->comment);
        return $stmt->execute();
    }

    public function getReviews() {
        $query = "SELECT * FROM reviews WHERE recipeId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->recipeId);
        $stmt->execute();
        $result = $stmt->get_result();

        $reviews = [];
        while ($review = $result->fetch_assoc()) {
            $reviews[] = $review; 
        }
        return $reviews;
    }
}
?>
