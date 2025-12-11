<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['sekolah']);
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();
$sekolah_id = $_SESSION['user_id'];

if (isset($_GET['terima']) && isset($_GET['id'])) {
    $pengiriman_id = (int)$_GET['id'];
    $tanggal_diterima = date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("UPDATE pengiriman SET status = 'diterima', tanggal_diterima = ? WHERE id = ? AND sekolah_id = ?");
    $stmt->bind_param("sii", $tanggal_diterima, $pengiriman_id, $sekolah_id);
    $stmt->execute();
    $stmt->close();
    
    header('Location: terima_makanan.php?success=Makanan berhasil diterima');
    exit();
}

$pengiriman = $conn->query("
    SELECT p.*, m.jenis_makanan, m.jenis_minuman, u.nama_vendor
    FROM pengiriman p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN menu m ON j.menu_id = m.id
    JOIN users u ON p.vendor_id = u.id
    WHERE p.sekolah_id = $sekolah_id AND p.status = 'dikirim'
    ORDER BY p.tanggal_pengiriman DESC
")->fetch_all(MYSQLI_ASSOC);

$pengiriman_diterima = $conn->query("
    SELECT p.*, m.jenis_makanan, m.jenis_minuman, u.nama_vendor
    FROM pengiriman p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN menu m ON j.menu_id = m.id
    JOIN users u ON p.vendor_id = u.id
    WHERE p.sekolah_id = $sekolah_id AND p.status = 'diterima'
    ORDER BY p.tanggal_pengiriman DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

$conn->close();

$page_title = 'Terima Makanan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Makanan yang Perlu Diterima</h2>
    
    <?php if (empty($pengiriman)): ?>
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Tidak ada makanan yang perlu diterima</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal Pengiriman</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Vendor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Porsi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Bukti Pengiriman</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pengiriman as $p): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y', strtotime($p['tanggal_pengiriman'])); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($p['nama_vendor']); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                            <?php echo htmlspecialchars($p['jenis_makanan'] . ' + ' . $p['jenis_minuman']); ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo number_format($p['porsi_dikirim']); ?></td>
                        <td class="px-4 py-3 text-sm">
                            <?php 
                            $bukti_files = [];
                            if ($p['bukti_pengiriman']) {
                                $decoded = json_decode($p['bukti_pengiriman'], true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $bukti_files = $decoded;
                                } else {
                                    $bukti_files = [$p['bukti_pengiriman']];
                                }
                            }
                            ?>
                            <?php if (!empty($bukti_files)): ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($bukti_files as $idx => $file): ?>
                                        <a href="../uploads/bukti_pengiriman/<?php echo htmlspecialchars($file); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-primary-100 text-primary-700 hover:bg-primary-200 rounded-lg text-xs transition-colors">
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
                            <a href="?terima=1&id=<?php echo $p['id']; ?>" 
                               onclick="return confirm('Konfirmasi terima makanan ini?')"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-colors text-sm">
                                <i class="fas fa-check"></i>
                                <span>Terima</span>
                            </a>
                        </td>
                    </tr>
                    <?php if ($p['catatan']): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-sm text-gray-600 bg-gray-50">
                            <strong>Catatan:</strong> <?php echo htmlspecialchars($p['catatan']); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Riwayat Makanan yang Sudah Diterima -->
<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Riwayat Makanan yang Diterima</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Vendor</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Porsi</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal Diterima</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($pengiriman_diterima)): ?>
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        <p>Belum ada makanan yang diterima</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($pengiriman_diterima as $p): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y', strtotime($p['tanggal_pengiriman'])); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($p['nama_vendor']); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <?php echo htmlspecialchars($p['jenis_makanan'] . ' + ' . $p['jenis_minuman']); ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo number_format($p['porsi_dikirim']); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?php echo $p['tanggal_diterima'] ? date('d/m/Y H:i', strtotime($p['tanggal_diterima'])) : '-'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>