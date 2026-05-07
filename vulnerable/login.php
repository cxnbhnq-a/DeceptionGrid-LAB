<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // VULN: Weak Hash

    // VULN: SQL Injection (Login Bypass: ' OR '1'='1)
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name']; // VULN: Stored Session XSS
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php"); exit();
    } else {
        $error = "Login failed. Invalid syntax in query: " . $query; // VULN: Information Disclosure
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | Vulnerable Lab</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="theme-vuln">
    <div class="top-banner"><i class="fa-solid fa-triangle-exclamation"></i> INTENTIONALLY VULNERABLE ENVIRONMENT</div>
    <div class="auth-container">
        <div class="auth-box glass-panel">
            <div class="auth-header">
                <div class="logo" style="justify-content: center;"><i class="fa-solid fa-bug"></i> DeceptionGrid</div>
                <h2 style="color: var(--primary);">System Login</h2>
                <p class="form-label">Enter your credentials to access the lab.</p>
            </div>
            <?php if(isset($error)) echo "<div class='alert'>$error</div>"; // VULN: Reflected XSS ?>
            <form method="post">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="text" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                    <i class="fa-solid fa-eye password-toggle"></i>
                </div>
                <button type="submit" class="btn btn-primary">Login to Lab</button>
            </form>
            <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">No account? <a href="register.php">Register here</a></p>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
