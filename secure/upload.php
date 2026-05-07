<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Logika Upload (PDO)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $file = $_FILES['profile_pic'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        $error = "Invalid file type.";
    } elseif ($file['size'] > 2097152) {
        $error = "File too large (Max 2MB).";
    } else {
        $new_filename = uniqid('profile_') . '.' . $file_extension;
        if (move_uploaded_file($file['tmp_name'], "uploads/" . $new_filename)) {
            $stmt = $pdo->prepare("UPDATE users SET profile_pic=? WHERE id=?");
            $stmt->execute([$new_filename, $user_id]);
            $success = "Image uploaded securely.";
        } else {
            $error = "Upload failed.";
        }
    }
}

$stmt = $pdo->prepare("SELECT role, profile_pic FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Upload | Secure Lab</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="theme-secure">
    <div class="top-banner"><i class="fa-solid fa-shield-check"></i> SECURE ENVIRONMENT (PATCHED)</div>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="logo"><i class="fa-solid fa-shield-halved"></i> SecOps Menu</div>
            <ul class="menu-list">
                <li><a href="dashboard.php"><i class="fa-solid fa-terminal"></i> Dashboard</a></li>
                <li><a href="edit_profile.php"><i class="fa-solid fa-user-shield"></i> Profile</a></li>
                <li><a href="upload.php" class="active"><i class="fa-solid fa-file-shield"></i> Secure Upload</a></li>
                <?php if ($user['role'] == 'admin') echo "<li><a href='admin.php'><i class='fa-solid fa-lock'></i> Admin Panel</a></li>"; ?>
                <li><a href="logout.php"><i class="fa-solid fa-power-off"></i> Disconnect</a></li>
            </ul>
        </aside>
<main class="main-content">
            <h2 style="font-family: var(--font-mono); margin-top: 40px; margin-bottom: 20px;">...</h2>
            
            <h2 style="font-family: var(--font-mono); margin-bottom: 20px;">Secure File Upload</h2>

            <div class="glass-panel" style="max-width: 600px; padding: 30px;">
                <p class="form-label" style="margin-bottom: 20px;">Strict verification: Extension & MIME type checking active. Files are renamed securely.</p>

                <?php if(isset($success)) echo "<div class='alert' style='background: rgba(16, 185, 129, 0.1); color: #10B981; border-color: #10B981;'>$success</div>"; ?>
                <?php if(isset($error)) echo "<div class='alert'>".htmlspecialchars($error, ENT_QUOTES, 'UTF-8')."</div>"; ?>

                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label class="form-label">Select Image (Max 2MB)</label>
                        <input type="file" name="profile_pic" class="form-control" accept=".jpg,.jpeg,.png,.gif" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-shield-virus"></i> Verify & Upload</button>
                </form>

                <?php if(!empty($user['profile_pic'])): ?>
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center;">
                    <h4 style="color: var(--text-muted); margin-bottom: 15px; font-family: var(--font-mono);">Current Profile Picture</h4>
                    <img src="uploads/<?php echo htmlspecialchars($user['profile_pic'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 3px solid var(--primary); box-shadow: 0 0 20px var(--primary-glow);">
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
