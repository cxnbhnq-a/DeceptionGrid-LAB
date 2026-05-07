<?php
// Error tracker
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // VULNERABILITY: No sanitization, allows XSS payloads
    $name = $_POST['name']; 
    $email = $_POST['email'];
    // VULNERABILITY: Weak hashing
    $password = md5($_POST['password']); 

    // VULNERABILITY: SQL Injection 
    $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'student')";

    if (mysqli_query($conn, $query)) {
        // VULNERABILITY: Reflected XSS (Name is echoed directly without escaping)
        $success = "Registration successful for user: $name! You can now <a href='login.php' style='text-decoration: underline;'>Login here</a>.";
    } else {
        // VULNERABILITY: Information Disclosure
        $error = "Database Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register | Vulnerable Lab</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="theme-vuln">
    <div class="top-banner"><i class="fa-solid fa-triangle-exclamation"></i> INTENTIONALLY VULNERABLE ENVIRONMENT</div>
    <div class="auth-container">
        <div class="auth-box glass-panel">
            <div class="auth-header">
                <div class="logo" style="justify-content: center;"><i class="fa-solid fa-bug"></i> DeceptionGrid</div>
                <h2 style="color: var(--primary);">System Registration</h2>
                <p class="form-label">Warning: Input filters are disabled.</p>
            </div>
            
            <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert' style='background: rgba(16, 185, 129, 0.1); color: #10B981; border-color: #10B981;'>$success</div>"; ?>
            
            <form method="post">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="text" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                    <i class="fa-solid fa-eye password-toggle"></i>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Register Account</button>
            </form>
            <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
