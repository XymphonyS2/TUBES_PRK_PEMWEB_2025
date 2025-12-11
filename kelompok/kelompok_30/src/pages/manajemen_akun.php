<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
$page_title = 'Manajemen Akun';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();


if (isset($_GET['hapus']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND id != ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Akun berhasil dihapus'); window.location.href='manajemen_akun.php';</script>";
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Manajemen Akun</h2>
    

    <div class="mb-6 border-b border-gray-200">
        <ul class="flex flex-wrap gap-1 -mb-px">
            <li>
                <button onclick="showTab('pegawai')" class="tab-btn inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 border-primary-600 text-primary-600 transition-colors" id="tab-pegawai">
                    <i class="fas fa-user-tie"></i>
                    <span>Buat Akun Pegawai</span>
                </button>
            </li>
            <li>
                <button onclick="showTab('dokter')" class="tab-btn inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors" id="tab-dokter">
                    <i class="fas fa-user-md"></i>
                    <span>Buat Akun Dokter</span>
                </button>
            </li>
            <li>
                <button onclick="showTab('vendor')" class="tab-btn inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors" id="tab-vendor">
                    <i class="fas fa-store"></i>
                    <span>Buat Akun Vendor</span>
                </button>
            </li>
            <li>
                <button onclick="showTab('sekolah')" class="tab-btn inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors" id="tab-sekolah">
                    <i class="fas fa-school"></i>
                    <span>Buat Akun Sekolah</span>
                </button>
            </li>
        </ul>
    </div>
    

    <div id="form-pegawai" class="tab-content">
        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span class="text-red-700 text-sm"><?php echo htmlspecialchars($_GET['error']); ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <span class="text-green-700 text-sm"><?php echo htmlspecialchars($_GET['success']); ?></span>
            </div>
        <?php endif; ?>
        <form action="../api/buat_akun.php" method="POST" class="space-y-5">
            <input type="hidden" name="role" value="pegawai">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_lengkap" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <input type="text" name="alamat" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kontak</label>
                    <input type="text" name="kontak" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white">
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 focus:ring-4 focus:ring-primary-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-primary-500/30">
                    <i class="fas fa-user-plus mr-2"></i>Buat Akun
                </button>
            </div>
        </form>
    </div>
    

    <div id="form-dokter" class="tab-content hidden">
        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span class="text-red-700 text-sm"><?php echo htmlspecialchars($_GET['error']); ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <span class="text-green-700 text-sm"><?php echo htmlspecialchars($_GET['success']); ?></span>
            </div>
        <?php endif; ?>
        <form action="../api/buat_akun.php" method="POST" class="space-y-5">
            <input type="hidden" name="role" value="dokter">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_lengkap" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gelar <span class="text-red-500">*</span></label>
                    <input type="text" name="gelar" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required placeholder="dr., Sp.GK, dll">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <input type="text" name="alamat" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kontak</label>
                    <input type="text" name="kontak" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white">
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 focus:ring-4 focus:ring-primary-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-primary-500/30">
                    <i class="fas fa-user-plus mr-2"></i>Buat Akun
                </button>
            </div>
        </form>
    </div>
    

    <div id="form-vendor" class="tab-content hidden">
        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span class="text-red-700 text-sm"><?php echo htmlspecialchars($_GET['error']); ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <span class="text-green-700 text-sm"><?php echo htmlspecialchars($_GET['success']); ?></span>
            </div>
        <?php endif; ?>
        <form action="../api/buat_akun.php" method="POST" class="space-y-5">
            <input type="hidden" name="role" value="vendor">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Vendor <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_vendor" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <input type="text" name="alamat" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kontak</label>
                    <input type="text" name="kontak" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white">
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 focus:ring-4 focus:ring-primary-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-primary-500/30">
                    <i class="fas fa-user-plus mr-2"></i>Buat Akun
                </button>
            </div>
        </form>
    </div>
    

    <div id="form-sekolah" class="tab-content hidden">
        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span class="text-red-700 text-sm"><?php echo htmlspecialchars($_GET['error']); ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <span class="text-green-700 text-sm"><?php echo htmlspecialchars($_GET['success']); ?></span>
            </div>
        <?php endif; ?>
        <form action="../api/buat_akun.php" method="POST" class="space-y-5">
            <input type="hidden" name="role" value="sekolah">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sekolah" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Siswa <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_siswa" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <input type="text" name="alamat" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kontak</label>
                    <input type="text" name="kontak" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white">
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 focus:ring-4 focus:ring-primary-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-primary-500/30">
                    <i class="fas fa-user-plus mr-2"></i>Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>


<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Daftar Semua Akun</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left font-semibold rounded-tl-xl">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold">Email</th>
                    <th class="px-4 py-3 text-left font-semibold">Role</th>
                    <th class="px-4 py-3 text-left font-semibold">Kontak</th>
                    <th class="px-4 py-3 text-left font-semibold rounded-tr-xl">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-800"><?php echo htmlspecialchars($user['nama_lengkap'] ?? $user['nama_sekolah'] ?? $user['nama_vendor'] ?? 'N/A'); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($user['kontak'] ?? '-'); ?></td>
                    <td class="px-4 py-3">
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="?hapus=1&id=<?php echo $user['id']; ?>" 
                               onclick="return confirm('Yakin ingin menghapus akun ini?')"
                               class="inline-flex items-center gap-2 px-3 py-1.5 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fas fa-trash"></i>
                                <span>Hapus</span>
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm italic">Akun Anda</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function showTab(tab) {

    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-primary-600', 'text-primary-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    

    document.getElementById('form-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-primary-600', 'text-primary-600');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

