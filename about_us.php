<?php
session_start();
include('connect.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - PinoyFoodTrip</title>
    <link rel="stylesheet" href="style/about_us.css">
</head>
<body>

<div class="navbar">
        <div class="logo">
            <h1>FoodLovers</h1>
        </div>
        <div class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="about_us.php">About Us</a>
            <a href="contact.php">Contact Us</a>
            <a href="profile.php">Profile</a> 
        </div>
    </div>

<div class="header">
    <h1>PinoyFoodTrip</h1>
    <p>Celebrating the heart of Filipino cuisine</p>
</div>

<div class="content">
    <!-- <h2>About Us</h2> -->
    <p class="p1">
        PinoyFoodTrip Recipe celebrates the heart of Filipino cuisine, bringing you traditional dishes that are simple to prepare and a joy to share. With a perfect blend of flavors and textures, Filipino food stands out for its comforting and delicious taste.
    </p>
    <p class="p2">
        The love and warmth we share through our cooking make every meal special, creating memorable moments with family and friends. Join us in discovering the rich and diverse flavors of the Philippines, where every dish tells a story of culture, family, and tradition.
    </p>
</div>

<div class="image">
    <img src="images/book1.png" alt="" srcset="">
</div>

<!-- <div class="footer">
    <p>&copy; 2024 PinoyFoodTrip. All rights reserved.</p>
</div> -->

</body>
</html>
