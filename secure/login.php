<?php
include 'config.php';
session_start();

// SECURITY: Session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
// SECURITY: CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SECURITY: Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token mismatch');
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // SECURITY: Input validation
    if (empty($email) || empty($password)) {
        $error = 'All fields are required';
    } else {
        // SECURITY: Prepared statement prevents SQL injection
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // SECURITY: Regenerate session ID to prevent fixation
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = htmlspecialchars($user['name']); // SECURITY: Sanitize stored data
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Invalid credentials';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | Secure Lab</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="theme-secure">
    <div class="top-banner"><i class="fa-solid fa-shield-check"></i> SECURE ENVIRONMENT (PATCHED)</div>
    <div class="auth-container">
        <div class="auth-box glass-panel">
            <div class="auth-header">
                <div class="logo" style="justify-content: center;"><i class="fa-solid fa-shield-halved"></i> DeceptionGrid</div>
                <h2 style="color: var(--primary);">Secure Login</h2>
            </div>
            <?php if(isset($error)) echo "<div class='alert'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</div>"; ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                    <i class="fa-solid fa-eye password-toggle"></i>
                </div>
                <button type="submit" class="btn btn-primary">Login to Hub</button>
            </form>
            <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">No account? <a href="register.php">Register here</a></p>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
