# Student Registration System

This project contains two versions of a student registration website built with PHP Native, HTML, CSS, JavaScript, and MySQL.

## Versions

### Version 1: Vulnerable (For Security Learning)
This version intentionally contains security vulnerabilities to demonstrate common web security issues.

**Vulnerabilities Included:**
1. **XSS (Cross-Site Scripting)**
   - Reflected XSS: Input from forms is echoed without escaping.
   - Stored XSS: User input stored in database and displayed without sanitization.
   - No use of `htmlspecialchars()`.

2. **SQL Injection**
   - Login and register forms use direct string concatenation in queries.
   - No prepared statements.

3. **Weak Password Security**
   - Passwords stored using MD5 hash (easily crackable).
   - No modern hashing like bcrypt.

4. **Weak Session Management**
   - Session fixation vulnerability.
   - No session ID regeneration.
   - No session timeout.

5. **File Upload Vulnerabilities**
   - No file extension validation.
   - No MIME type checking.
   - Allows uploading PHP files (potential RCE).

6. **Server Permission Issues**
   - Upload folder is writable.
   - Sensitive files accessible directly.
   - No .htaccess protection.

### Version 2: Secure Version
This version implements security best practices.

**Security Features:**
1. Input validation and sanitization.
2. `htmlspecialchars()` for XSS prevention.
3. Prepared statements with PDO.
4. `password_hash()` and `password_verify()`.
5. Secure session management (regenerate ID, timeout, secure cookies).
6. Secure file upload (extension, MIME, size validation, random rename).
7. Proper server permissions.
8. CSRF tokens.
9. Safe error handling.

## Setup Instructions

1. Install XAMPP or Laragon.
2. Start Apache and MySQL.
3. Import `database.sql` into MySQL.
4. Place the project folders in `htdocs` (XAMPP) or `www` (Laragon).
5. Access via `http://localhost/student_registration_system/vulnerable/` or `/secure/`.

## Folder Structure

```
student_registration_system/
├── database.sql
├── README.md
├── vulnerable/
│   ├── assets/
│   ├── uploads/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   ├── config.php
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── upload.php
│   ├── edit_profile.php
│   ├── admin.php
│   └── logout.php
└── secure/
    ├── assets/
    ├── uploads/
    ├── css/
    │   └── style.css
    ├── js/
    │   └── script.js
    ├── config.php
    ├── index.php
    ├── login.php
    ├── register.php
    ├── dashboard.php
    ├── upload.php
    ├── edit_profile.php
    ├── admin.php
    └── logout.php
```

## Usage

- Register as a student.
- Login to dashboard.
- Upload profile picture.
- Edit profile.
- Admin can view all students.

## Learning Objectives

- Understand common web vulnerabilities.
- Learn how to implement secure coding practices.
- Compare vulnerable vs secure code.