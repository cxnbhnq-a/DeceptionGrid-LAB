<?php
include 'config.php';
session_start();

// SECURITY: CSRF protection using token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SECURITY: Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
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
        // SECURITY: Strong password hashing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // SECURITY: Prepared statement prevents SQL injection
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $hashed_password]);
            $success = 'Registration successful. <a href="login.php">Login</a>';
            // SECURITY: Regenerate session ID after successful action
            session_regenerate_id(true);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Email already exists';
            } else {
                // SECURITY: Generic error message, no details exposed
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Secure</title>
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
            <?php if (isset($error)) echo "<div class='alert alert-error'>" . htmlspecialchars($error) . "</div>"; ?>
            <?php if (isset($success)) echo "<div class='alert alert-success'>" . $success . "</div>"; ?>
            <form method="post" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
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