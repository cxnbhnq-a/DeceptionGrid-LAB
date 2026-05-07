<?php
// VULNERABLE CONFIG - No security measures
// Vulnerability: Plain credentials, no error handling security
session_name('VULN_LAB_SESSION');
session_start();

$host = 'localhost';
$user = 'sispen';
$pass = '0102';
$db = 'student_registration';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Vulnerability: Exposing error details
    die("Connection failed: " . mysqli_connect_error());
}
?>
