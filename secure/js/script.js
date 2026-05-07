document.addEventListener('DOMContentLoaded', function() {
    
    // 1. FITUR SHOW/HIDE PASSWORD
    const togglePasswords = document.querySelectorAll('.password-toggle');
    
    togglePasswords.forEach(icon => {
        icon.addEventListener('click', function() {
            // Mengambil elemen input yang posisinya tepat sebelum icon
            const input = this.previousElementSibling;
            
            // Toggle tipe input dan icon FontAwesome
            if (input.getAttribute('type') === 'password') {
                input.setAttribute('type', 'text');
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
                this.style.color = 'var(--primary)'; // Memberikan efek nyala saat password terlihat
            } else {
                input.setAttribute('type', 'password');
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
                this.style.color = 'var(--text-muted)';
            }
        });
    });

    // 2. CLIENT-SIDE FORM VALIDATION (Mencegah submit kosong/salah format)
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Ambil semua input di dalam form yang sedang di-submit
            const inputs = form.querySelectorAll('.form-control');
            
            inputs.forEach(input => {
                // Reset styling error sebelumnya (jika ada)
                input.style.borderColor = 'var(--border-color)';
                
                // Cek jika input required tapi kosong
                if (input.hasAttribute('required') && input.value.trim() === '') {
                    isValid = false;
                    input.style.borderColor = '#EF4444'; // Warna merah error
                }
                
                // Cek khusus untuk password jika ada atribut minlength (untuk versi Secure)
                if (input.type === 'password' && input.hasAttribute('minlength')) {
                    const min = input.getAttribute('minlength');
                    if (input.value.length < min && input.value.length > 0) {
                        isValid = false;
                        input.style.borderColor = '#EF4444';
                        alert(`Password harus memiliki minimal ${min} karakter!`);
                    }
                }
            });
            
            if (!isValid) {
                // Hentikan proses submit ke server jika validasi gagal
                event.preventDefault();
                
                // Efek getar (shake) ringan jika error
                form.style.transform = 'translateX(10px)';
                setTimeout(() => { form.style.transform = 'translateX(-10px)'; }, 100);
                setTimeout(() => { form.style.transform = 'translateX(10px)'; }, 200);
                setTimeout(() => { form.style.transform = 'translateX(0)'; }, 300);
            }
        });
    });

});
