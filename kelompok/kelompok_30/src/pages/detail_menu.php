<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai', 'dokter', 'vendor']);
$page_title = 'Detail Menu';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$menu_id = (int)($_GET['id'] ?? 0);
$from_page = $_GET['from'] ?? '';

if (!$menu_id) {
    $default_page = 'dashboard_' . $_SESSION['role'] . '.php';
    if ($from_page) {
        header('Location: ' . $from_page);
    } else {
        header('Location: ' . $default_page);
    }
    exit();
}

$conn = koneksiDatabase();
$stmt = $conn->prepare("
    SELECT m.*, u.nama_vendor, u.nama_lengkap as nama_vendor_lengkap,
           d.nama_lengkap as nama_dokter, d.gelar
    FROM menu m
    LEFT JOIN users u ON m.vendor_id = u.id
    LEFT JOIN users d ON m.dokter_id = d.id
    WHERE m.id = ?
");
$stmt->bind_param("i", $menu_id);
$stmt->execute();
$menu = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$menu) {
    $conn->close();
    $default_page = 'dashboard_' . $_SESSION['role'] . '.php';
    if ($from_page) {
        header('Location: ' . $from_page);
    } else {
        header('Location: ' . $default_page);
    }
    exit();
}

$conn->close();

$back_url = $from_page;
if (empty($back_url)) {
    switch ($_SESSION['role']) {
        case 'vendor':
            $back_url = 'buat_menu.php';
            break;
        case 'pegawai':
            $back_url = 'daftar_menu_pegawai.php';
            break;
        case 'dokter':
            $back_url = 'daftar_menu_dokter.php';
            break;
        default:
            $back_url = 'dashboard_' . $_SESSION['role'] . '.php';
    }
}
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Detail Menu</h2>
            <p class="text-sm text-gray-500">Informasi lengkap menu</p>
        </div>
        <a href="<?php echo htmlspecialchars($back_url); ?>" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
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
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                <?php
                $status_class = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'disetujui' => 'bg-green-100 text-green-800',
                    'ditolak' => 'bg-red-100 text-red-800'
                ];
                $status_text = [
                    'pending' => 'Pending',
                    'disetujui' => 'Disetujui',
                    'ditolak' => 'Ditolak'
                ];
                ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $status_class[$menu['status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                    <?php echo $status_text[$menu['status']] ?? ucfirst($menu['status']); ?>
                </span>
            </div>
        </div>
        
        <?php if ($menu['dokter_id']): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Dokter Reviewer</label>
            <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo htmlspecialchars($menu['gelar'] . ' ' . $menu['nama_dokter']); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if ($menu['tanggal_persetujuan']): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Persetujuan</label>
            <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200"><?php echo date('d/m/Y H:i', strtotime($menu['tanggal_persetujuan'])); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if ($menu['pesan_dokter']): ?>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan dari Dokter</label>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 min-h-[80px]">
                <p class="text-gray-800 whitespace-pre-wrap"><?php echo htmlspecialchars($menu['pesan_dokter']); ?></p>
            </div>
        </div>
        <?php else: ?>
        <div class="md:col-span-2 hidden"></div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

