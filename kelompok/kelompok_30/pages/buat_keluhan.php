<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['sekolah']);
$page_title = 'Buat Keluhan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();
$sekolah_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pengiriman_id = (int)($_POST['pengiriman_id'] ?? 0);
    $jenis_keluhan = $_POST['jenis_keluhan'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    
    if ($pengiriman_id && $jenis_keluhan && $catatan) {
        $stmt = $conn->prepare("INSERT INTO keluhan (pengiriman_id, sekolah_id, jenis_keluhan, catatan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $pengiriman_id, $sekolah_id, $jenis_keluhan, $catatan);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: buat_keluhan.php?success=' . urlencode('Keluhan berhasil dikirim'));
            exit();
        } else {
            $error = "Gagal mengirim keluhan: " . $stmt->error;
            $stmt->close();
        }
    } else {
        $error = "Semua field wajib diisi";
    }
}

// Get pengiriman yang sudah diterima (bisa dikeluhkan)
$pengiriman = $conn->query("
    SELECT p.*, m.jenis_makanan, m.jenis_minuman, u.nama_vendor
    FROM pengiriman p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN menu m ON j.menu_id = m.id
    JOIN users u ON p.vendor_id = u.id
    WHERE p.sekolah_id = $sekolah_id AND p.status = 'diterima'
    ORDER BY p.tanggal_pengiriman DESC
    LIMIT 20
")->fetch_all(MYSQLI_ASSOC);

// Get keluhan saya
$keluhan_saya = $conn->query("
    SELECT k.*, p.tanggal_pengiriman, m.jenis_makanan
    FROM keluhan k
    JOIN pengiriman p ON k.pengiriman_id = p.id
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN menu m ON j.menu_id = m.id
    WHERE k.sekolah_id = $sekolah_id
    ORDER BY k.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();

// Get success/error from URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Buat Keluhan</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-gray-700 mb-2">Pilih Pengiriman yang Dikeluhkan *</label>
            <select name="pengiriman_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Pilih Pengiriman --</option>
                <?php foreach ($pengiriman as $p): ?>
                    <option value="<?php echo $p['id']; ?>">
                        <?php echo htmlspecialchars($p['jenis_makanan'] . ' + ' . $p['jenis_minuman'] . ' - ' . date('d/m/Y', strtotime($p['tanggal_pengiriman']))); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-gray-700 mb-2">Jenis Keluhan *</label>
            <select name="jenis_keluhan" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Pilih Jenis Keluhan --</option>
                <option value="basi">Basi</option>
                <option value="berbau">Berbau</option>
                <option value="porsi_kurang">Porsi Kurang</option>
                <option value="komposisi_tidak_sesuai">Komposisi Tidak Sesuai</option>
                <option value="lain_lain">Lain-lain</option>
            </select>
        </div>
        
        <div>
            <label class="block text-gray-700 mb-2">Catatan *</label>
            <textarea name="catatan" rows="5" class="w-full border rounded px-3 py-2" required placeholder="Tuliskan detail keluhan..."></textarea>
        </div>
        
        <div>
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                <i class="fas fa-paper-plane mr-2"></i>Kirim Keluhan
            </button>
        </div>
    </form>
</div>

<!-- Keluhan Saya -->
<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Keluhan yang Saya Buat</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal Pengiriman</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Jenis Keluhan</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Catatan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($keluhan_saya)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                            <p>Belum ada keluhan</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($keluhan_saya as $kel): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y H:i', strtotime($kel['created_at'])); ?></td>
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
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($kel['catatan']); ?></td>
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

