<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        /* Simple CSS for sliding puzzle modal */
        .puzzle-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }
        .puzzle-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 350px;
        }
        .puzzle-container {
            display: grid;
            grid-template-columns: repeat(3, 100px);
            grid-template-rows: repeat(3, 100px);
            gap: 5px;
            margin-bottom: 20px;
        }
        .slider-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .slider {
            width: 300px;
            margin-top: 10px;
        }
        .puzzle-tile {
            width: 100px;
            height: 100px;
            background-color: #ccc;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #ddd;
            transition: transform 0.3s ease;
        }
        .puzzle-content button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container" id="signup" style="display:none;">
        <h1 class="form-title">Register</h1>
        <form method="POST" action="register.php" id="signupForm" onsubmit="return handleSubmit();">
            <label for="Fname">First Name:</label>
            <input type="text" name="Fname" id="Fname" required>
            <br>

            <label for="Lname">Last Name:</label>
            <input type="text" name="Lname" id="Lname" required>
            <br>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            <br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <br>

            <!-- Google reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
            <p id="recaptchaError" style="color: red; display: none;">Please verify that you are not a robot.</p>
            <br>

            <input type="submit" class="btn" name="signUp" value="Sign Up">
        </form>

        <p class="or">----------</p>
        <div class="links">
            <p>Already Have an Account?</p>
            <button id="signInButton">Sign In</button>
        </div>
    </div>

    <div class="container" id="signIn">
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="register.php">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
        <p class="or">----------</p>
        <div class="links">
            <p>Don't have an account yet?</p>
            <button id="signUpButton">Sign Up</button>
        </div>
    </div>

    <!-- Sliding Puzzle Modal -->
    <div id="puzzleModal" class="puzzle-modal">
        <div class="puzzle-content">
            <h2>Complete the Puzzle</h2>
            <div id="puzzleContainer" class="puzzle-container">
                <!-- Puzzle tiles will be inserted here -->
            </div>
            <div class="slider-container">
                <input type="range" min="0" max="100" value="0" id="slider" class="slider">
            </div>
            <button onclick="submitPuzzle()" id="submitPuzzleBtn">Submit Puzzle</button>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
    const puzzleContainer = document.getElementById("puzzleContainer");
    const slider = document.getElementById("slider");

    // Initialize puzzle pieces with one tile that will be moved
    let puzzlePieces = [
        { id: 1, img: "images/puzzle_part_1.jpg", position: 0, isMovable: false },
        { id: 2, img: "images/puzzle_part_2.jpg", position: 1, isMovable: false },
        { id: 3, img: "images/puzzle_part_3.jpg", position: 2, isMovable: false },
        { id: 4, img: "images/puzzle_part_4.jpg", position: 3, isMovable: false },
        { id: 5, img: "images/puzzle_part_5.jpg", position: 4, isMovable: false },
        { id: 6, img: "images/puzzle_part_6.jpg", position: 5, isMovable: false },
        { id: 7, img: "images/puzzle_part_7.jpg", position: 6, isMovable: false },
        { id: 8, img: "images/puzzle_part_8.jpg", position: 7, isMovable: false },
        { id: 9, img: "images/puzzle_part_9.jpg", position: 8, isMovable: true } // Only this tile can be moved
    ];

    // Function to render the puzzle tiles
    function renderPuzzle() {
        puzzleContainer.innerHTML = ""; // Clear existing puzzle

        puzzlePieces.forEach(piece => {
            const tile = document.createElement("div");
            tile.classList.add("puzzle-tile");
            tile.style.backgroundImage = `url(${piece.img})`;
            tile.style.backgroundSize = "cover";
            tile.dataset.id = piece.id;
            tile.dataset.position = piece.position;
            tile.style.transform = `translateX(${piece.isMovable ? 100 : 0}px)`; // Tile moves based on slider
            puzzleContainer.appendChild(tile);
        });
    }

    // Handle the slider to move the movable tile
    slider.addEventListener('input', function() {
        const moveAmount = slider.value;
        const movableTile = puzzlePieces.find(piece => piece.isMovable);

        if (movableTile) {
            movableTile.position = moveAmount;
            renderPuzzle();
        }
    });

    // Submit the puzzle after completion
    function submitPuzzle() {
        alert("Please complete the puzzle by sliding the piece into place.");
    }

    // Handle form submission
    function handleSubmit() {
        var response = grecaptcha.getResponse();
        if (response.length === 0) {
            document.getElementById('recaptchaError').style.display = 'block';
            return false;
        }

        // Trigger puzzle after reCAPTCHA is verified
        showPuzzleCaptcha();
        return false;
    }

    function showPuzzleCaptcha() {
        document.getElementById('puzzleModal').style.display = 'flex';
        renderPuzzle(); // Render the puzzle when modal shows up
    }

    </script>

</body>
</html>
