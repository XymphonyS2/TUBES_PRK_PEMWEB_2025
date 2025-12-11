<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['sekolah']);
$page_title = 'Detail Jadwal';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$conn = koneksiDatabase();
$sekolah_id = $_SESSION['user_id'];

$jadwal = $conn->query("
    SELECT j.*, m.jenis_makanan, m.jenis_minuman, m.komposisi, u.nama_vendor
    FROM jadwal j
    JOIN menu m ON j.menu_id = m.id
    JOIN users u ON m.vendor_id = u.id
    WHERE j.id = $id AND j.sekolah_id = $sekolah_id
")->fetch_assoc();

$conn->close();

if (!$jadwal) {
    header('Location: jadwal_menu.php');
    exit();
}
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Detail Jadwal Menu</h2>
            <p class="text-sm text-gray-500">Informasi lengkap jadwal menu</p>
        </div>
        <a href="jadwal_menu.php" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
            <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo date('d/m/Y', strtotime($jadwal['tanggal'])); ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor</label>
            <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo htmlspecialchars($jadwal['nama_vendor']); ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Makanan</label>
            <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo htmlspecialchars($jadwal['jenis_makanan']); ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Minuman</label>
            <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo htmlspecialchars($jadwal['jenis_minuman']); ?></p>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Komposisi</label>
            <p class="text-gray-800 whitespace-pre-wrap bg-gray-50 rounded-xl p-4 border border-gray-200 min-h-[80px]"><?php echo htmlspecialchars($jadwal['komposisi']); ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Porsi Ditentukan</label>
            <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo number_format($jadwal['porsi_ditentukan']); ?></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

