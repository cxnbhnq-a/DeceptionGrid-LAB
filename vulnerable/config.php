<?php
// VULNERABLE CONFIG - No security measures
// Vulnerability: Plain credentials, no error handling security

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'student_registration';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Vulnerability: Exposing error details
    die("Connection failed: " . mysqli_connect_error());
}
?>