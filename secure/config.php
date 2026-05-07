<?php
// SECURE CONFIG - Using PDO with error handling
// Security: PDO prevents SQL injection, error handling doesn't expose details
session_name('SEC_LAB_SESSION');
session_start();
$host = 'localhost';
$dbname = 'student_registration';
$user = 'sispen';
$pass = '0102';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Security: Set charset to prevent encoding issues
    $pdo->exec("SET NAMES utf8");
} catch (PDOException $e) {
    // Security: Don't expose error details in production
    die("Database connection failed. Please try again later.");
}
?>
