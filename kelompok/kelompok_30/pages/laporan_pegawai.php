<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
$page_title = 'Laporan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();

// Get laporan menu dari vendor
$laporan_menu = $conn->query("
    SELECT lm.*, m.jenis_makanan, m.jenis_minuman, u.nama_vendor
    FROM laporan_menu lm
    JOIN menu m ON lm.menu_id = m.id
    JOIN users u ON lm.vendor_id = u.id
    ORDER BY lm.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

// Get keluhan dari sekolah
$keluhan = $conn->query("
    SELECT k.*, p.tanggal_pengiriman, m.jenis_makanan, s.nama_sekolah
    FROM keluhan k
    JOIN pengiriman p ON k.pengiriman_id = p.id
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN menu m ON j.menu_id = m.id
    JOIN users s ON k.sekolah_id = s.id
    ORDER BY k.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

// Get pengiriman
$pengiriman = $conn->query("
    SELECT p.*, m.jenis_makanan, s.nama_sekolah, v.nama_vendor
    FROM pengiriman p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN menu m ON j.menu_id = m.id
    JOIN users s ON p.sekolah_id = s.id
    JOIN users v ON p.vendor_id = v.id
    ORDER BY p.tanggal_pengiriman DESC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="space-y-6">
    <!-- Laporan Menu dari Vendor -->
    <div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Laporan Menu dari Vendor</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Vendor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Foto Struk</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Foto Proses</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($laporan_menu)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                                <p>Belum ada laporan</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($laporan_menu as $lap): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y H:i', strtotime($lap['created_at'])); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($lap['nama_vendor']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($lap['jenis_makanan'] . ' + ' . $lap['jenis_minuman']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php 
                                $struk_files = [];
                                if ($lap['foto_struk_belanja']) {
                                    $decoded = json_decode($lap['foto_struk_belanja'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $struk_files = $decoded;
                                    } else {
                                        $struk_files = [$lap['foto_struk_belanja']];
                                    }
                                }
                                ?>
                                <?php if (!empty($struk_files)): ?>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($struk_files as $idx => $file): ?>
                                            <a href="../uploads/foto_struk/<?php echo htmlspecialchars($file); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-primary-100 text-primary-700 hover:bg-primary-200 rounded-lg text-xs transition-colors">
                                                <i class="fas fa-image"></i>
                                                <span>Foto <?php echo $idx + 1; ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php 
                                $proses_files = [];
                                if ($lap['foto_proses_pembuatan']) {
                                    $decoded = json_decode($lap['foto_proses_pembuatan'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $proses_files = $decoded;
                                    } else {
                                        $proses_files = [$lap['foto_proses_pembuatan']];
                                    }
                                }
                                ?>
                                <?php if (!empty($proses_files)): ?>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($proses_files as $idx => $file): ?>
                                            <a href="../uploads/foto_proses/<?php echo htmlspecialchars($file); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs transition-colors">
                                                <i class="fas fa-image"></i>
                                                <span>Foto <?php echo $idx + 1; ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <a href="detail_laporan.php?id=<?php echo $lap['id']; ?>&from=laporan_pegawai.php" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors text-sm">
                                    <i class="fas fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Keluhan dari Sekolah -->
    <div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Keluhan dari Sekolah</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Sekolah</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal Pengiriman</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Jenis Keluhan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($keluhan)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                                <p>Belum ada keluhan</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($keluhan as $kel): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y H:i', strtotime($kel['created_at'])); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($kel['nama_sekolah']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($kel['jenis_makanan']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($kel['tanggal_pengiriman'])); ?></td>
                            <td class="px-4 py-3">
                                <?php
                                $jenis = [
                                    'basi' => 'Basi',
                                    'berbau' => 'Berbau',
                                    'porsi_kurang' => 'Porsi Kurang',
                                    'komposisi_tidak_sesuai' => 'Komposisi Tidak Sesuai',
                                    'lain_lain' => 'Lain-lain'
                                ];
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <?php echo $jenis[$kel['jenis_keluhan']] ?? ucfirst($kel['jenis_keluhan']); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="detail_keluhan.php?id=<?php echo $kel['id']; ?>&from=laporan_pegawai.php" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors text-sm">
                                    <i class="fas fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Riwayat Pengiriman -->
    <div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Riwayat Pengiriman</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Vendor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Sekolah</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Porsi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($pengiriman)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                                <p>Belum ada pengiriman</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pengiriman as $p): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y', strtotime($p['tanggal_pengiriman'])); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($p['nama_vendor']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($p['nama_sekolah']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($p['jenis_makanan']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800"><?php echo number_format($p['porsi_dikirim']); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $p['status'] == 'diterima' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo ucfirst($p['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

