<?php

declare(strict_types=1);

require_once 'config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit();
}

if (!isset($_GET['file'])) {
    http_response_code(400);
    exit();
}

$base_dir = realpath('/var/uploads');

$filename = basename($_GET['file']);

$path = realpath($base_dir . '/' . $filename);

if (
    $path === false ||
    !str_starts_with($path, $base_dir)
) {
    http_response_code(403);
    exit();
}

if (!file_exists($path)) {
    http_response_code(404);
    exit();
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);

$mime = finfo_file($finfo, $path);

finfo_close($finfo);

$allowed = [
    'image/jpeg',
    'image/png',
    'image/gif'
];

if (!in_array($mime, $allowed, true)) {
    http_response_code(403);
    exit();
}

header("Content-Type: " . $mime);
header("Content-Length: " . filesize($path));
header("X-Content-Type-Options: nosniff");

readfile($path);
exit();
