// script.js - SIAKAD POLIJE

// Fade-in animasi saat halaman load
document.addEventListener('DOMContentLoaded', function () {
    // Animasi masuk untuk elemen utama
    const main = document.querySelector('main');
    if (main) {
        main.style.opacity = '0';
        main.style.transform = 'translateY(16px)';
        main.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        setTimeout(() => {
            main.style.opacity = '1';
            main.style.transform = 'translateY(0)';
        }, 50);
    }

    // Animasi input focus
    document.querySelectorAll('.modern-input').forEach(input => {
        input.addEventListener('focus', function () {
            this.parentElement && this.parentElement.classList.add('input-focused');
        });
        input.addEventListener('blur', function () {
            this.parentElement && this.parentElement.classList.remove('input-focused');
        });
    });

    // Auto-dismiss alert setelah 3 detik (jika ada)
    const alerts = document.querySelectorAll('.alert-auto-dismiss');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
});
