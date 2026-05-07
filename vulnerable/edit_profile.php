<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'config.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name']; // VULN: No sanitization
    $email = $_POST['email'];

    // VULN: SQL Injection in UPDATE
    $query = "UPDATE users SET name='$name', email='$email' WHERE id=$user_id";
    if(mysqli_query($conn, $query)) {
        $_SESSION['name'] = $name; // VULN: Update session with XSS payload
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile: " . mysqli_error($conn); // VULN: Info Disclosure
    }
}

$query = "SELECT name, email, role FROM users WHERE id=$user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile | Vulnerable Lab</title>
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
                <li><a href="edit_profile.php" class="active"><i class="fa-solid fa-user-ninja"></i> Profile</a></li>
                <li><a href="upload.php"><i class="fa-solid fa-file-arrow-up"></i> Upload Data</a></li>
                <?php if ($user['role'] == 'admin') echo "<li><a href='admin.php'><i class='fa-solid fa-skull'></i> Admin Panel</a></li>"; ?>
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
                    <div class="form-group">
                        <label class="form-label">Display Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo $user['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
