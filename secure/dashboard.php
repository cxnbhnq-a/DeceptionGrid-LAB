<?php
// MENGAKTIFKAN PENDETEKSI ERROR 
// (Catatan SecOps: Di dunia nyata/production, ubah angka 1 menjadi 0 agar error tidak bocor ke publik)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// PANGGIL CONFIG PALING AWAL AGAR SESSION TERBACA
require_once 'config.php';

// MENCEGAH CACHE BROWSER
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// SECURITY: Session timeout check (30 menit / 1800 detik)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: login.php?msg=timeout"); // Tambahkan parameter msg agar bisa ditangkap di login.php
    exit();
}
$_SESSION['last_activity'] = time();

if (!isset($pdo)) {
    die("Koneksi database (\$pdo) tidak ditemukan di config.php!");
}

$user_id = $_SESSION['user_id'];

// SECURITY: PDO Prepared statement
$stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$search = $_GET['search'] ?? '';

if (!$user) {
    session_unset();
    session_destroy();
    die("User tidak ditemukan di database.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | Secure Lab</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="theme-secure">
    <div class="top-banner"><i class="fa-solid fa-shield-check"></i> SECURE ENVIRONMENT (PATCHED)</div>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="logo"><a href="../index.php"><i class="fa-solid fa-shield-halved"></i></a> SecOps Menu</div>
            <ul class="menu-list">
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-terminal"></i> Dashboard</a></li>
                <li><a href="edit_profile.php"><i class="fa-solid fa-user-shield"></i> Profile</a></li>
                <li><a href="upload.php"><i class="fa-solid fa-file-shield"></i> Secure Upload</a></li>
                
                <?php if (strtolower($user['role']) === 'admin') echo "<li><a href='admin.php'><i class='fa-solid fa-lock'></i> Admin Panel</a></li>"; ?>
                
                <li><a href="logout.php"><i class="fa-solid fa-power-off"></i> Disconnect</a></li>
            </ul>
        </aside>
        <main class="main-content">
<div
    class="glass-panel"
    style="
        padding:20px;
        margin-top:40px;
        margin-bottom:20px;
    "
>

    <h3 style="margin-bottom:15px;">
        Quick Search
    </h3>

    <form method="GET">

        <div
            style="
                display:flex;
                gap:10px;
                align-items:center;
            "
        >

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search page..."
                value="<?php
echo htmlspecialchars(
    $search,
    ENT_QUOTES,
    'UTF-8'
); ?>"
            >

            <button
                type="submit"
                class="btn btn-primary"
                style="width:auto;"
            >
                Search
            </button>

        </div>

    </form>

    <?php if (!empty($search)): ?>

    <div
        style="
            margin-top:20px;
            padding:15px;
            border:1px solid rgba(255,0,0,.2);
            border-radius:10px;
        "
    >

        Search result for:
<?php
echo htmlspecialchars(
    $search,
    ENT_QUOTES,
    'UTF-8'
);
?>
    </div>

    <?php endif; ?>

</div>
            <h2 style="font-family: var(--font-mono); margin-top: 40px;">Welcome, <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>_</h2>

            <div class="grid-cards">
                <div class="stat-card glass-panel">
                    <p class="form-label">System Role</p>
                    <h3><?php echo htmlspecialchars(strtoupper($user['role']), ENT_QUOTES, 'UTF-8'); ?></h3>
                </div>
                <div class="stat-card glass-panel">
                    <p class="form-label">Registered Email</p>
                    <h3 style="word-break: break-all;"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></h3>
                </div>
            </div>

            <div class="glass-panel" style="padding: 20px;">
                <h3>System Status</h3>
                <p class="form-label" style="margin-top: 10px;">Security measures are active. Input validation, Output Encoding, and Prepared Statements are strictly enforced.</p>
            </div>
        </main>
    </div>
</body>
</html>
