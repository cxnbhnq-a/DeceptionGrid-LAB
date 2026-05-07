<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <h1>Student Registration System</h1>
    </div>
    <div class="container">
        <div class="card">
            <h2>Welcome</h2>
            <p>Please <a href="login.php">login</a> or <a href="register.php">register</a>.</p>
        </div>
    </div>
</body>
</html>