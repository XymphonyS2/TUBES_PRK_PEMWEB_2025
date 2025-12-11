<?php
ob_start();

require_once __DIR__ . '/../config/auth.php';
cekRole(['dokter']);
$page_title = 'Review Menu';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu_id = (int)($_POST['menu_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $pesan = $_POST['pesan'] ?? '';
    
    if ($menu_id && $action) {
        $dokter_id = $_SESSION['user_id'];
        $status = $action == 'approve' ? 'disetujui' : 'ditolak';
        $tanggal_persetujuan = date('Y-m-d H:i:s');
        
        $stmt = $conn->prepare("UPDATE menu SET status = ?, dokter_id = ?, pesan_dokter = ?, tanggal_persetujuan = ? WHERE id = ?");
        $stmt->bind_param("sissi", $status, $dokter_id, $pesan, $tanggal_persetujuan, $menu_id);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            $message = "Menu berhasil " . ($action == 'approve' ? 'disetujui' : 'ditolak');
            header('Location: daftar_menu_dokter.php?success=' . urlencode($message));
            exit();
        } else {
            $error = "Gagal memproses menu: " . $stmt->error;
            $stmt->close();
        }
    }
}

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

$menus = $conn->query("
    SELECT m.*, u.nama_vendor, u.nama_lengkap as nama_vendor_lengkap
    FROM menu m
    LEFT JOIN users u ON m.vendor_id = u.id
    WHERE m.status = 'pending'
    ORDER BY m.created_at ASC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Review Menu dari Vendor</h2>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500 flex-shrink-0"></i>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (empty($menus)): ?>
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Tidak ada menu yang perlu direview</p>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($menus as $menu): ?>
            <div class="bg-white rounded-xl p-6 sm:p-8 card-shadow card-hover border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vendor</label>
                        <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo htmlspecialchars($menu['nama_vendor'] ?? $menu['nama_vendor_lengkap'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200">
                            <?php 
                            echo date('d/m/Y', strtotime($menu['tanggal_mulai'])); 
                            if ($menu['tanggal_mulai'] != $menu['tanggal_selesai']) {
                                echo ' - ' . date('d/m/Y', strtotime($menu['tanggal_selesai']));
                            }
                            ?>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Makanan</label>
                        <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo htmlspecialchars($menu['jenis_makanan']); ?></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Minuman</label>
                        <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo htmlspecialchars($menu['jenis_minuman']); ?></p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Komposisi</label>
                        <p class="text-gray-800 whitespace-pre-wrap bg-gray-50 rounded-xl p-4 border border-gray-200 min-h-[80px]"><?php echo htmlspecialchars($menu['komposisi']); ?></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Porsi Maksimal</label>
                        <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo number_format($menu['porsi_maksimal']); ?></p>
                    </div>
                </div>
                
                <form method="POST" class="mt-6 border-t border-gray-200 pt-6" onsubmit="return validateForm(this)">
                    <input type="hidden" name="menu_id" value="<?php echo $menu['id']; ?>">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan/Alasan <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(wajib jika ditolak)</span></label>
                        <textarea name="pesan" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" placeholder="Tuliskan review gizi atau alasan penolakan..."></textarea>
                    </div>
                    
                    <div class="flex flex-wrap gap-4">
                        <button type="submit" name="action" value="approve" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-500 text-white font-semibold rounded-xl hover:from-green-700 hover:to-green-600 focus:ring-4 focus:ring-green-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-green-500/30">
                            <i class="fas fa-check mr-2"></i>Setujui (Gizi Terpenuhi)
                        </button>
                        <button type="submit" name="action" value="reject" class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-500 text-white font-semibold rounded-xl hover:from-red-700 hover:to-red-600 focus:ring-4 focus:ring-red-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-red-500/30">
                            <i class="fas fa-times mr-2"></i>Tolak (Gizi Tidak Terpenuhi)
                        </button>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function validateForm(form) {
    const action = form.querySelector('button[type="submit"]:focus')?.value || 
                   event.submitter?.value || 
                   form.querySelector('input[name="action"]')?.value;
    const pesan = form.querySelector('textarea[name="pesan"]').value.trim();
    
    if (action === 'reject' && pesan === '') {
        alert('Catatan/alasan wajib diisi jika menu ditolak!');
        form.querySelector('textarea[name="pesan"]').focus();
        return false;
    }
    
    const confirmMessage = action === 'approve' 
        ? 'Apakah Anda yakin ingin menyetujui menu ini?' 
        : 'Apakah Anda yakin ingin menolak menu ini?';
    
    return confirm(confirmMessage);
}

document.querySelectorAll('button[name="action"]').forEach(button => {
    button.addEventListener('click', function() {
        this.form.querySelector('input[name="action"]')?.remove();
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'action';
        hiddenInput.value = this.value;
        this.form.appendChild(hiddenInput);
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

<?php ob_end_flush();?>