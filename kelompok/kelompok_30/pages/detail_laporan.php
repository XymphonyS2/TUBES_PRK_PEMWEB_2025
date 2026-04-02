<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
$page_title = 'Detail Laporan Menu';
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
$laporan = $conn->query("
    SELECT lm.*, 
           m.jenis_makanan, m.jenis_minuman, m.komposisi, m.tanggal_mulai, m.tanggal_selesai, 
           m.porsi_maksimal, m.status as status_menu,
           u.nama_vendor, u.alamat as alamat_vendor, u.kontak as kontak_vendor
    FROM laporan_menu lm
    JOIN menu m ON lm.menu_id = m.id
    JOIN users u ON lm.vendor_id = u.id
    WHERE lm.id = $id
")->fetch_assoc();

$conn->close();

if (!$laporan) {
    header('Location: ' . $from_page);
    exit();
}
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Detail Laporan Menu</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <!-- Informasi Vendor -->
        <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-100">
            <h3 class="text-lg font-bold mb-4 text-primary-600 flex items-center gap-2">
                <i class="fas fa-store"></i>
                <span>Informasi Vendor</span>
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Vendor</label>
                    <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($laporan['nama_vendor']); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                    <p class="text-gray-800"><?php echo htmlspecialchars($laporan['alamat_vendor'] ?? '-'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Kontak</label>
                    <p class="text-gray-800"><?php echo htmlspecialchars($laporan['kontak_vendor'] ?? '-'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Informasi Menu -->
        <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-100">
            <h3 class="text-lg font-bold mb-4 text-green-600 flex items-center gap-2">
                <i class="fas fa-utensils"></i>
                <span>Informasi Menu</span>
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Makanan</label>
                    <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($laporan['jenis_makanan']); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Minuman</label>
                    <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($laporan['jenis_minuman']); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Menu</label>
                    <p class="text-gray-800">
                        <?php 
                        echo date('d/m/Y', strtotime($laporan['tanggal_mulai'])); 
                        if ($laporan['tanggal_mulai'] != $laporan['tanggal_selesai']) {
                            echo ' - ' . date('d/m/Y', strtotime($laporan['tanggal_selesai']));
                        }
                        ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Porsi Maksimal</label>
                    <p class="text-gray-800"><?php echo number_format($laporan['porsi_maksimal']); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status Menu</label>
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
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?php echo $status_class[$laporan['status_menu']] ?? 'bg-gray-100 text-gray-800'; ?>">
                        <?php echo $status_text[$laporan['status_menu']] ?? ucfirst($laporan['status_menu']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Komposisi -->
        <div class="col-span-1 md:col-span-2 bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-100">
            <h3 class="text-lg font-bold mb-4 text-purple-600 flex items-center gap-2">
                <i class="fas fa-list"></i>
                <span>Komposisi Menu</span>
            </h3>
            <div class="bg-white p-4 rounded-xl border border-gray-200">
                <p class="text-gray-800 whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($laporan['komposisi']); ?></p>
            </div>
        </div>
        
        <!-- Laporan dari Vendor -->
        <div class="col-span-1 md:col-span-2 bg-primary-50 rounded-xl p-4 sm:p-6 border border-primary-100">
            <h3 class="text-lg font-bold mb-4 text-primary-600 flex items-center gap-2">
                <i class="fas fa-file-alt"></i>
                <span>Laporan dari Vendor</span>
            </h3>
            <div class="space-y-4 sm:space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Laporan</label>
                    <p class="text-gray-800 font-medium"><?php echo date('d/m/Y H:i', strtotime($laporan['created_at'])); ?></p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Struk Belanja</label>
                        <?php 
                        $struk_files = [];
                        if ($laporan['foto_struk_belanja']) {
                            $decoded = json_decode($laporan['foto_struk_belanja'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $struk_files = $decoded;
                            } else {
                                $struk_files = [$laporan['foto_struk_belanja']];
                            }
                        }
                        ?>
                        <?php if (!empty($struk_files)): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($struk_files as $idx => $file): ?>
                                    <a href="../uploads/foto_struk/<?php echo htmlspecialchars($file); ?>" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors">
                                        <i class="fas fa-image"></i>
                                        <span>Foto Struk <?php echo $idx + 1; ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400 text-sm">Tidak ada foto struk</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Proses Pembuatan</label>
                        <?php 
                        $proses_files = [];
                        if ($laporan['foto_proses_pembuatan']) {
                            $decoded = json_decode($laporan['foto_proses_pembuatan'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $proses_files = $decoded;
                            } else {
                                $proses_files = [$laporan['foto_proses_pembuatan']];
                            }
                        }
                        ?>
                        <?php if (!empty($proses_files)): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($proses_files as $idx => $file): ?>
                                    <a href="../uploads/foto_proses/<?php echo htmlspecialchars($file); ?>" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-colors">
                                        <i class="fas fa-image"></i>
                                        <span>Foto Proses <?php echo $idx + 1; ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400 text-sm">Tidak ada foto proses</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <div class="bg-white rounded-xl p-4 border border-gray-200 min-h-[80px]">
                        <?php if (!empty($laporan['keterangan'])): ?>
                            <p class="text-gray-800 whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($laporan['keterangan']); ?></p>
                        <?php else: ?>
                            <p class="text-gray-400 text-sm italic text-center py-4">Tidak ada keterangan tambahan</p>
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

