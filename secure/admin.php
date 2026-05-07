<?php
// Error Tracker
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// SECURE: Validasi tipe data dan Prepared Statement untuk DELETE
if (isset($_GET['delete'])) {
    $id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: admin.php");
    exit();
}

// Ambil semua user
$stmt = $pdo->query("SELECT id, name, email, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data user aktif untuk menu sidebar
$user_id = $_SESSION['user_id'];
$u_stmt = $pdo->prepare("SELECT role FROM users WHERE id=?");
$u_stmt->execute([$user_id]);
$user = $u_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel | Secure Lab</title>
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
                <li><a href="upload.php"><i class="fa-solid fa-file-shield"></i> Secure Upload</a></li>
                <li><a href="admin.php" class="active"><i class="fa-solid fa-lock"></i> Admin Panel</a></li>
                <li><a href="logout.php"><i class="fa-solid fa-power-off"></i> Disconnect</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2 style="font-family: var(--font-mono); margin-top: 40px; margin-bottom: 20px;">Admin SecOps Panel</h2>
            
            <div class="glass-panel" style="padding: 30px;">
                <div class="table-wrapper">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $row) { ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; background: rgba(14, 165, 233, 0.2); color: #7DD3FC; font-weight: 600;"><?php echo htmlspecialchars(strtoupper($row['role']), ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 6px 12px; width: auto;" onclick="return confirm('Hapus data secara aman (Secure)?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
