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

<!-- Registration Form -->
<div class="container" id="signup" style="display:none;">
    <h1 class="form-title">Register</h1>
    <form id="signupForm" onsubmit="return handleSubmit();">
        <label for="Fname">First Name:</label>
        <input type="text" name="Fname" id="Fname" required><br>
        <label for="Lname">Last Name:</label>
        <input type="text" name="Lname" id="Lname" required><br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
        <p id="recaptchaError" style="color: red; display: none;">Please verify that you are not a robot.</p><br>
        <input type="submit" class="btn" name="signUp" value="Sign Up">
    </form>
    <p class="or">----------</p>
    <div class="links">
        <p>Already Have an Account?</p>
        <button id="signInButton">Sign In</button>
    </div>
</div>

<!-- Login Form -->
<div class="container" id="signIn">
    <h1 class="form-title">Sign In</h1>
    <form id="login-form">
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <input type="submit" class="btn" value="Sign In" name="signIn">
        <div id="block-message" style="display: none; color: red; margin-top: 10px;"></div>
        <button id="request-access" style="display: none; margin-top: 5px;">Request Access</button>
    </form>
    <p class="or">----------</p>
    <div class="links">
        <p>Don't have an account yet?</p>
        <button id="signUpButton">Sign Up</button>
    </div>
</div>

<!-- Puzzle Modal -->
<div id="puzzleModal" class="puzzle-modal">
    <div class="puzzle-content">
        <h2>Complete the Puzzle</h2>
        <div id="puzzleContainer" class="puzzle-container"></div>
        <div class="slider-container">
            <input type="range" min="0" max="100" value="0" id="slider" class="slider">
        </div>
        <button onclick="submitPuzzle()" id="submitPuzzleBtn">Submit Puzzle</button>
    </div>
</div>

<!-- OTP Modal -->
<div id="otpModal" class="puzzle-modal">
    <div class="puzzle-content">
        <h2>Enter OTP</h2>
        <form id="otpForm">
            <input type="text" name="otp" id="otpInput" placeholder="Enter OTP" required><br>
            <button type="submit">Verify OTP</button>
        </form>
    </div>
</div>

<script src="script.js"></script>

<script>
const puzzleContainer = document.getElementById("puzzleContainer");
const slider = document.getElementById("slider");
let puzzlePieces = [
    { id: 1, img: "images/puzzle_part_1.jpg" },
    { id: 2, img: "images/puzzle_part_2.jpg" },
    { id: 3, img: "images/puzzle_part_3.jpg" },
    { id: 4, img: "images/puzzle_part_4.jpg" },
    { id: 5, img: "images/puzzle_part_5.jpg" },
    { id: 6, img: "images/puzzle_part_6.jpg" },
    { id: 7, img: "images/puzzle_part_7.jpg" },
    { id: 8, img: "images/puzzle_part_8.jpg" },
    { id: 9, img: "images/puzzle_part_9.jpg" }
];
const puzzleOffset = -100;
const maxSliderValue = 100;

function renderPuzzle(sliderValue = 0) {
    puzzleContainer.innerHTML = "";
    puzzlePieces.forEach((piece, index) => {
        const tile = document.createElement("div");
        tile.classList.add("puzzle-tile");
        tile.style.backgroundImage = `url(${piece.img})`;
        tile.style.backgroundSize = "cover";
        if (index === 8) {
            const moveX = puzzleOffset + (sliderValue * (Math.abs(puzzleOffset) / maxSliderValue));
            tile.style.transform = `translateX(${moveX}px)`;
        }
        puzzleContainer.appendChild(tile);
    });
}
slider.addEventListener('input', () => {
    renderPuzzle(slider.value);
});

function submitPuzzle() {
    const sliderValue = parseInt(slider.value);
    if (sliderValue >= 98 && sliderValue <= 100) {
        const form = document.getElementById("signupForm");
        const formData = new FormData(form);

        fetch("submit_user.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                window.location.href = "index.php";
            }
        })
        .catch(error => {
            alert("Something went wrong. Try again.");
            console.error(error);
        });

        document.getElementById('puzzleModal').style.display = 'none';
    } else {
        alert("Please slide the puzzle piece into the correct position.");
    }
}
function showPuzzleCaptcha() {
    document.getElementById('puzzleModal').style.display = 'flex';
    slider.value = 0;
    renderPuzzle();
}
function handleSubmit() {
    var response = grecaptcha.getResponse();
    if (response.length === 0) {
        document.getElementById('recaptchaError').style.display = 'block';
        return false;
    }
    showPuzzleCaptcha();
    return false;
}
</script>

<script>
// LOGIN WITH OTP + BLOCK CHECK
const loginForm = document.getElementById('login-form');

loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(loginForm);

    fetch('send_otp.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.blocked) {
            document.getElementById("block-message").textContent = data.message;
            document.getElementById("block-message").style.display = "block";
            document.getElementById("request-access").style.display = "inline-block";
            sessionStorage.setItem("blockedEmail", formData.get("email"));
        } else if (data.success) {
            document.getElementById('otpModal').style.display = 'flex';
            localStorage.setItem('loginEmail', formData.get('email'));
        } else {
            alert(data.message);
        }
    });
});

// REQUEST ACCESS BUTTON
document.getElementById("request-access").addEventListener("click", function () {
    const email = sessionStorage.getItem("blockedEmail");

    fetch("request_unblock.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `email=${encodeURIComponent(email)}`
    })
    .then(res => res.text())
    .then(msg => {
        alert(msg);
    });
});

// OTP SUBMIT
document.getElementById("otpForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const otp = document.getElementById("otpInput").value;
    const email = localStorage.getItem("loginEmail");

    fetch('verify_otp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ otp, email })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            window.location.href = "homepage.php";
        }
    });
});
</script>

</body>
</html>
