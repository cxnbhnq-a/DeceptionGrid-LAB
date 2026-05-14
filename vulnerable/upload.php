<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $file = $_FILES['profile_pic'];
    $file_name = $file['name']; // VULN: No sanitization, allows Path Traversal ../
    $file_tmp = $file['tmp_name'];

    // VULN: Unrestricted File Upload (No MIME check, no extension check)
    // Attackers can upload .php web shells
    if (move_uploaded_file($file_tmp, "uploads/$file_name")) {
        // VULN: SQL Injection in update
        $query = "UPDATE users SET profile_pic='$file_name' WHERE id=$user_id";
        mysqli_query($conn, $query);
        $success = "File uploaded to: uploads/$file_name"; // VULN: Path disclosure
    } else {
        $error = "Failed to upload file.";
    }
}

$query = "SELECT role FROM users WHERE id=$user_id";
$user = mysqli_fetch_assoc(mysqli_query($conn, $query));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Upload | Vulnerable Lab</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="theme-vuln">
    <div class="top-banner"><i class="fa-solid fa-triangle-exclamation"></i> INTENTIONALLY VULNERABLE ENVIRONMENT</div>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="logo"><i class="fa-solid fa-bug"></i> Lab Menu</div>
            <ul class="menu-list">
                <li><a href="dashboard.php"><i class="fa-solid fa-terminal"></i> Dashboard</a></li>
                <li><a href="edit_profile.php"><i class="fa-solid fa-user-ninja"></i> Profile</a></li>
                <li><a href="upload.php" class="active"><i class="fa-solid fa-file-arrow-up"></i> Upload Data</a></li>
                <?php if ($user['role'] == 'admin') echo "<li><a href='admin.php'><i class='fa-solid fa-skull'></i> Admin Panel</a></li>"; ?>
                <li><a href="logout.php"><i class="fa-solid fa-power-off"></i> Disconnect</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2 style="font-family: var(--font-mono); margin-top: 40px; margin-bottom: 20px;">...</h2>
   <h2 style="font-family: var(--font-mono); margin-top: 40px; margin-bottom: 20px;">Upload Profile Image</h2>
            
            <div class="glass-panel" style="max-width: 600px; padding: 30px;">
                <p class="form-label" style="margin-bottom: 20px;">Warning: Upload filters are currently disabled.</p>
                
                <?php if(isset($success)) echo "<div class='alert' style='background: rgba(16, 185, 129, 0.1); color: #10B981; border-color: #10B981;'>$success</div>"; ?>
                <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">Select File to Upload</label>
                        <input type="file" name="profile_pic" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i> Upload File</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
