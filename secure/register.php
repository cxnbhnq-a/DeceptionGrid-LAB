<?php
session_start();
include 'config.php';

// SECURITY: CSRF protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = function_exists('random_bytes') ? bin2hex(random_bytes(32)) : md5(uniqid(mt_rand(), true));
}

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SECURITY: Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token mismatch');
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // SECURITY: Input validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        // SECURITY: Strong password hashing (Bcrypt)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // SECURITY: PDO Prepared statement
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
        try {
            $stmt->execute([$name, $email, $hashed_password]);
            $success = 'Registration successful! You can now <a href="login.php" style="text-decoration: underline;">Login here</a>.';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Email already exists.';
            } else {
                $error = 'Registration failed. Please try again.'; // SECURITY: Generic error
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register | Secure Lab</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="theme-secure">
    <div class="top-banner"><i class="fa-solid fa-shield-check"></i> SECURE ENVIRONMENT (PATCHED)</div>
    <div class="auth-container">
        <div class="auth-box glass-panel">
            <div class="auth-header">
                <div class="logo" style="justify-content: center;"><i class="fa-solid fa-shield-halved"></i> DeceptionGrid</div>
                <h2 style="color: var(--primary);">Secure Registration</h2>
                <p class="form-label">Create an account safely.</p>
            </div>
            
            <?php if(isset($error)) echo "<div class='alert'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert' style='background: rgba(16, 185, 129, 0.1); color: #10B981; border-color: #10B981;'>" . $success . "</div>"; ?>
            
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required pattern="[a-zA-Z\s]+">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password (Min 8 characters)</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                    <i class="fa-solid fa-eye password-toggle"></i>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Create Secure Account</button>
            </form>
            <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
