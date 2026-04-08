// script.js
document.addEventListener('DOMContentLoaded', () => {
    // 1. Animasi Masuk untuk Elemen List
    const cards = document.querySelectorAll('.glass-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // 2. Konfirmasi Hapus yang Lebih Manis
    const deleteButtons = document.querySelectorAll('a[onclick*="confirm"]');
    deleteButtons.forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const url = btn.getAttribute('href');
            if (confirm("Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan.")) {
                // Beri efek fade out sebelum pindah halaman
                btn.closest('.glass-card').style.opacity = '0.3';
                btn.closest('.glass-card').style.transform = 'scale(0.9)';
                setTimeout(() => {
                    window.location.href = url;
                }, 300);
            }
        };
    });

    // 3. Validasi Form Real-time (Nilai)
    const inputNilai = document.querySelector('input[name="nilai"]');
    if (inputNilai) {
        inputNilai.addEventListener('input', (e) => {
            const val = parseInt(e.target.value);
            if (val > 100) e.target.value = 100;
            if (val < 0) e.target.value = 0;
            
            // Ubah warna border berdasarkan nilai
            if (val >= 85) e.target.style.borderColor = '#10b981'; // Hijau
            else if (val >= 70) e.target.style.borderColor = '#3b82f6'; // Biru
            else if (val < 40) e.target.style.borderColor = '#ef4444'; // Merah
        });
    }

    // 4. Efek Mengetik pada Judul (Simple Typewriter)
    const subtitle = document.querySelector('header p');
    if (subtitle) {
        const text = subtitle.innerHTML;
        subtitle.innerHTML = '';
        let i = 0;
        function typeWriter() {
            if (i < text.length) {
                subtitle.innerHTML += text.charAt(i);
                i++;
                setTimeout(typeWriter, 30);
            }
        }
        typeWriter();
    }
});