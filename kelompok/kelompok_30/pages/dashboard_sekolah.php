<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['sekolah']);
$page_title = 'Dashboard Sekolah';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();
$sekolah_id = $_SESSION['user_id'];

// Statistik
$jadwal_minggu_ini = $conn->query("SELECT COUNT(*) as total FROM jadwal WHERE sekolah_id = $sekolah_id AND tanggal >= CURDATE() AND tanggal <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['total'];
$pengiriman_hari_ini = $conn->query("SELECT COUNT(*) as total FROM pengiriman WHERE sekolah_id = $sekolah_id AND tanggal_pengiriman = CURDATE()")->fetch_assoc()['total'];
$keluhan_dibuat = $conn->query("SELECT COUNT(*) as total FROM keluhan WHERE sekolah_id = $sekolah_id")->fetch_assoc()['total'];

$conn->close();
?>

<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 card-shadow">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-alt text-blue-500 text-lg sm:text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm sm:text-sm mb-1.5">Jadwal 7 Hari Ke Depan</p>
        <p class="text-2xl sm:text-3xl font-bold text-gray-800"><?php echo $jadwal_minggu_ini; ?></p>
    </div>
    
    <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 card-shadow">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-green-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-truck text-green-500 text-lg sm:text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm sm:text-sm mb-1.5">Pengiriman Hari Ini</p>
        <p class="text-2xl sm:text-3xl font-bold text-gray-800"><?php echo $pengiriman_hari_ini; ?></p>
    </div>
    
    <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 card-shadow">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-red-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-comment-dots text-red-500 text-lg sm:text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm sm:text-sm mb-1.5">Keluhan Dibuat</p>
        <p class="text-2xl sm:text-3xl font-bold text-gray-800"><?php echo $keluhan_dibuat; ?></p>
    </div>
</div>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h2>
    <p class="text-gray-600 mb-4">Sebagai Sekolah, Anda dapat:</p>
    <ul class="list-disc list-inside space-y-2 text-gray-700 pl-4">
        <li>Melihat jadwal menu yang sudah ditentukan pada hari itu dan setelahnya</li>
        <li>Menerima makanan yang dikirim Vendor</li>
        <li>Membuat keluhan terhadap menu yang dikirim (basi, berbau, porsi kurang, komposisi tidak sesuai, dll)</li>
    </ul>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

