<?php
class User {
    private $email;
    private $firstName;
    private $lastName;
    private $password;

    public function __construct($email, $firstName, $lastName, $password) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password;
    }

    public function register($conn) {
        $passwordHash = md5($this->password);
        $query = "INSERT INTO users (email, firstName, lastName, password) VALUES ('$this->email', '$this->firstName', '$this->lastName', '$passwordHash')";
        return mysqli_query($conn, $query);
    }

    public function login($conn) {
        $passwordHash = md5($this->password);
        $query = "SELECT * FROM users WHERE email='$this->email' AND password='$passwordHash'";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_assoc($result);
    }
    
}
?>
