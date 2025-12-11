<?php
ob_start();

require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
$page_title = 'Tentukan Sekolah';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu_id = (int)($_POST['menu_id'] ?? 0);
    $sekolah_id = (int)($_POST['sekolah_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $porsi = (int)($_POST['porsi'] ?? 0);
    
    $menu = $conn->query("SELECT * FROM menu WHERE id = $menu_id AND status = 'disetujui'")->fetch_assoc();
    $sekolah = $conn->query("SELECT * FROM users WHERE id = $sekolah_id AND role = 'sekolah'")->fetch_assoc();
    
    if ($menu && $sekolah && $porsi > 0) {
        if ($sekolah['jumlah_siswa'] > $menu['porsi_maksimal']) {
            $error = "Jumlah siswa (" . $sekolah['jumlah_siswa'] . ") harus lebih kecil dari porsi maksimal menu (" . $menu['porsi_maksimal'] . ")";
        } elseif ($porsi > $menu['porsi_maksimal']) {
            $error = "Porsi ditentukan tidak boleh melebihi porsi maksimal menu";
        } else {
            $check = $conn->query("SELECT id FROM jadwal WHERE menu_id = $menu_id AND sekolah_id = $sekolah_id AND tanggal = '$tanggal'");
            if ($check->num_rows > 0) {
                $error = "Jadwal untuk sekolah ini pada tanggal tersebut sudah ada";
            } else {
                $pegawai_id = $_SESSION['user_id'];
                $stmt = $conn->prepare("INSERT INTO jadwal (menu_id, sekolah_id, tanggal, porsi_ditentukan, pegawai_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisii", $menu_id, $sekolah_id, $tanggal, $porsi, $pegawai_id);
                if ($stmt->execute()) {
                    $stmt->close();
                    $conn->close();
                    header('Location: tentukan_sekolah.php?success=' . urlencode('Sekolah berhasil ditentukan'));
                    exit();
                } else {
                    $error = "Gagal menentukan sekolah: " . $stmt->error;
                    $stmt->close();
                }
            }
        }
    } else {
        $error = "Data tidak valid";
    }
}

$menus = $conn->query("SELECT * FROM menu WHERE status = 'disetujui' ORDER BY tanggal_mulai DESC")->fetch_all(MYSQLI_ASSOC);

$sekolah_list = $conn->query("SELECT * FROM users WHERE role = 'sekolah' ORDER BY nama_sekolah")->fetch_all(MYSQLI_ASSOC);

$jadwal_list = $conn->query("
    SELECT j.*, m.jenis_makanan, m.jenis_minuman, s.nama_sekolah, s.jumlah_siswa
    FROM jadwal j
    JOIN menu m ON j.menu_id = m.id
    JOIN users s ON j.sekolah_id = s.id
    ORDER BY j.tanggal DESC, j.created_at DESC
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
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Tentukan Sekolah untuk Menu</h2>
    
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
    
    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6" onsubmit="return validateForm()">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Menu (Status: Disetujui) <span class="text-red-500">*</span>
            </label>
            <select name="menu_id" id="menu_id" 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" 
                    required onchange="loadMenuInfo()">
                <option value="">-- Pilih Menu --</option>
                <?php foreach ($menus as $menu): ?>
                    <option value="<?php echo $menu['id']; ?>" 
                            data-porsi="<?php echo $menu['porsi_maksimal']; ?>"
                            data-tanggal-mulai="<?php echo $menu['tanggal_mulai']; ?>"
                            data-tanggal-selesai="<?php echo $menu['tanggal_selesai']; ?>">
                        <?php echo htmlspecialchars($menu['jenis_makanan'] . ' - ' . date('d/m/Y', strtotime($menu['tanggal_mulai']))); ?>
                        (Porsi Max: <?php echo number_format($menu['porsi_maksimal']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Sekolah <span class="text-red-500">*</span>
            </label>
            <select name="sekolah_id" id="sekolah_id" 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" 
                    required onchange="loadSekolahInfo()">
                <option value="">-- Pilih Sekolah --</option>
                <?php foreach ($sekolah_list as $sekolah): ?>
                    <option value="<?php echo $sekolah['id']; ?>" data-siswa="<?php echo $sekolah['jumlah_siswa']; ?>">
                        <?php echo htmlspecialchars($sekolah['nama_sekolah'] . ' (' . number_format($sekolah['jumlah_siswa']) . ' siswa)'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Tanggal <span class="text-red-500">*</span>
            </label>
            <input type="date" name="tanggal" id="tanggal" 
                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" 
                   required min="<?php echo date('Y-m-d'); ?>">
            <p class="text-xs text-gray-500 mt-1" id="info-tanggal"></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Porsi Ditentukan <span class="text-red-500">*</span>
            </label>
            <input type="number" name="porsi" id="porsi" 
                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" 
                   required min="1" placeholder="Masukkan jumlah porsi">
            <p class="text-xs text-gray-500 mt-1" id="info-porsi"></p>
        </div>
        
        <div class="md:col-span-2">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 focus:ring-4 focus:ring-primary-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-primary-500/30">
                <i class="fas fa-check mr-2"></i>Tentukan Sekolah
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Daftar Jadwal</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold rounded-tl-lg">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Sekolah</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Jumlah Siswa</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Porsi Ditentukan</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold rounded-tr-lg">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($jadwal_list)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-6xl mb-4 block text-gray-300"></i>
                        <p class="text-lg">Tidak ada jadwal</p>
                        <p class="text-sm text-gray-400 mt-2">Silakan tentukan sekolah untuk menu yang sudah disetujui</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($jadwal_list as $jadwal): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-800 font-medium">
                        <?php echo date('d/m/Y', strtotime($jadwal['tanggal'])); ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <div class="flex flex-col">
                            <span class="font-medium"><?php echo htmlspecialchars($jadwal['jenis_makanan']); ?></span>
                            <span class="text-xs text-gray-500"><?php echo htmlspecialchars($jadwal['jenis_minuman']); ?></span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 font-medium">
                        <?php echo htmlspecialchars($jadwal['nama_sekolah']); ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?php echo number_format($jadwal['jumlah_siswa']); ?> siswa
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <?php echo number_format($jadwal['porsi_ditentukan']); ?> porsi
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="hapus_jadwal.php?id=<?php echo $jadwal['id']; ?>" 
                           onclick="return confirm('Yakin ingin menghapus jadwal ini?\n\nSekolah: <?php echo htmlspecialchars($jadwal['nama_sekolah']); ?>\nTanggal: <?php echo date('d/m/Y', strtotime($jadwal['tanggal'])); ?>')"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fas fa-trash"></i>
                            <span>Hapus</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
let currentMenuPorsi = 0;
let currentJumlahSiswa = 0;

function loadMenuInfo() {
    const select = document.getElementById('menu_id');
    const option = select.options[select.selectedIndex];
    const porsiMax = option.getAttribute('data-porsi');
    const tanggalMulai = option.getAttribute('data-tanggal-mulai');
    const tanggalSelesai = option.getAttribute('data-tanggal-selesai');
    
    if (porsiMax) {
        currentMenuPorsi = parseInt(porsiMax);
        const infoText = 'Porsi maksimal menu: ' + currentMenuPorsi.toLocaleString('id-ID');
        document.getElementById('info-porsi').textContent = infoText;
        document.getElementById('porsi').max = porsiMax;
        
        if (tanggalMulai && tanggalSelesai) {
            document.getElementById('tanggal').min = tanggalMulai;
            document.getElementById('tanggal').max = tanggalSelesai;
            document.getElementById('info-tanggal').textContent = 
                'Tanggal harus antara ' + formatDate(tanggalMulai) + ' - ' + formatDate(tanggalSelesai);
        }
        
        validatePorsi();
    } else {
        currentMenuPorsi = 0;
        document.getElementById('info-porsi').textContent = '';
        document.getElementById('info-tanggal').textContent = '';
        document.getElementById('tanggal').min = '<?php echo date('Y-m-d'); ?>';
        document.getElementById('tanggal').max = '';
    }
}

function loadSekolahInfo() {
    const select = document.getElementById('sekolah_id');
    const option = select.options[select.selectedIndex];
    const jumlahSiswa = option.getAttribute('data-siswa');
    
    if (jumlahSiswa) {
        currentJumlahSiswa = parseInt(jumlahSiswa);
        validatePorsi();
    } else {
        currentJumlahSiswa = 0;
    }
}

function validatePorsi() {
    const infoElement = document.getElementById('info-porsi');
    let infoText = '';
    
    if (currentMenuPorsi > 0) {
        infoText = 'Porsi maksimal menu: ' + currentMenuPorsi.toLocaleString('id-ID');
        
        if (currentJumlahSiswa > 0) {
            infoText += ' | Jumlah siswa: ' + currentJumlahSiswa.toLocaleString('id-ID');
            
            if (currentJumlahSiswa > currentMenuPorsi) {
                infoText += ' ❌ Jumlah siswa melebihi porsi maksimal!';
                infoElement.className = 'text-xs text-red-600 mt-1 font-medium';
            } else {
                infoText += ' ✓';
                infoElement.className = 'text-xs text-green-600 mt-1 font-medium';
            }
        } else {
            infoElement.className = 'text-xs text-gray-500 mt-1';
        }
    }
    
    infoElement.textContent = infoText;
}

function validateForm() {
    const porsi = parseInt(document.getElementById('porsi').value);
    const menuId = document.getElementById('menu_id').value;
    const sekolahId = document.getElementById('sekolah_id').value;
    const tanggal = document.getElementById('tanggal').value;
    
    if (!menuId || !sekolahId || !tanggal || !porsi) {
        alert('Semua field wajib diisi!');
        return false;
    }
    
    if (currentJumlahSiswa > currentMenuPorsi) {
        alert('Tidak dapat menentukan sekolah!\n\nJumlah siswa (' + currentJumlahSiswa.toLocaleString('id-ID') + ') melebihi porsi maksimal menu (' + currentMenuPorsi.toLocaleString('id-ID') + ')');
        return false;
    }
    
    if (porsi > currentMenuPorsi) {
        alert('Porsi ditentukan (' + porsi.toLocaleString('id-ID') + ') tidak boleh melebihi porsi maksimal menu (' + currentMenuPorsi.toLocaleString('id-ID') + ')');
        return false;
    }
    
    if (porsi < 1) {
        alert('Porsi ditentukan minimal 1');
        return false;
    }
    
    return confirm('Apakah Anda yakin ingin menentukan sekolah ini untuk menu pada tanggal ' + formatDate(tanggal) + '?');
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

document.addEventListener('DOMContentLoaded', function() {
    const porsiInput = document.getElementById('porsi');
    if (porsiInput) {
        porsiInput.addEventListener('input', function() {
            const porsi = parseInt(this.value);
            const infoElement = document.getElementById('info-porsi');
            
            if (porsi > currentMenuPorsi && currentMenuPorsi > 0) {
                infoElement.textContent = 'Porsi maksimal menu: ' + currentMenuPorsi.toLocaleString('id-ID') + ' ❌ Melebihi batas!';
                infoElement.className = 'text-xs text-red-600 mt-1 font-medium';
            } else {
                validatePorsi();
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

<?php ob_end_flush(); ?>