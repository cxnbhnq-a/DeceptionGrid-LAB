<?php
include 'config.php';
session_start();

// VULNERABILITY: No CSRF protection
// VULNERABILITY: No input validation/sanitization

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name']; // VULNERABILITY: No sanitization - XSS possible
    $email = $_POST['email'];
    $password = md5($_POST['password']); // VULNERABILITY: Weak password hashing (MD5 is broken)

    // VULNERABILITY: SQL Injection - direct string concatenation
    // Example attack: name='test', email='test@test.com' OR '1'='1 -- ', password=anything
    $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

    if (mysqli_query($conn, $query)) {
        // VULNERABILITY: XSS - echoing user input without escaping
        echo "<div class='alert alert-success'>Registration successful for $name. <a href='login.php'>Login</a></div>";
    } else {
        // VULNERABILITY: Information disclosure - exposing database errors
        echo "<div class='alert alert-error'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Vulnerable</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js"></script>
</head>
<body>
    <div class="navbar">
        <h1>Student Registration</h1>
        <a href="login.php">Login</a>
    </div>
    <div class="container">
        <div class="card">
            <h2>Register</h2>
            <form method="post" id="registerForm">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
        </div>
    </div>
</body>
</html>