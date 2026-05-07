<?php
include 'config.php';
session_start(); // VULNERABILITY: Session fixation - session ID not regenerated

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // VULNERABILITY: Weak hashing

    // VULNERABILITY: SQL Injection - direct concatenation
    // Example attack: email='admin@example.com' -- ', password=anything
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name']; // VULNERABILITY: XSS if name contains script
        $_SESSION['role'] = $user['role'];
        // VULNERABILITY: No session regeneration
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vulnerable</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js"></script>
</head>
<body>
    <div class="navbar">
        <h1>Student Registration</h1>
        <a href="register.php">Register</a>
    </div>
    <div class="container">
        <div class="card">
            <h2>Login</h2>
            <?php if (isset($error)) echo "<div class='alert alert-error'>" . $error . "</div>"; // VULNERABILITY: XSS if error contains script ?>
            <form method="post" id="loginForm">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>