<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['vendor']);
require_once __DIR__ . '/../config/database.php';

$vendor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = koneksiDatabase();
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $jenis_makanan = $_POST['jenis_makanan'] ?? '';
    $jenis_minuman = $_POST['jenis_minuman'] ?? '';
    $komposisi = $_POST['komposisi'] ?? '';
    $porsi_maksimal = (int)($_POST['porsi_maksimal'] ?? 0);
    
    if ($tanggal_mulai && $tanggal_selesai && $jenis_makanan && $jenis_minuman && $komposisi && $porsi_maksimal > 0) {
        if ($tanggal_mulai <= $tanggal_selesai) {
            $stmt = $conn->prepare("INSERT INTO menu (vendor_id, tanggal_mulai, tanggal_selesai, jenis_makanan, jenis_minuman, komposisi, porsi_maksimal, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("isssssi", $vendor_id, $tanggal_mulai, $tanggal_selesai, $jenis_makanan, $jenis_minuman, $komposisi, $porsi_maksimal);
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: buat_menu.php?success=' . urlencode('Menu berhasil dibuat dan dikirim ke Pemerintah untuk review'));
                exit();
            } else {
                $error = "Gagal membuat menu: " . $stmt->error;
                $stmt->close();
            }
        } else {
            $error = "Tanggal mulai tidak boleh lebih besar dari tanggal selesai";
        }
    } else {
        $error = "Semua field wajib diisi";
    }
    $conn->close();
}

$page_title = 'Buat Menu';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$conn = koneksiDatabase();

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

$my_menus = $conn->query("
    SELECT * FROM menu 
    WHERE vendor_id = $vendor_id 
    ORDER BY created_at DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Buat Menu Baru</h2>
    
    <?php if (isset($error)): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <span class="text-green-700 text-sm"><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai *</label>
                <input type="date" name="tanggal_mulai" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai *</label>
                <input type="date" name="tanggal_selesai" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required min="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Makanan *</label>
            <input type="text" name="jenis_makanan" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required placeholder="Contoh: Nasi, Ayam Goreng, Sayur Asem">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Minuman *</label>
            <input type="text" name="jenis_minuman" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required placeholder="Contoh: Es Teh, Jus Jeruk">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Komposisi *</label>
            <textarea name="komposisi" rows="5" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required placeholder="Detail bahan-bahan dan kandungan gizi..."></textarea>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Porsi Maksimal yang Bisa Dibuat *</label>
            <input type="number" name="porsi_maksimal" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required min="1" placeholder="Contoh: 500">
        </div>
        
        <div>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 focus:ring-4 focus:ring-primary-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-primary-500/30">
                <i class="fas fa-paper-plane mr-2"></i>Kirim ke Pemerintah
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Menu Saya (10 Terbaru)</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left font-semibold rounded-tl-xl">Tanggal</th>
                    <th class="px-4 py-3 text-left font-semibold">Jenis Makanan</th>
                    <th class="px-4 py-3 text-left font-semibold">Jenis Minuman</th>
                    <th class="px-4 py-3 text-left font-semibold">Porsi</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-left font-semibold rounded-tr-xl">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($my_menus as $menu): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-800">
                        <?php 
                        echo date('d/m/Y', strtotime($menu['tanggal_mulai'])); 
                        if ($menu['tanggal_mulai'] != $menu['tanggal_selesai']) {
                            echo ' - ' . date('d/m/Y', strtotime($menu['tanggal_selesai']));
                        }
                        ?>
                    </td>
                    <td class="px-4 py-3 text-gray-800"><?php echo htmlspecialchars($menu['jenis_makanan']); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($menu['jenis_minuman']); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo number_format($menu['porsi_maksimal']); ?></td>
                    <td class="px-4 py-3">
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
                    </td>
                    <td class="px-4 py-3">
                        <a href="detail_menu.php?id=<?php echo $menu['id']; ?>&from=buat_menu.php" class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-medium">
                            <i class="fas fa-eye"></i>
                            <span>Detail</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

