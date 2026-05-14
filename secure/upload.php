<?php

declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| SECURITY HEADERS
|--------------------------------------------------------------------------
*/

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: no-referrer");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

/*
|--------------------------------------------------------------------------
| AUTH CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| SECURE UPLOAD CONFIG
|--------------------------------------------------------------------------
*/

$upload_dir = '/var/uploads/';

$allowed_mimes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif'
];

$max_size = 2 * 1024 * 1024; // 2MB

/*
|--------------------------------------------------------------------------
| CREATE DIRECTORY IF NOT EXISTS
|--------------------------------------------------------------------------
*/

if (!is_dir($upload_dir)) {

    if (!mkdir($upload_dir, 0755, true)) {
        die("Failed to create upload directory.");
    }
}

/*
|--------------------------------------------------------------------------
| HANDLE UPLOAD
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | CSRF VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        die("Invalid CSRF token.");
    }

    /*
    |--------------------------------------------------------------------------
    | FILE EXISTS
    |--------------------------------------------------------------------------
    */

    if (
        !isset($_FILES['profile_pic']) ||
        !is_uploaded_file($_FILES['profile_pic']['tmp_name'])
    ) {
        $error = "No file uploaded.";
    } else {

        $file = $_FILES['profile_pic'];

        /*
        |--------------------------------------------------------------------------
        | PHP UPLOAD ERROR
        |--------------------------------------------------------------------------
        */

        if ($file['error'] !== UPLOAD_ERR_OK) {

            $error = "Upload failed.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | FILE SIZE CHECK
            |--------------------------------------------------------------------------
            */

            if ($file['size'] > $max_size) {

                $error = "File too large. Maximum 2MB.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | ORIGINAL FILENAME
                |--------------------------------------------------------------------------
                */

                $original_name = trim($file['name']);

                /*
                |--------------------------------------------------------------------------
                | BLOCK DOUBLE EXTENSION / DANGEROUS EXTENSIONS
                |--------------------------------------------------------------------------
                */

                if (
                    preg_match(
                        '/\.(php|phtml|phar|php[0-9]?|cgi|pl|py|sh|asp|aspx|jsp)(\..*)?$/i',
                        $original_name
                    )
                ) {

                    $error = "Dangerous filename detected.";

                    error_log(
                        "[UPLOAD BLOCKED] Dangerous filename from IP " .
                        $_SERVER['REMOTE_ADDR'] .
                        " : " .
                        $original_name
                    );

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | MIME VALIDATION
                    |--------------------------------------------------------------------------
                    */

                    $finfo = finfo_open(FILEINFO_MIME_TYPE);

                    if ($finfo === false) {
                        die("Failed to initialize fileinfo.");
                    }

                    $mime_type = finfo_file(
                        $finfo,
                        $file['tmp_name']
                    );

                    finfo_close($finfo);

                    if (!array_key_exists($mime_type, $allowed_mimes)) {

                        $error = "Invalid file type.";

                        error_log(
                            "[UPLOAD BLOCKED] Invalid MIME from IP " .
                            $_SERVER['REMOTE_ADDR'] .
                            " : " .
                            $mime_type
                        );

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | VALIDATE REAL IMAGE
                        |--------------------------------------------------------------------------
                        */

                        $image_info = @getimagesize(
                            $file['tmp_name']
                        );

                        if ($image_info === false) {

                            $error = "Fake image detected.";

                            error_log(
                                "[UPLOAD BLOCKED] Fake image from IP " .
                                $_SERVER['REMOTE_ADDR']
                            );

                        } else {

                            /*
                            |--------------------------------------------------------------------------
                            | SAFE RANDOM FILENAME
                            |--------------------------------------------------------------------------
                            */

                            $extension = $allowed_mimes[$mime_type];

                            $new_filename =
                                bin2hex(random_bytes(32)) .
                                '.' .
                                $extension;

                            $destination =
                                $upload_dir .
                                $new_filename;

                            /*
                            |--------------------------------------------------------------------------
                            | MOVE FILE
                            |--------------------------------------------------------------------------
                            */

                            if (
                                !move_uploaded_file(
                                    $file['tmp_name'],
                                    $destination
                                )
                            ) {

                                $error = "Failed to save uploaded file.";

                            } else {

                                /*
                                |--------------------------------------------------------------------------
                                | RE-ENCODE IMAGE
                                |--------------------------------------------------------------------------
                                | Removes embedded payloads
                                */

                                $reencode_success = false;

                                switch ($mime_type) {

                                    case 'image/jpeg':

                                        $img =
                                            @imagecreatefromjpeg(
                                                $destination
                                            );

                                        if ($img !== false) {

                                            imagejpeg(
                                                $img,
                                                $destination,
                                                90
                                            );

                                            imagedestroy($img);

                                            $reencode_success = true;
                                        }

                                        break;

                                    case 'image/png':

                                        $img =
                                            @imagecreatefrompng(
                                                $destination
                                            );

                                        if ($img !== false) {

                                            imagepng(
                                                $img,
                                                $destination
                                            );

                                            imagedestroy($img);

                                            $reencode_success = true;
                                        }

                                        break;

                                    case 'image/gif':

                                        $img =
                                            @imagecreatefromgif(
                                                $destination
                                            );

                                        if ($img !== false) {

                                            imagegif(
                                                $img,
                                                $destination
                                            );

                                            imagedestroy($img);

                                            $reencode_success = true;
                                        }

                                        break;
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | IF RE-ENCODE FAILS
                                |--------------------------------------------------------------------------
                                */

                                if (!$reencode_success) {

                                    unlink($destination);

                                    $error = "Image processing failed.";

                                } else {

                                    /*
                                    |--------------------------------------------------------------------------
                                    | SAVE TO DATABASE
                                    |--------------------------------------------------------------------------
                                    */

                                    $stmt = $pdo->prepare(
                                        "UPDATE users
                                         SET profile_pic = ?
                                         WHERE id = ?"
                                    );

                                    $stmt->execute([
                                        $new_filename,
                                        $user_id
                                    ]);

                                    $success =
                                        "Image uploaded securely.";
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| GET USER DATA
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare(
    "SELECT role, profile_pic
     FROM users
     WHERE id = ?"
);

$stmt->execute([$user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

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
                    <img
    src="view_image.php?file=<?php
    echo urlencode($user['profile_pic']);
    ?>"
    alt="Profile Picture"
    style="
        width:150px;
        height:150px;
        object-fit:cover;
        border-radius:50%;
        border:3px solid var(--primary);
        box-shadow:0 0 20px var(--primary-glow);
    "
>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
