<?php

declare(strict_types=1);

require_once 'config.php';

/*
|--------------------------------------------------------------------------
| AUTHORIZATION
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {

    header("Location: login.php");

    exit();
}

$user_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| VERIFY ADMIN STILL EXISTS
|--------------------------------------------------------------------------
*/

$check_admin = $pdo->prepare(
    "SELECT id, role
     FROM users
     WHERE id = ?
     LIMIT 1"
);

$check_admin->execute([$user_id]);

$current_admin = $check_admin->fetch();

if (
    !$current_admin ||
    $current_admin['role'] !== 'admin'
) {

    session_destroy();

    header("Location: login.php");

    exit();
}

/*
|--------------------------------------------------------------------------
| DELETE USER (POST ONLY + CSRF)
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | CSRF CHECK
    |--------------------------------------------------------------------------
    */

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ) {

        http_response_code(403);

        die("Invalid CSRF token.");
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE USER ID
    |--------------------------------------------------------------------------
    */

    $delete_id = filter_input(
        INPUT_POST,
        'delete_id',
        FILTER_VALIDATE_INT
    );

    if ($delete_id === false || $delete_id <= 0) {

        $error = "Invalid user ID.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | PREVENT SELF DELETE
        |--------------------------------------------------------------------------
        */

        if ($delete_id === $user_id) {

            $error =
                "You cannot delete your own account.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | CHECK TARGET EXISTS
            |--------------------------------------------------------------------------
            */

            $check_user = $pdo->prepare(
                "SELECT id
                 FROM users
                 WHERE id = ?
                 LIMIT 1"
            );

            $check_user->execute([$delete_id]);

            if (!$check_user->fetch()) {

                $error = "User not found.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | DELETE USER
                |--------------------------------------------------------------------------
                */

                $delete_stmt = $pdo->prepare(
                    "DELETE FROM users
                     WHERE id = ?"
                );

                $delete_stmt->execute([$delete_id]);

                /*
                |--------------------------------------------------------------------------
                | SECURITY LOG
                |--------------------------------------------------------------------------
                */

                error_log(
                    "[ADMIN DELETE] Admin ID " .
                    $user_id .
                    " deleted user ID " .
                    $delete_id .
                    " from IP " .
                    $_SERVER['REMOTE_ADDR']
                );

                $success = "User deleted securely.";
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| FETCH USERS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query(
    "SELECT id, name, email, role
     FROM users
     ORDER BY id ASC"
);

$users = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Admin Panel</title>

    <link rel="stylesheet" href="css/style.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

</head>

<body class="theme-secure">

<div class="top-banner">
    SECURE ENVIRONMENT (PATCHED)
</div>

<div class="app-layout">

    <aside class="sidebar">

        <div class="logo">
            SecOps Menu
        </div>

        <ul class="menu-list">

            <li>
                <a href="dashboard.php">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="edit_profile.php">
                    Profile
                </a>
            </li>

            <li>
                <a href="upload.php">
                    Secure Upload
                </a>
            </li>

            <li>
                <a href="admin.php" class="active">
                    Admin Panel
                </a>
            </li>

            <li>
                <a href="logout.php">
                    Disconnect
                </a>
            </li>

        </ul>

    </aside>

    <main class="main-content">

        <h2 style="
            font-family: var(--font-mono);
            margin-top:40px;
            margin-bottom:20px;
        ">
            Admin SecOps Panel
        </h2>

        <?php if (isset($success)): ?>

            <div class="alert"
                 style="
                 background:rgba(16,185,129,.1);
                 color:#10B981;
                 border-color:#10B981;
                 ">

                <?php
                echo htmlspecialchars(
                    $success,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>

        <?php if (isset($error)): ?>

            <div class="alert">

                <?php
                echo htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>

        <div class="glass-panel"
             style="padding:30px;">

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

                    <?php foreach ($users as $row): ?>

                        <tr>

                            <td>
                                #<?php
                                echo htmlspecialchars(
                                    (string)$row['id'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['email'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>

                                <span style="
                                    padding:4px 8px;
                                    border-radius:4px;
                                    font-size:12px;
                                    background:rgba(14,165,233,.2);
                                    color:#7DD3FC;
                                    font-weight:600;
                                ">

                                    <?php
                                    echo htmlspecialchars(
                                        strtoupper($row['role']),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </span>

                            </td>

                            <td>

                                <?php if (
                                    (int)$row['id'] !== $user_id
                                ): ?>

                                    <form method="POST"
                                          style="display:inline;">

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?php
                                            echo htmlspecialchars(
                                                $_SESSION['csrf_token'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="delete_id"
                                            value="<?php
                                            echo (int)$row['id'];
                                            ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-danger"
                                            style="
                                            padding:6px 12px;
                                            width:auto;
                                            "
                                            onclick="
                                            return confirm(
                                            'Delete user securely?'
                                            )
                                            "
                                        >
                                            Delete
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span
                                        style="
                                        color:#94A3B8;
                                        font-size:13px;
                                        "
                                    >
                                        Current Admin
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>
