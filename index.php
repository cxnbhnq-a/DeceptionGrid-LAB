<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeceptionGrid | Web Security Learning Platform</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;700&family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="cyber-grid"></div>
    <div class="glow-blob blob-1"></div>
    <div class="glow-blob blob-2"></div>

    <nav class="navbar glass-panel">
        <div class="container nav-content">
            <div class="logo">
                <i class="fa-solid fa-shield-virus neon-text"></i>
                <span>Deception<span class="neon-text">Grid</span> Lab</span>
            </div>
            <div class="nav-links">
                <a href="#topics">Learning Topics</a>
                <a href="#comparison">Comparison</a>
                <a href="https://github.com/cxnbhnq-a" target="_blank"><i class="fa-brands fa-github"></i> GitHub</a>
            </div>
        </div>
    </nav>

    <header class="hero container">
        <div class="hero-content" data-aos="fade-up">
            <div class="warning-badge">
                <i class="fa-solid fa-triangle-exclamation"></i> Educational Purpose Only
            </div>
            <h1 class="glitch-text">Web Security <br>Learning Platform</h1>
            <p class="subtitle">Pelajari perbedaan antara aplikasi rentan dan aplikasi aman melalui simulasi nyata. Eksploitasi kerentanan OWASP dan pelajari cara memitigasinya dengan teknik <i>secure coding</i>.</p>
            
            <div class="action-buttons">
                <a href="vulnerable/login.php" class="btn btn-vulnerable pulse-red">
                    <i class="fa-solid fa-bug"></i>
                    <div class="btn-text">
                        <strong>Vulnerable Version</strong>
                        <span>Intentionally flawed lab</span>
                    </div>
                </a>
                
                <a href="secure/login.php" class="btn btn-secure pulse-blue">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div class="btn-text">
                        <strong>Secure Version</strong>
                        <span>Patched & hardened</span>
                    </div>
                </a>
            </div>
        </div>
    </header>

    <section id="topics" class="container section">
        <h2 class="section-title" data-aos="fade-right"><span class="neon-text">/</span> Learning Topics</h2>
        <div class="grid-cards">
            <div class="cyber-card glass-panel" data-aos="fade-up" data-aos-delay="100">
                <i class="fa-solid fa-code neon-red"></i>
                <h3>Cross-Site Scripting (XSS)</h3>
                <p>Eksploitasi input yang tidak divalidasi untuk mengeksekusi script berbahaya pada browser pengguna lain.</p>
            </div>
            <div class="cyber-card glass-panel" data-aos="fade-up" data-aos-delay="200">
                <i class="fa-solid fa-database neon-red"></i>
                <h3>SQL Injection (SQLi)</h3>
                <p>Manipulasi query database backend melalui form input untuk mem-bypass autentikasi atau mencuri data.</p>
            </div>
            <div class="cyber-card glass-panel" data-aos="fade-up" data-aos-delay="300">
                <i class="fa-solid fa-file-arrow-up neon-red"></i>
                <h3>Insecure File Upload</h3>
                <p>Bypass filter ekstensi file untuk mengunggah web shell (PHP) dan mengambil alih server.</p>
            </div>
        </div>
    </section>

    <section id="comparison" class="container section">
        <h2 class="section-title" data-aos="fade-right"><span class="neon-text">/</span> Architecture Comparison</h2>
        <div class="table-container glass-panel" data-aos="fade-up">
            <table class="cyber-table">
                <thead>
                    <tr>
                        <th>Feature / Vector</th>
                        <th class="text-red"><i class="fa-solid fa-xmark"></i> Vulnerable Implementation</th>
                        <th class="text-blue"><i class="fa-solid fa-check-double"></i> Secure Implementation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Database Queries</strong></td>
                        <td class="text-red">Raw String Concatenation <code>"... id='$id'"</code></td>
                        <td class="text-blue">Prepared Statements (PDO/MySQLi)</td>
                    </tr>
                    <tr>
                        <td><strong>Password Storage</strong></td>
                        <td class="text-red">Plaintext or MD5 Hashing</td>
                        <td class="text-blue">Bcrypt (<code>password_hash()</code>)</td>
                    </tr>
                    <tr>
                        <td><strong>Data Output</strong></td>
                        <td class="text-red">Direct Echo (XSS Vector)</td>
                        <td class="text-blue">Escaped (<code>htmlspecialchars()</code>)</td>
                    </tr>
                    <tr>
                        <td><strong>Session Handling</strong></td>
                        <td class="text-red">Static Session ID (Fixation)</td>
                        <td class="text-blue">Regenerated (<code>session_regenerate_id()</code>)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="cyber-footer glass-panel">
        <div class="container">
            <p>&copy; 2026 DeceptionGrid Lab. Created for cybersecurity education and training.</p>
            <p class="disclaimer"><i class="fa-solid fa-triangle-exclamation text-red"></i> Do not use these vulnerabilities on systems you do not own or have explicit permission to test.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
    </script>
</body>
</html>
