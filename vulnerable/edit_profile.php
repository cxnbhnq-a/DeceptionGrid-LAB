<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name']; // VULNERABILITY: No sanitization
    $email = $_POST['email'];

    // VULNERABILITY: SQL Injection
    $query = "UPDATE users SET name='$name', email='$email' WHERE id=$user_id";
    mysqli_query($conn, $query);

    $_SESSION['name'] = $name; // VULNERABILITY: XSS stored in session
    header("Location: dashboard.php");
    exit();
}

$query = "SELECT name, email FROM users WHERE id=$user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Vulnerable</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <h1>Edit Profile</h1>
        <a href="dashboard.php">Dashboard</a>
    </div>
    <div class="container">
        <div class="card">
            <h2>Edit Profile</h2>
            <form method="post">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $user['name']; // VULNERABILITY: XSS if name contains script ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <button type="submit" class="btn">Update</button>
            </form>
        </div>
    </div>
</body>
</html>