<?php
ob_start();

require_once __DIR__ . '/../config/auth.php';
cekRole(['vendor']);
$page_title = 'Kirim Makanan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();
$vendor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti_pengiriman'])) {
    $jadwal_id = (int)($_POST['jadwal_id'] ?? 0);
    $porsi_dikirim = (int)($_POST['porsi_dikirim'] ?? 0);
    $catatan = $_POST['catatan'] ?? '';
    
    $upload_dir = __DIR__ . '/../uploads/bukti_pengiriman/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $uploaded_files = [];
    
    if (isset($_FILES['bukti_pengiriman'])) {
        $files = $_FILES['bukti_pengiriman'];
        
        if (is_array($files['name'])) {
            $file_count = count($files['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] == 0) {
                    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    $file_type = mime_content_type($files['tmp_name'][$i]);
                    
                    if (in_array($file_type, $allowed_types)) {
                        if ($files['size'][$i] <= 5 * 1024 * 1024) {
                            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                            $file_name = 'bukti_' . time() . '_' . $vendor_id . '_' . $i . '_' . uniqid() . '.' . $ext;
                            if (move_uploaded_file($files['tmp_name'][$i], $upload_dir . $file_name)) {
                                $uploaded_files[] = $file_name;
                            }
                        } else {
                            $error = "File " . $files['name'][$i] . " terlalu besar (maksimal 5MB)";
                        }
                    } else {
                        $error = "File " . $files['name'][$i] . " bukan format gambar yang valid";
                    }
                }
            }
        } else {
            if ($files['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $file_type = mime_content_type($files['tmp_name']);
                
                if (in_array($file_type, $allowed_types)) {
                    if ($files['size'] <= 5 * 1024 * 1024) {
                        $ext = pathinfo($files['name'], PATHINFO_EXTENSION);
                        $file_name = 'bukti_' . time() . '_' . $vendor_id . '_' . uniqid() . '.' . $ext;
                        if (move_uploaded_file($files['tmp_name'], $upload_dir . $file_name)) {
                            $uploaded_files[] = $file_name;
                        }
                    } else {
                        $error = "File terlalu besar (maksimal 5MB)";
                    }
                } else {
                    $error = "File bukan format gambar yang valid";
                }
            }
        }
    }
    
    if (!isset($error) && empty($uploaded_files)) {
        $error = "Bukti pengiriman wajib diupload";
    }
    
    if (!isset($error)) {
        $bukti_pengiriman_json = json_encode($uploaded_files);
        
        $jadwal = $conn->query("SELECT * FROM jadwal WHERE id = $jadwal_id")->fetch_assoc();
        if ($jadwal && $porsi_dikirim > 0) {
            if ($porsi_dikirim > $jadwal['porsi_ditentukan']) {
                $error = "Porsi dikirim tidak boleh melebihi porsi yang ditentukan";
            } else {
                $tanggal_pengiriman = date('Y-m-d');
                $sekolah_id = $jadwal['sekolah_id'];
                
                $check = $conn->query("SELECT id FROM pengiriman WHERE jadwal_id = $jadwal_id AND tanggal_pengiriman = '$tanggal_pengiriman'");
                if ($check->num_rows > 0) {
                    $error = "Pengiriman untuk jadwal ini sudah dilakukan hari ini";
                } else {
                    $stmt = $conn->prepare("INSERT INTO pengiriman (jadwal_id, vendor_id, sekolah_id, tanggal_pengiriman, porsi_dikirim, bukti_pengiriman, catatan, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'dikirim')");
                    $stmt->bind_param("iiisiss", $jadwal_id, $vendor_id, $sekolah_id, $tanggal_pengiriman, $porsi_dikirim, $bukti_pengiriman_json, $catatan);
                    if ($stmt->execute()) {
                        $stmt->close();
                        $conn->close();
                        header('Location: kirim_makanan.php?success=' . urlencode('Makanan berhasil dikirim'));
                        exit();
                    } else {
                        $error = "Gagal mengirim makanan: " . $stmt->error;
                        $stmt->close();
                    }
                }
            }
        } else {
            $error = "Data tidak valid";
        }
    }
}

