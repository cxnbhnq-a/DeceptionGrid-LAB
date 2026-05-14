<?php
// VULNERABLE CONFIG - No security measures
// Vulnerability: Plain credentials, no error handling security
session_name('VULN_LAB_SESSION');
session_start();

$host = 'localhost';
$user = '(nama user)';
$pass = '(password)';
$db = '(nama database)';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Vulnerability: Exposing error details
    die("Connection failed: " . mysqli_connect_error());
}
?>
