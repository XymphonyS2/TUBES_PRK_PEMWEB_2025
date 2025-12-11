// Main JavaScript untuk sistem
document.addEventListener('DOMContentLoaded', function() {
    // Auto hide alert setelah 5 detik - HANYA untuk alert message boxes
    // Selector SANGAT SPESIFIK: hanya alert message boxes yang punya border dan pesan
    // JANGAN hapus card stats atau icon container
    
    // Hanya pilih alert yang jelas-jelas adalah message box dengan border
    const alerts = document.querySelectorAll('.bg-red-50.border-red-200, .bg-green-50.border-green-200');
    
    alerts.forEach(alert => {
        // PENTING: Skip jika ada di dalam card-hover (card stats dashboard)
        if (alert.closest('.card-hover')) {
            return; // Jangan proses card stats
        }
        
        // Skip jika tidak punya border (bukan alert message)
        if (!alert.classList.contains('border-red-200') && !alert.classList.contains('border-green-200')) {
            return;
        }
        
        // Skip jika ada di dalam card stats (punya class bg-white.rounded-xl)
        if (alert.closest('.bg-white.rounded-xl') || alert.closest('.bg-white.rounded-2xl')) {
            return;
        }
        
        // Pastikan ini benar-benar alert message box dengan text yang jelas
        const text = alert.textContent.toLowerCase().trim();
        const isAlertMessage = text.includes('error') || text.includes('success') || 
                              text.includes('gagal') || text.includes('berhasil') ||
                              text.includes('terjadi') || text.includes('tidak dapat') ||
                              text.includes('perhatian') || text.includes('peringatan');
        
        // Hanya hide jika ini benar-benar alert message
        if (isAlertMessage && (alert.classList.contains('p-4') || alert.classList.contains('px-4'))) {
            setTimeout(() => {
                if (alert.parentNode) { // Pastikan masih ada di DOM
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                }
            }, 5000);
        }
    });
    
    // Format tanggal Indonesia
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value) {
                const date = new Date(this.value);
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                // Bisa ditambahkan formatter jika diperlukan
            }
        });
    });
});

// Fungsi untuk konfirmasi hapus
function confirmDelete(message = 'Yakin ingin menghapus?') {
    return confirm(message);
}

// Format number dengan pemisah ribuan
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
