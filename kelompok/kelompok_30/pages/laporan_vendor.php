<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['vendor']);
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();
$vendor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES)) {
    $menu_id = (int)($_POST['menu_id'] ?? 0);
    $keterangan = $_POST['keterangan'] ?? '';
    
    $upload_dir_struk = __DIR__ . '/../uploads/foto_struk/';
    $upload_dir_proses = __DIR__ . '/../uploads/foto_proses/';
    
    if (!is_dir($upload_dir_struk)) mkdir($upload_dir_struk, 0777, true);
    if (!is_dir($upload_dir_proses)) mkdir($upload_dir_proses, 0777, true);
    
    $foto_struk_files = [];
    $foto_proses_files = [];
    
    if (isset($_FILES['foto_struk_belanja'])) {
        $files = $_FILES['foto_struk_belanja'];
        if (is_array($files['name'])) {
            $file_count = count($files['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] == 0) {
                    $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    $file_name = 'struk_' . time() . '_' . $vendor_id . '_' . $i . '.' . $ext;
                    if (move_uploaded_file($files['tmp_name'][$i], $upload_dir_struk . $file_name)) {
                        $foto_struk_files[] = $file_name;
                    }
                }
            }
        } else {
            if ($files['error'] == 0) {
                $ext = pathinfo($files['name'], PATHINFO_EXTENSION);
                $file_name = 'struk_' . time() . '_' . $vendor_id . '.' . $ext;
                if (move_uploaded_file($files['tmp_name'], $upload_dir_struk . $file_name)) {
                    $foto_struk_files[] = $file_name;
                }
            }
        }
    }
    
    if (isset($_FILES['foto_proses_pembuatan'])) {
        $files = $_FILES['foto_proses_pembuatan'];
        if (is_array($files['name'])) {
            $file_count = count($files['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] == 0) {
                    $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    $file_name = 'proses_' . time() . '_' . $vendor_id . '_' . $i . '.' . $ext;
                    if (move_uploaded_file($files['tmp_name'][$i], $upload_dir_proses . $file_name)) {
                        $foto_proses_files[] = $file_name;
                    }
                }
            }
        } else {
            if ($files['error'] == 0) {
                $ext = pathinfo($files['name'], PATHINFO_EXTENSION);
                $file_name = 'proses_' . time() . '_' . $vendor_id . '.' . $ext;
                if (move_uploaded_file($files['tmp_name'], $upload_dir_proses . $file_name)) {
                    $foto_proses_files[] = $file_name;
                }
            }
        }
    }
    
    $foto_struk_json = !empty($foto_struk_files) ? json_encode($foto_struk_files) : null;
    $foto_proses_json = !empty($foto_proses_files) ? json_encode($foto_proses_files) : null;
    
    if ($menu_id) {
        $stmt = $conn->prepare("INSERT INTO laporan_menu (menu_id, vendor_id, foto_struk_belanja, foto_proses_pembuatan, keterangan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $menu_id, $vendor_id, $foto_struk_json, $foto_proses_json, $keterangan);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: laporan_vendor.php?success=' . urlencode('Laporan berhasil disimpan'));
            exit();
        } else {
            $error = "Gagal menyimpan laporan: " . $stmt->error;
            $stmt->close();
        }
    }
}

$my_menus = $conn->query("
    SELECT * FROM menu 
    WHERE vendor_id = $vendor_id AND status = 'disetujui'
    ORDER BY created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$reports = $conn->query("
    SELECT lm.*, m.jenis_makanan, m.jenis_minuman
    FROM laporan_menu lm
    JOIN menu m ON lm.menu_id = m.id
    WHERE lm.vendor_id = $vendor_id
    ORDER BY lm.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();

$page_title = 'Laporan Menu';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Buat Laporan Menu</h2>
    
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
    
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-gray-700 mb-2">Pilih Menu *</label>
            <select name="menu_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Pilih Menu --</option>
                <?php foreach ($my_menus as $menu): ?>
                    <option value="<?php echo $menu['id']; ?>">
                        <?php echo htmlspecialchars($menu['jenis_makanan'] . ' + ' . $menu['jenis_minuman'] . ' (' . date('d/m/Y', strtotime($menu['tanggal_mulai'])) . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 mb-2">Foto Struk Belanja <span class="text-sm text-gray-500">(Bisa pilih beberapa)</span></label>
                <input type="file" name="foto_struk_belanja[]" accept="image/*" multiple class="w-full border rounded px-3 py-2">
                <p class="text-sm text-gray-500 mt-1">Tekan Ctrl/Cmd untuk memilih beberapa file</p>
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Foto Proses Pembuatan <span class="text-sm text-gray-500">(Bisa pilih beberapa)</span></label>
                <input type="file" name="foto_proses_pembuatan[]" accept="image/*" multiple class="w-full border rounded px-3 py-2">
                <p class="text-sm text-gray-500 mt-1">Tekan Ctrl/Cmd untuk memilih beberapa file</p>
            </div>
        </div>
        
        <div>
            <label class="block text-gray-700 mb-2">Keterangan</label>
            <textarea name="keterangan" rows="4" class="w-full border rounded px-3 py-2" placeholder="Tuliskan keterangan tambahan..."></textarea>
        </div>
        
        <div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Simpan Laporan
            </button>
        </div>
    </form>
</div>

<!-- Daftar Laporan -->
<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Laporan Saya</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Foto Struk</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Foto Proses</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Keterangan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                            <p>Belum ada laporan</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reports as $report): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y H:i', strtotime($report['created_at'])); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                            <?php echo htmlspecialchars($report['jenis_makanan'] . ' + ' . $report['jenis_minuman']); ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php 
                            $struk_files = [];
                            if ($report['foto_struk_belanja']) {
                                $decoded = json_decode($report['foto_struk_belanja'], true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $struk_files = $decoded;
                                } else {
                                    $struk_files = [$report['foto_struk_belanja']];
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
                            if ($report['foto_proses_pembuatan']) {
                                $decoded = json_decode($report['foto_proses_pembuatan'], true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $proses_files = $decoded;
                                } else {
                                    $proses_files = [$report['foto_proses_pembuatan']];
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
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($report['keterangan'] ?: '-'); ?></td>
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