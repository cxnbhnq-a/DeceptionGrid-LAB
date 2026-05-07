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

    // SECURITY: Define allowed file types
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_mime = $file['type'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // SECURITY: Validate file extension
    if (!in_array($file_ext, $allowed_extensions)) {
        $error = 'Invalid file type. Only JPG, PNG, GIF allowed.';
    }
    // SECURITY: Validate MIME type
    elseif (!in_array($file_mime, $allowed_mimes)) {
        $error = 'Invalid file type detected.';
    }
    // SECURITY: Check file size
    elseif ($file_size > $max_size) {
        $error = 'File size too large. Max 2MB.';
    }
    // SECURITY: Check if file is actually uploaded
    elseif (!is_uploaded_file($file_tmp)) {
        $error = 'Invalid upload.';
    } else {
        // SECURITY: Generate random filename to prevent conflicts and path traversal
        $new_name = bin2hex(random_bytes(16)) . '.' . $file_ext;

        if (move_uploaded_file($file_tmp, "uploads/$new_name")) {
            // SECURITY: Prepared statement
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->execute([$new_name, $user_id]);
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Upload failed.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Picture - Secure</title>
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
            <?php if (isset($error)) echo "<div class='alert alert-error'>" . htmlspecialchars($error) . "</div>"; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_pic">Select Image (JPG, PNG, GIF, max 2MB):</label>
                    <input type="file" id="profile_pic" name="profile_pic" accept="image/*" required>
                </div>
                <button type="submit" class="btn">Upload</button>
            </form>
        </div>
    </div>
</body>
</html>