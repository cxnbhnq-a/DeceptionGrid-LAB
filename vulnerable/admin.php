<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // VULNERABILITY: SQL Injection in DELETE
    // Example attack: ?delete=1 OR 1=1 --
    $query = "DELETE FROM users WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: admin.php");
    exit();
}

$query = "SELECT id, name, email, role FROM users";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Vulnerable</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <h1>Admin Panel</h1>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <div class="card">
            <h2>All Users</h2>
            <table class="table">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php while ($user = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['name']; // VULNERABILITY: XSS if name contains script ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td><a href="?delete=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</a></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>