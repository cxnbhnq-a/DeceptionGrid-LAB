# DeceptionGrid LAB

Cyber Security Web Application Lab for Secure Coding & Vulnerability Demonstration.

---

# Overview

DeceptionGrid LAB adalah project pembelajaran keamanan aplikasi web berbasis PHP dan MySQL yang menyediakan dua environment berbeda:

* `vulnerable/` → aplikasi yang sengaja dibuat rentan
* `secure/` → aplikasi yang sudah diperbaiki dengan implementasi secure coding

Lab ini dibuat untuk:

* Praktikum keamanan web
* Demonstrasi vulnerability
* Pembelajaran secure coding
* Simulasi exploit dan patching
* Presentasi materi cyber security

---

# Learning Objectives

Lab ini membahas beberapa topik utama:

1. Input Validation & XSS
2. SQL Injection
3. Password Security
4. Session Management
5. Secure File Upload
6. Server Permission Security
7. CSRF Protection
8. Output Encoding
9. Authentication Security
10. Defense in Depth

---

# Project Structure

```bash
lab-learn/
├── assets
│   ├── css
│   └── js
├── index.php
├── README.md
├── secure
│   ├── admin.php
│   ├── config.php
│   ├── dashboard.php
│   ├── edit_profile.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   ├── upload.php
│   └── view_image.php
└── vulnerable
    ├── admin.php
    ├── config.php
    ├── dashboard.php
    ├── edit_profile.php
    ├── index.php
    ├── login.php
    ├── logout.php
    ├── register.php
    ├── upload.php
    └── uploads
```

---

# Vulnerable Environment

Folder `vulnerable/` berisi aplikasi yang sengaja dibuat tidak aman untuk tujuan pembelajaran.

## Vulnerabilities Included

### SQL Injection

Contoh:

```php
$query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
```

Payload bypass login:

```sql
' OR 1=1 -- -
```

---

### Cross Site Scripting (XSS)

Reflected XSS:

```text
?search=<script>alert(1)</script>
```

Stored XSS:

```html
<img src=x onerror=alert(1)>
```

---

### Weak Password Hashing

Menggunakan:

```php
md5()
```

Yang sudah tidak aman terhadap brute force dan rainbow table attack.

---

### Unsafe File Upload

Vulnerable upload menerima file berbahaya seperti:

```text
shell.php
shell.php.png
```

Yang dapat menyebabkan:

* Remote Code Execution (RCE)
* Web shell upload
* Server takeover

---

### Information Disclosure

Query SQL ditampilkan langsung ke user ketika error.

Contoh:

```php
$error = "Login failed: " . $query;
```

---

# Secure Environment

Folder `secure/` merupakan versi aplikasi yang sudah diamankan menggunakan secure coding.

## Security Improvements

### Prepared Statements (PDO)

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

Mencegah SQL Injection.

---

### Output Encoding

```php
htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
```

Mencegah XSS.

---

### Strong Password Hashing

```php
password_hash()
password_verify()
```

Menggunakan hashing modern bawaan PHP.

---

### CSRF Protection

Menggunakan CSRF token:

```php
$_SESSION['csrf_token']
```

---

### Secure Session Management

Fitur:

* Session regeneration
* HttpOnly cookie
* SameSite cookie
* Session timeout
* Session fixation protection

---

### Secure File Upload

Implementasi:

* MIME validation
* Extension validation
* UUID filename
* Upload di luar web root
* File serving via PHP
* File permission hardening
* Random filename generation

---

# Upload Storage Architecture

## Secure Upload Storage

File upload pada secure environment disimpan di:

```bash
/var/uploads/
```

Bukan di dalam:

```bash
/var/www/
```

Hal ini dilakukan untuk mencegah file upload dieksekusi langsung oleh web server.

---

## Why This Is Important

Jika file upload disimpan di dalam web root:

```bash
/var/www/html/uploads/
```

Attacker dapat mengakses file secara langsung melalui browser:

```text
http://target/uploads/shell.php
```

Yang dapat menyebabkan:

* Remote Code Execution
* Web shell access
* Full server compromise

---

## Secure Upload Flow

### Vulnerable Flow

```text
User Upload -> /var/www/uploads -> Accessible Directly
```

### Secure Flow

```text
User Upload -> /var/uploads -> Served Securely via PHP
```

---

## Secure File Access

File ditampilkan menggunakan:

```text
view_image.php
```

Bukan direct access ke filesystem.

File dibaca menggunakan:

```php
readfile()
```

Dengan validasi:

* UUID filename
* File existence check
* MIME validation
* Access control

---

# Server Permission Security

## Recommended Permissions

### Upload Directory

```bash
sudo chown www-data:www-data /var/uploads
sudo chmod 700 /var/uploads
```

---

### Prevent PHP Execution

Jika menggunakan Apache:

```apache
php_admin_flag engine off
```

Atau:

```apache
RemoveHandler .php
RemoveType .php
```

---

# Technologies Used

* PHP
* MySQL / MariaDB
* Apache2
* HTML5
* CSS3
* JavaScript
* Font Awesome

---

# Installation

## Clone Repository

```bash
git clone https://github.com/cxnbhnq-a/DeceptionGrid-LAB.git
```

---

## Move Project

```bash
sudo mv DeceptionGrid-LAB /var/www/lab-learn
```

---

## Setup Database

Create database:

```sql
CREATE DATABASE student_registration;
```

Import schema and configure database credentials inside:

```bash
secure/config.php
vulnerable/config.php
```

---

## Create Secure Upload Directory

```bash
sudo mkdir -p /var/uploads
sudo chown www-data:www-data /var/uploads
sudo chmod 700 /var/uploads
```

---

## Restart Apache

```bash
sudo systemctl restart apache2
```

---

# Demonstration Topics

Lab ini cocok untuk demonstrasi:

* SQL Injection Login Bypass
* Reflected XSS
* Stored XSS
* Weak Hash Exploitation
* File Upload RCE
* Session Security
* Secure Coding
* Defense in Depth
* Access Control
* Secure Authentication

---

# Educational Purpose Disclaimer

Project ini dibuat hanya untuk:

* edukasi
* praktikum
* penelitian keamanan
* pembelajaran secure coding

Jangan gunakan teknik yang dipelajari untuk aktivitas ilegal.

---
