<?php
// Radar Error
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. PANGGIL CONFIG PALING ATAS (Berisi session_name & session_start)
require_once 'config.php';

// 2. PROTEKSI ANTI-CACHE
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 3. CEK LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Logika Update Profile (PDO)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $name = strip_tags(trim($_POST['name'])); 
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    $stmt = $pdo->prepare("UPDATE users SET name=?, email=? WHERE id=?");
    if($stmt->execute([$name, $email, $user_id])) {
        $_SESSION['name'] = $name; 
        $success = "Profile updated securely!";
    } else {
        $error = "An error occurred.";
    }
}

// Ambil Data User untuk Form
$stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile | Secure Lab</title>
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
                <li><a href="edit_profile.php" class="active"><i class="fa-solid fa-user-shield"></i> Profile</a></li>
                <li><a href="upload.php"><i class="fa-solid fa-file-shield"></i> Secure Upload</a></li>
                <?php if ($user['role'] == 'admin') echo "<li><a href='admin.php'><i class='fa-solid fa-lock'></i> Admin Panel</a></li>"; ?>
                <li><a href="logout.php"><i class="fa-solid fa-power-off"></i> Disconnect</a></li>
            </ul>
        </aside>
<main class="main-content">
            <h2 style="font-family: var(--font-mono); margin-top: 40px; margin-bottom: 20px;">...</h2>            
            <h2 style="font-family: var(--font-mono); margin-bottom: 20px;">Edit System Profile</h2>
            
            <div class="glass-panel" style="max-width: 600px; padding: 30px;">
                <?php if(isset($success)) echo "<div class='alert' style='background: rgba(16, 185, 129, 0.1); color: #10B981; border-color: #10B981;'>$success</div>"; ?>
                <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
                
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label class="form-label">Display Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Securely</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
