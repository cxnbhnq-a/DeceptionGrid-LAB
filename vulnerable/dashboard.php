<?php
// Error Tracker (Biar gampang kalau ada error lain)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. PANGGIL CONFIG PALING ATAS
// Ini otomatis menjalankan session_name('VULN_LAB_SESSION') dan session_start()
require_once 'config.php';

// 2. MENCEGAH CACHE BROWSER (Posisi yang benar: di luar blok if)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 3. BARU CEK LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// VULNERABILITY: SQL Injection 
$query = "SELECT * FROM users WHERE id=$user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | Vulnerable Lab</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="theme-vuln">
    <div class="top-banner"><i class="fa-solid fa-triangle-exclamation"></i> INTENTIONALLY VULNERABLE ENVIRONMENT</div>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="logo"><a href="../index.php"><i class="fa-solid fa-bug"></i></a> Lab Menu</div>
            <ul class="menu-list">
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-terminal"></i> Dashboard</a></li>
                <li><a href="edit_profile.php"><i class="fa-solid fa-user-ninja"></i> Profile</a></li>
                <li><a href="upload.php"><i class="fa-solid fa-file-arrow-up"></i> Upload Data</a></li>
                <?php if (isset($user['role']) && strtolower($user['role']) == 'admin') echo "<li><a href='admin.php'><i class='fa-solid fa-skull'></i> Admin Panel</a></li>"; ?>
                <li><a href="logout.php"><i class="fa-solid fa-power-off"></i> Disconnect</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2 style="font-family: var(--font-mono); margin-top: 40px;">Welcome, <?php echo $user['name']; // VULN: Stored XSS ?>_</h2>

            <div class="grid-cards">
                <div class="stat-card glass-panel">
                    <p class="form-label">System Role</p>
                    <h3><?php echo strtoupper($user['role'] ?? 'UNKNOWN'); ?></h3>
                </div>
                <div class="stat-card glass-panel">
                    <p class="form-label">Registered Email</p>
                    <h3 style="word-break: break-all;"><?php echo $user['email']; ?></h3>
                </div>
            </div>

            <div class="glass-panel" style="padding: 20px;">
                <h3>System Status</h3>
                <p class="form-label" style="margin-top: 10px;">Warning: Firewalls are intentionally disabled for educational purposes. Input validation is currently offline.</p>
            </div>
        </main>
    </div>
</body>
</html>
