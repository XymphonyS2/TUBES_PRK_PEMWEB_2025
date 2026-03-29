<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
$page_title = 'Detail Keluhan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$from_page = $_GET['from'] ?? 'laporan_pegawai.php';

if (!$id) {
    header('Location: ' . $from_page);
    exit();
}

$conn = koneksiDatabase();
$keluhan = $conn->query("
    SELECT k.*, p.tanggal_pengiriman, p.porsi_dikirim, p.bukti_pengiriman, p.catatan as catatan_pengiriman,
           m.jenis_makanan, m.jenis_minuman, m.komposisi,
           s.nama_sekolah, s.alamat as alamat_sekolah, s.kontak as kontak_sekolah,
           v.nama_vendor, v.alamat as alamat_vendor, v.kontak as kontak_vendor
    FROM keluhan k
    JOIN pengiriman p ON k.pengiriman_id = p.id
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN menu m ON j.menu_id = m.id
    JOIN users s ON k.sekolah_id = s.id
    JOIN users v ON p.vendor_id = v.id
    WHERE k.id = $id
")->fetch_assoc();

$conn->close();

if (!$keluhan) {
    header('Location: ' . $from_page);
    exit();
}

// Mapping jenis keluhan
$jenis_keluhan_text = [
    'basi' => 'Basi',
    'berbau' => 'Berbau',
    'porsi_kurang' => 'Porsi Kurang',
    'komposisi_tidak_sesuai' => 'Komposisi Tidak Sesuai',
    'lain_lain' => 'Lain-lain'
];
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Detail Keluhan</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <!-- Informasi Sekolah -->
        <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-100">
            <h3 class="text-lg font-bold mb-4 text-primary-600 flex items-center gap-2">
                <i class="fas fa-school"></i>
                <span>Informasi Sekolah</span>
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Sekolah</label>
                    <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($keluhan['nama_sekolah']); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                    <p class="text-gray-800"><?php echo htmlspecialchars($keluhan['alamat_sekolah'] ?? '-'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Kontak</label>
                    <p class="text-gray-800"><?php echo htmlspecialchars($keluhan['kontak_sekolah'] ?? '-'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Informasi Vendor -->
        <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-100">
            <h3 class="text-lg font-bold mb-4 text-green-600 flex items-center gap-2">
                <i class="fas fa-store"></i>
                <span>Informasi Vendor</span>
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Vendor</label>
                    <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($keluhan['nama_vendor']); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                    <p class="text-gray-800"><?php echo htmlspecialchars($keluhan['alamat_vendor'] ?? '-'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Kontak</label>
                    <p class="text-gray-800"><?php echo htmlspecialchars($keluhan['kontak_vendor'] ?? '-'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Informasi Menu & Pengiriman -->
        <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-100">
            <h3 class="text-lg font-bold mb-4 text-purple-600 flex items-center gap-2">
                <i class="fas fa-utensils"></i>
                <span>Informasi Menu & Pengiriman</span>
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Menu</label>
                    <p class="text-gray-800 font-medium">
                        <?php echo htmlspecialchars($keluhan['jenis_makanan'] . ' + ' . $keluhan['jenis_minuman']); ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Komposisi</label>
                    <div class="bg-white p-3 rounded-xl border border-gray-200">
                        <p class="text-gray-800 whitespace-pre-wrap text-sm leading-relaxed">
                            <?php echo htmlspecialchars($keluhan['komposisi']); ?>
                        </p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Pengiriman</label>
                    <p class="text-gray-800"><?php echo date('d/m/Y', strtotime($keluhan['tanggal_pengiriman'])); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Porsi Dikirim</label>
                    <p class="text-gray-800"><?php echo number_format($keluhan['porsi_dikirim']); ?></p>
                </div>
                <?php 
                $bukti_files = [];
                if ($keluhan['bukti_pengiriman']) {
                    $decoded = json_decode($keluhan['bukti_pengiriman'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $bukti_files = $decoded;
                    } else {
                        $bukti_files = [$keluhan['bukti_pengiriman']];
                    }
                }
                ?>
                <?php if (!empty($bukti_files)): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Bukti Pengiriman</label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($bukti_files as $idx => $file): ?>
                            <a href="../uploads/bukti_pengiriman/<?php echo htmlspecialchars($file); ?>" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">
                                <i class="fas fa-image"></i>
                                <span>Foto <?php echo $idx + 1; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($keluhan['catatan_pengiriman']): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Catatan Pengiriman</label>
                    <div class="bg-white p-3 rounded-xl border border-gray-200">
                        <p class="text-gray-800 text-sm leading-relaxed">
                            <?php echo htmlspecialchars($keluhan['catatan_pengiriman']); ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Detail Keluhan -->
        <div class="col-span-1 md:col-span-2 bg-red-50 rounded-xl p-4 sm:p-6 border border-red-200">
            <h3 class="text-lg font-bold mb-4 text-red-600 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>Detail Keluhan</span>
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Keluhan</label>
                    <p class="text-gray-800"><?php echo date('d/m/Y H:i', strtotime($keluhan['created_at'])); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Keluhan</label>
                    <span class="inline-block px-4 py-2 bg-red-200 text-red-800 rounded-full font-semibold text-sm">
                        <?php echo $jenis_keluhan_text[$keluhan['jenis_keluhan']] ?? ucfirst($keluhan['jenis_keluhan']); ?>
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Keluhan</label>
                    <div class="bg-white rounded-xl p-4 border border-red-200 min-h-[80px]">
                        <?php if (!empty($keluhan['catatan'])): ?>
                            <p class="text-gray-800 whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($keluhan['catatan']); ?></p>
                        <?php else: ?>
                            <p class="text-gray-400 text-sm italic text-center py-4">Tidak ada catatan keluhan</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-6 flex gap-3">
        <a href="<?php echo htmlspecialchars($from_page); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-xl transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

