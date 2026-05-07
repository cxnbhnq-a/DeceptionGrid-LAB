<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';
$user_id = $_SESSION['user_id'];

// VULNERABILITY: SQL Injection if user_id is manipulated, but since it's from session, less likely but still
$query = "SELECT * FROM users WHERE id=$user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vulnerable</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <h1>Dashboard</h1>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <div class="card">
            <h2>Welcome, <?php echo $user['name']; // VULNERABILITY: XSS - no htmlspecialchars ?></h2>
            <p>Email: <?php echo $user['email']; ?></p>
            <p>Role: <?php echo $user['role']; ?></p>
            <?php if ($user['profile_pic']) echo "<img src='uploads/" . $user['profile_pic'] . "' class='profile-pic'>"; // VULNERABILITY: Path traversal if manipulated ?>
            <br><br>
            <a href="upload.php" class="btn">Upload Profile Pic</a>
            <a href="edit_profile.php" class="btn">Edit Profile</a>
            <?php if ($user['role'] == 'admin') echo "<a href='admin.php' class='btn'>Admin Panel</a>"; ?>
        </div>
    </div>
</body>
</html>