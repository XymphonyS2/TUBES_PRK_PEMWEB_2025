document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.bg-red-50.border-red-200, .bg-green-50.border-green-200');
    
    alerts.forEach(alert => {
        if (alert.closest('.card-hover')) {
            return;
        }
        
        if (!alert.classList.contains('border-red-200') && !alert.classList.contains('border-green-200')) {
            return;
        }
        
        if (alert.closest('.bg-white.rounded-xl') || alert.closest('.bg-white.rounded-2xl')) {
            return;
        }
        
        const text = alert.textContent.toLowerCase().trim();
        const isAlertMessage = text.includes('error') || text.includes('success') || 
                              text.includes('gagal') || text.includes('berhasil') ||
                              text.includes('terjadi') || text.includes('tidak dapat') ||
                              text.includes('perhatian') || text.includes('peringatan');
        
        if (isAlertMessage && (alert.classList.contains('p-4') || alert.classList.contains('px-4'))) {
            setTimeout(() => {
                if (alert.parentNode) { 
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
    
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value) {
                const date = new Date(this.value);
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
            }
        });
    });
});

function confirmDelete(message = 'Yakin ingin menghapus?') {
    return confirm(message);
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
