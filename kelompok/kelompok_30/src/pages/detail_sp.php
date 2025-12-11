<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai', 'vendor']);
require_once __DIR__ . '/../config/database.php';

$sp_id = (int)($_GET['id'] ?? 0);
if (!$sp_id) {
    header('Location: ' . ($_SESSION['role'] == 'vendor' ? 'daftar_sp.php' : 'berikan_sp.php'));
    exit();
}

$conn = koneksiDatabase();

$stmt = $conn->prepare("
    SELECT sp.*, 
           v.nama_vendor, v.email as vendor_email, v.alamat as vendor_alamat, v.kontak as vendor_kontak,
           p.nama_lengkap as nama_pegawai, p.email as pegawai_email
    FROM sp sp
    JOIN users v ON sp.vendor_id = v.id
    JOIN users p ON sp.pegawai_id = p.id
    WHERE sp.id = ?
");
$stmt->bind_param("i", $sp_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header('Location: ' . ($_SESSION['role'] == 'vendor' ? 'daftar_sp.php?error=' . urlencode('SP tidak ditemukan') : 'berikan_sp.php?error=' . urlencode('SP tidak ditemukan')));
    exit();
}

$sp = $result->fetch_assoc();
$stmt->close();
$conn->close();

$back_url = $_SESSION['role'] == 'vendor' ? 'daftar_sp.php' : 'berikan_sp.php';
if (isset($_GET['from'])) {
    $back_url = $_GET['from'];
}

$page_title = 'Detail SP';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$tingkat_map = [
    '1' => ['label' => 'SP 1 (Ringan)', 'color' => 'bg-yellow-100 text-yellow-800', 'badge_color' => 'bg-yellow-500'],
    '2' => ['label' => 'SP 2 (Sedang)', 'color' => 'bg-orange-100 text-orange-800', 'badge_color' => 'bg-orange-500'],
    '3' => ['label' => 'SP 3 (Pencabutan Izin)', 'color' => 'bg-red-100 text-red-800', 'badge_color' => 'bg-red-500']
];

$jenis_map = [
    'tidak_higienis' => 'Tidak Higienis dalam Mengelola Makanan',
    'kecurangan_pendataan' => 'Kecurangan dalam Pendataan (Markup Harga, dll)',
    'lainnya' => 'Lainnya'
];
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Detail Surat Peringatan (SP)</h2>
            <p class="text-sm text-gray-500">Informasi lengkap surat peringatan</p>
        </div>
        <a href="<?php echo htmlspecialchars($back_url); ?>" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="space-y-6">
        <!-- Badge Tingkat SP -->
        <div class="flex items-center gap-4">
            <span class="px-4 py-2 rounded-xl text-sm font-semibold <?php echo $tingkat_map[$sp['tingkat_sp']]['color']; ?>">
                <?php echo $tingkat_map[$sp['tingkat_sp']]['label']; ?>
            </span>
            <span class="text-sm text-gray-500">
                Diterbitkan pada: <?php echo date('d F Y, H:i', strtotime($sp['created_at'])); ?>
            </span>
        </div>

        <!-- Informasi Vendor -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-store text-primary-600"></i>
                Informasi Vendor
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Vendor</label>
                    <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200">
                        <?php echo htmlspecialchars($sp['nama_vendor']); ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200">
                        <?php echo htmlspecialchars($sp['vendor_email']); ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200 min-h-[60px]">
                        <?php echo !empty($sp['vendor_alamat']) ? htmlspecialchars($sp['vendor_alamat']) : '<span class="text-gray-400 italic text-sm">Tidak ada alamat</span>'; ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontak</label>
                    <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200">
                        <?php echo !empty($sp['vendor_kontak']) ? htmlspecialchars($sp['vendor_kontak']) : '<span class="text-gray-400 italic text-sm">Tidak ada kontak</span>'; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Informasi Pelanggaran -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
                Informasi Pelanggaran
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pelanggaran</label>
                    <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200">
                        <?php echo htmlspecialchars($jenis_map[$sp['jenis_pelanggaran']] ?? ucfirst($sp['jenis_pelanggaran'])); ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diberikan Oleh</label>
                    <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200">
                        <?php echo htmlspecialchars($sp['nama_pegawai']); ?>
                        <span class="text-gray-500 text-sm block mt-1"><?php echo htmlspecialchars($sp['pegawai_email']); ?></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Pesan SP -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-file-alt text-primary-600"></i>
                Pesan Surat Peringatan
            </h3>
            <div class="bg-white rounded-xl p-4 border border-gray-200 min-h-[120px]">
                <?php if (!empty($sp['pesan'])): ?>
                    <p class="text-gray-800 whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($sp['pesan']); ?></p>
                <?php else: ?>
                    <p class="text-gray-400 text-sm italic text-center py-4">Tidak ada pesan</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

