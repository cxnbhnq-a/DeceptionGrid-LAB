<?php
// Radar Error
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. PANGGIL CONFIG PALING ATAS (Berisi session_name & session_start)
require_once 'config.php';

// 2. PROTEKSI (Sesi sudah dimulai otomatis oleh config.php)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Logika Delete (Vulnerable SQLi)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM users WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: admin.php");
    exit();
}

$query = "SELECT id, name, email, role FROM users";
$result = mysqli_query($conn, $query);

// Data user untuk sidebar
$u_id = $_SESSION['user_id'];
$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT role FROM users WHERE id=$u_id"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel | Vulnerable Lab</title>
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
                <li><a href="upload.php"><i class="fa-solid fa-file-arrow-up"></i> Upload Data</a></li>
                <li><a href="admin.php" class="active"><i class="fa-solid fa-skull"></i> Admin Panel</a></li>
                <li><a href="logout.php"><i class="fa-solid fa-power-off"></i> Disconnect</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2 style="font-family: var(--font-mono); margin-top: 40px; margin-bottom: 20px;">Admin SecOps Panel</h2>
            <div class="glass-panel" style="padding: 30px;">
                <div class="table-wrapper">
                    <table class="cyber-table">
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><span style="color: #FCA5A5;"><?php echo strtoupper($row['role']); ?></span></td>
                                <td><a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" style="width:auto;"><i class="fa-solid fa-trash"></i></a></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
