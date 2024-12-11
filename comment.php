<?php
class Comment {
    private $conn;
    private $postId;
    private $userId;
    private $comment;

    
    public function __construct($conn, $postId, $userId, $comment) {
        $this->conn = $conn;
        $this->postId = $postId;
        $this->userId = $userId;
        $this->comment = $comment;
    }

    public function getComments() {
        $sql = "
            SELECT comments.comment, comments.created_at, users.Fname, users.Lname, users.profile_pic
            FROM comments
            JOIN users ON comments.userId = users.id
            WHERE comments.postId = ?
            ORDER BY comments.created_at DESC
        ";
    
       
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->postId);  
        $stmt->execute();
        $result = $stmt->get_result();
    
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    
        return $comments;
    }
    
}
?>
