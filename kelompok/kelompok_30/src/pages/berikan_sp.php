<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendor_id = (int)($_POST['vendor_id'] ?? 0);
    $tingkat_sp = $_POST['tingkat_sp'] ?? '';
    $jenis_pelanggaran = $_POST['jenis_pelanggaran'] ?? '';
    $pesan = $_POST['pesan'] ?? '';
    
    if ($vendor_id && $tingkat_sp && $jenis_pelanggaran && $pesan) {
        $pegawai_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO sp (vendor_id, pegawai_id, tingkat_sp, jenis_pelanggaran, pesan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $vendor_id, $pegawai_id, $tingkat_sp, $jenis_pelanggaran, $pesan);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: berikan_sp.php?success=' . urlencode('SP berhasil diberikan kepada Vendor'));
            exit();
        } else {
            $error = "Gagal memberikan SP: " . $stmt->error;
            $stmt->close();
        }
    } else {
        $error = "Semua field wajib diisi";
    }
}

$vendors = $conn->query("SELECT * FROM users WHERE role = 'vendor' ORDER BY nama_vendor")->fetch_all(MYSQLI_ASSOC);

$sp_list = $conn->query("
    SELECT sp.*, v.nama_vendor, p.nama_lengkap as nama_pegawai
    FROM sp sp
    JOIN users v ON sp.vendor_id = v.id
    JOIN users p ON sp.pegawai_id = p.id
    ORDER BY sp.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();

$page_title = 'Berikan SP';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Berikan SP (Surat Peringatan)</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
            <span><?php echo htmlspecialchars($_GET['error']); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500 flex-shrink-0"></i>
            <span><?php echo htmlspecialchars($_GET['success']); ?></span>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Vendor *</label>
            <select name="vendor_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                <option value="">-- Pilih Vendor --</option>
                <?php foreach ($vendors as $vendor): ?>
                    <option value="<?php echo $vendor['id']; ?>">
                        <?php echo htmlspecialchars($vendor['nama_vendor'] ?? $vendor['nama_lengkap']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat SP *</label>
            <select name="tingkat_sp" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                <option value="">-- Pilih Tingkat SP --</option>
                <option value="1">SP 1 - Ringan</option>
                <option value="2">SP 2 - Sedang</option>
                <option value="3">SP 3 - Pencabutan Izin</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pelanggaran *</label>
            <select name="jenis_pelanggaran" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                <option value="">-- Pilih Jenis Pelanggaran --</option>
                <option value="tidak_higienis">Tidak Higienis dalam Mengelola Makanan</option>
                <option value="kecurangan_pendataan">Kecurangan dalam Pendataan (markup harga, dll)</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan *</label>
            <textarea name="pesan" rows="5" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required placeholder="Tuliskan detail pelanggaran dan peringatan..."></textarea>
        </div>
        
        <div>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-500 text-white font-semibold rounded-xl hover:from-red-700 hover:to-red-600 focus:ring-4 focus:ring-red-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-red-500/30">
                <i class="fas fa-exclamation-triangle mr-2"></i>Berikan SP
            </button>
        </div>
    </form>
</div>

<!-- Daftar SP -->
<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Riwayat SP yang Diberikan</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-700 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Vendor</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tingkat SP</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Jenis Pelanggaran</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Diberikan Oleh</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sp_list)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        <p>Tidak ada riwayat SP</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($sp_list as $sp): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y H:i', strtotime($sp['created_at'])); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($sp['nama_vendor']); ?></td>
                    <td class="px-4 py-3">
                        <?php
                        $tingkat = ['1' => 'SP 1 (Ringan)', '2' => 'SP 2 (Sedang)', '3' => 'SP 3 (Pencabutan Izin)'];
                        $color = ['1' => 'bg-yellow-100 text-yellow-800', '2' => 'bg-orange-100 text-orange-800', '3' => 'bg-red-100 text-red-800'];
                        ?>
                        <span class="px-3 py-1 rounded-lg text-xs font-medium <?php echo $color[$sp['tingkat_sp']]; ?>">
                            <?php echo $tingkat[$sp['tingkat_sp']]; ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <?php
                        $jenis = [
                            'tidak_higienis' => 'Tidak Higienis',
                            'kecurangan_pendataan' => 'Kecurangan Pendataan',
                            'lainnya' => 'Lainnya'
                        ];
                        echo $jenis[$sp['jenis_pelanggaran']] ?? ucfirst($sp['jenis_pelanggaran']);
                        ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($sp['nama_pegawai']); ?></td>
                    <td class="px-4 py-3">
                        <a href="detail_sp.php?id=<?php echo $sp['id']; ?>&from=berikan_sp.php" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors text-sm">
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>