$jadwal_list = $conn->query("
    SELECT j.*, m.jenis_makanan, m.jenis_minuman, s.nama_sekolah, s.jumlah_siswa,
           CASE WHEN p.id IS NULL THEN 0 ELSE 1 END as sudah_dikirim
    FROM jadwal j
    JOIN menu m ON j.menu_id = m.id
    JOIN users s ON j.sekolah_id = s.id
    LEFT JOIN pengiriman p ON j.id = p.jadwal_id AND p.tanggal_pengiriman = CURDATE()
    WHERE m.vendor_id = $vendor_id AND m.status = 'disetujui'
    ORDER BY j.tanggal ASC
")->fetch_all(MYSQLI_ASSOC);

$pengiriman_history = $conn->query("
    SELECT p.*, m.jenis_makanan, s.nama_sekolah
    FROM pengiriman p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN menu m ON j.menu_id = m.id
    JOIN users s ON p.sekolah_id = s.id
    WHERE p.vendor_id = $vendor_id
    ORDER BY p.tanggal_pengiriman DESC
    LIMIT 20
")->fetch_all(MYSQLI_ASSOC);

$conn->close();

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Kirim Makanan ke Sekolah</h2>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500 flex-shrink-0"></i>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>
    
    <div class="space-y-6">
        <?php 
        $has_jadwal = false;
        foreach ($jadwal_list as $jadwal): 
            if ($jadwal['sudah_dikirim'] == 0 && $jadwal['tanggal'] <= date('Y-m-d')): 
                $has_jadwal = true;
        ?>
            <div class="bg-white rounded-xl p-6 card-shadow card-hover border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-primary-600"></i>Tanggal
                        </label>
                        <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200 font-medium">
                            <?php echo date('d/m/Y', strtotime($jadwal['tanggal'])); ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-school mr-2 text-primary-600"></i>Sekolah
                        </label>
                        <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200 font-medium">
                            <?php echo htmlspecialchars($jadwal['nama_sekolah']); ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-utensils mr-2 text-primary-600"></i>Menu
                        </label>
                        <p class="text-gray-800 bg-gray-50 rounded-xl p-3 border border-gray-200">
                            <?php echo htmlspecialchars($jadwal['jenis_makanan'] . ' + ' . $jadwal['jenis_minuman']); ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-box mr-2 text-primary-600"></i>Porsi Ditentukan
                        </label>
                        <p class="text-gray-800 bg-blue-50 rounded-xl p-3 border border-blue-200 font-bold text-blue-800">
                            <?php echo number_format($jadwal['porsi_ditentukan']); ?> porsi
                        </p>
                    </div>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="border-t border-gray-200 pt-6" onsubmit="return validateForm(this)">
                    <input type="hidden" name="jadwal_id" value="<?php echo $jadwal['id']; ?>">
                    <input type="hidden" id="max_porsi_<?php echo $jadwal['id']; ?>" value="<?php echo $jadwal['porsi_ditentukan']; ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Porsi Dikirim <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="porsi_dikirim" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" 
                                   required min="1" max="<?php echo $jadwal['porsi_ditentukan']; ?>"
                                   placeholder="Masukkan jumlah porsi">
                            <p class="text-xs text-gray-500 mt-1">Maksimal: <?php echo number_format($jadwal['porsi_ditentukan']); ?> porsi</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Bukti Pengiriman (Foto) <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="bukti_pengiriman[]" accept="image/*" multiple 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" 
                                   required onchange="previewImages(this)">
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Tekan Ctrl/Cmd untuk memilih beberapa foto. Maks 5MB per foto.
                            </p>
                            <div id="preview_<?php echo $jadwal['id']; ?>" class="mt-3 grid grid-cols-3 gap-2"></div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Pengiriman
                        </label>
                        <textarea name="catatan" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" 
                                  placeholder="Tambahkan catatan jika ada (opsional)..."></textarea>
                    </div>
                    
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-600 focus:ring-4 focus:ring-blue-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-blue-500/30">
                        <i class="fas fa-truck mr-2"></i>Kirim Sekarang
                    </button>
                </form>
            </div>
        <?php 
            endif; 
        endforeach; 
        ?>
    </div>
    
    <?php if (!$has_jadwal): ?>
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">Tidak ada jadwal pengiriman hari ini</p>
            <p class="text-gray-400 text-sm">Jadwal pengiriman akan muncul di sini saat waktunya tiba</p>
        </div>
    <?php endif; ?>
</div>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Riwayat Pengiriman</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold rounded-tl-lg">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Sekolah</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Porsi</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Bukti</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold rounded-tr-lg">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($pengiriman_history)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-6xl mb-4 block text-gray-300"></i>
                        <p class="text-lg">Belum ada riwayat pengiriman</p>
                        <p class="text-sm text-gray-400 mt-2">Riwayat pengiriman Anda akan muncul di sini</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($pengiriman_history as $p): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-800 font-medium">
                        <?php echo date('d/m/Y', strtotime($p['tanggal_pengiriman'])); ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 font-medium">
                        <?php echo htmlspecialchars($p['nama_sekolah']); ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <?php echo htmlspecialchars($p['jenis_makanan']); ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <?php echo number_format($p['porsi_dikirim']); ?> porsi
                        </span>
                    </td>
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
                                    <a href="../uploads/bukti_pengiriman/<?php echo htmlspecialchars($file); ?>" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-1 px-3 py-1 bg-primary-100 text-primary-700 hover:bg-primary-200 rounded-lg text-xs transition-colors">
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
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                            <?php 
                                if ($p['status'] == 'diterima') {
                                    echo 'bg-green-100 text-green-800';
                                } elseif ($p['status'] == 'ditolak') {
                                    echo 'bg-red-100 text-red-800';
                                } else {
                                    echo 'bg-yellow-100 text-yellow-800';
                                }
                            ?>">
                            <i class="fas fa-<?php 
                                if ($p['status'] == 'diterima') echo 'check-circle';
                                elseif ($p['status'] == 'ditolak') echo 'times-circle';
                                else echo 'clock';
                            ?> mr-1"></i>
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

<script>
function validateForm(form) {
    const porsiInput = form.querySelector('input[name="porsi_dikirim"]');
    const fileInput = form.querySelector('input[name="bukti_pengiriman[]"]');
    const porsi = parseInt(porsiInput.value);
    const maxPorsi = parseInt(porsiInput.max);
    
    if (!porsi || porsi < 1) {
        alert('Porsi dikirim harus diisi minimal 1');
        porsiInput.focus();
        return false;
    }
    
    if (porsi > maxPorsi) {
        alert('Porsi dikirim (' + porsi.toLocaleString('id-ID') + ') tidak boleh melebihi porsi yang ditentukan (' + maxPorsi.toLocaleString('id-ID') + ')');
        porsiInput.focus();
        return false;
    }
    
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Bukti pengiriman (foto) wajib diupload!');
        fileInput.focus();
        return false;
    }
    
    for (let i = 0; i < fileInput.files.length; i++) {
        const file = fileInput.files[i];
        const maxSize = 5 * 1024 * 1024; 
        
        if (file.size > maxSize) {
            alert('File "' + file.name + '" terlalu besar!\nMaksimal ukuran file: 5MB\nUkuran file Anda: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB');
            fileInput.focus();
            return false;
        }
        
        if (!file.type.startsWith('image/')) {
            alert('File "' + file.name + '" bukan format gambar yang valid!');
            fileInput.focus();
            return false;
        }
    }
    
    return confirm('Apakah Anda yakin ingin mengirim makanan?\n\nPorsi: ' + porsi.toLocaleString('id-ID') + '\nBukti: ' + fileInput.files.length + ' foto');
}

function previewImages(input) {
    const jadwalId = input.form.querySelector('input[name="jadwal_id"]').value;
    const preview = document.getElementById('preview_' + jadwalId);
    preview.innerHTML = '';
    
    if (input.files) {
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border-2 border-gray-200" alt="Preview ${i + 1}">
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-medium">Foto ${i + 1}</span>
                        </div>
                    `;
                    preview.appendChild(div);
                }
                
                reader.readAsDataURL(file);
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

<?php ob_end_flush();?>