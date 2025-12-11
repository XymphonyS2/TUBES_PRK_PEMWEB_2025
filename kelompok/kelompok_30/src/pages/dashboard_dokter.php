<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['dokter']);
$page_title = 'Dashboard Dokter';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();

$menu_pending = $conn->query("SELECT COUNT(*) as total FROM menu WHERE status = 'pending'")->fetch_assoc()['total'];
$menu_disetujui = $conn->query("SELECT COUNT(*) as total FROM menu WHERE status = 'disetujui' AND dokter_id = " . $_SESSION['user_id'])->fetch_assoc()['total'];
$menu_ditolak = $conn->query("SELECT COUNT(*) as total FROM menu WHERE status = 'ditolak' AND dokter_id = " . $_SESSION['user_id'])->fetch_assoc()['total'];

$conn->close();
?>

<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 card-shadow">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-yellow-500 text-lg sm:text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm sm:text-sm mb-1.5">Menu Pending</p>
        <p class="text-2xl sm:text-3xl font-bold text-gray-800"><?php echo $menu_pending; ?></p>
    </div>
    
    <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 card-shadow">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-green-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 text-lg sm:text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm sm:text-sm mb-1.5">Menu Disetujui</p>
        <p class="text-2xl sm:text-3xl font-bold text-gray-800"><?php echo $menu_disetujui; ?></p>
    </div>
    
    <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 card-shadow">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-red-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-times-circle text-red-500 text-lg sm:text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm sm:text-sm mb-1.5">Menu Ditolak</p>
        <p class="text-2xl sm:text-3xl font-bold text-gray-800"><?php echo $menu_ditolak; ?></p>
    </div>
</div>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h2>
    <p class="text-gray-600 mb-4">Sebagai Dokter, Anda dapat:</p>
    <ul class="list-disc list-inside space-y-2 text-gray-700 pl-4">
        <li>Melihat list menu yang dikirimkan oleh Vendor</li>
        <li>Memberikan persetujuan atau tidak berdasarkan gizi terpenuhi atau tidak</li>
        <li>Memberikan catatan/alasan jika menu ditolak</li>
    </ul>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

