<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// SECURITY: Session timeout check
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

include 'config.php';
$user_id = $_SESSION['user_id'];

// SECURITY: Prepared statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Secure</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <h1>Dashboard</h1>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <div class="card">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); // SECURITY: XSS prevention ?></h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Role: <?php echo htmlspecialchars($user['role']); ?></p>
            <?php if ($user['profile_pic']) echo "<img src='uploads/" . htmlspecialchars($user['profile_pic']) . "' class='profile-pic'>"; // SECURITY: Sanitize filename ?>
            <br><br>
            <a href="upload.php" class="btn">Upload Profile Pic</a>
            <a href="edit_profile.php" class="btn">Edit Profile</a>
            <?php if ($user['role'] == 'admin') echo "<a href='admin.php' class='btn'>Admin Panel</a>"; ?>
        </div>
    </div>
</body>
</html>