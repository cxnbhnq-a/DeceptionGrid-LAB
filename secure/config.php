<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ERROR HANDLING
|--------------------------------------------------------------------------
*/

ini_set('display_errors', '0');
error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| SESSION HARDENING
|--------------------------------------------------------------------------
*/

ini_set('session.use_strict_mode', '1');
ini_set('session.sid_length', '64');
ini_set('session.sid_bits_per_character', '6');
ini_set('session.cookie_httponly', '1');

$https =
    (!empty($_SERVER['HTTPS']) &&
    $_SERVER['HTTPS'] !== 'off');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_name('SECURE_LAB_SESSION');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| SESSION FIXATION PROTECTION
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['initiated'])) {

    session_regenerate_id(true);

    $_SESSION['initiated'] = true;

    $_SESSION['created_at'] = time();
}

/*
|--------------------------------------------------------------------------
| SESSION TIMEOUT
|--------------------------------------------------------------------------
*/

$timeout = 1800;

if (
    isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > $timeout
) {

    $_SESSION = [];

    session_destroy();

    header("Location: login.php");

    exit();
}

$_SESSION['last_activity'] = time();

/*
|--------------------------------------------------------------------------
| PERIODIC SESSION REGENERATION
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['last_regeneration']) ||
    (time() - $_SESSION['last_regeneration']) > 900
) {

    session_regenerate_id(true);

    $_SESSION['last_regeneration'] = time();
}

/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] =
        bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| SECURITY HEADERS
|--------------------------------------------------------------------------
*/

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

header(
    "Content-Security-Policy:
    default-src 'self';
    style-src 'self' https://cdnjs.cloudflare.com;
    script-src 'self';
    img-src 'self' data:;
    object-src 'none';
    frame-ancestors 'none';
    base-uri 'self';"
);

/*
|--------------------------------------------------------------------------
| DATABASE CONFIG
|--------------------------------------------------------------------------
*/

$host = 'localhost';
$dbname = 'student_registration';
$user = 'sispen';
$pass = '0102';

$dsn =
    "mysql:host=$host;
    dbname=$dbname;
    charset=utf8mb4";

$options = [

    PDO::ATTR_ERRMODE =>
        PDO::ERRMODE_EXCEPTION,

    PDO::ATTR_DEFAULT_FETCH_MODE =>
        PDO::FETCH_ASSOC,

    PDO::ATTR_EMULATE_PREPARES =>
        false
];

try {

    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        $options
    );

} catch (PDOException $e) {

    error_log($e->getMessage());

    die("Database connection failed.");
}
?>
