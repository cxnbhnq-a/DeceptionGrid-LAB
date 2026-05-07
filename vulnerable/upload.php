<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_pic'];
    $file_name = $file['name']; // VULNERABILITY: No sanitization
    $file_tmp = $file['tmp_name'];

    // VULNERABILITY: No file extension validation - allows .php, .exe, etc.
    // VULNERABILITY: No MIME type checking
    // VULNERABILITY: No file size limit
    // VULNERABILITY: File can overwrite existing files
    // VULNERABILITY: Path traversal if name contains ../

    move_uploaded_file($file_tmp, "uploads/$file_name");

    // VULNERABILITY: SQL Injection
    $query = "UPDATE users SET profile_pic='$file_name' WHERE id=$user_id";
    mysqli_query($conn, $query);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Picture - Vulnerable</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <h1>Upload Profile Picture</h1>
        <a href="dashboard.php">Dashboard</a>
    </div>
    <div class="container">
        <div class="card">
            <h2>Upload Profile Picture</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_pic">Select Image:</label>
                    <input type="file" id="profile_pic" name="profile_pic" required>
                </div>
                <button type="submit" class="btn">Upload</button>
            </form>
        </div>
    </div>
</body>
</html>