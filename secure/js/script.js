// JavaScript for client-side validation and interactions

function validateRegisterForm() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (name === '' || email === '' || password === '') {
        alert('All fields are required');
        return false;
    }
    return true;
}

function validateLoginForm() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (email === '' || password === '') {
        alert('Email and password are required');
        return false;
    }
    return true;
}

// Attach to forms if needed
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.onsubmit = validateRegisterForm;
    }
    
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.onsubmit = validateLoginForm;
    }
});