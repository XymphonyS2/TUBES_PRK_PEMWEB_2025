<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['sekolah']);
$page_title = 'Jadwal Menu';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();
$sekolah_id = $_SESSION['user_id'];

$jadwal = $conn->query("
    SELECT j.*, m.jenis_makanan, m.jenis_minuman, m.komposisi, u.nama_vendor
    FROM jadwal j
    JOIN menu m ON j.menu_id = m.id
    JOIN users u ON m.vendor_id = u.id
    WHERE j.sekolah_id = $sekolah_id AND j.tanggal >= CURDATE()
    ORDER BY j.tanggal ASC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Jadwal Menu Sekolah</h2>
    
    <?php if (empty($jadwal)): ?>
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Tidak ada jadwal menu</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Vendor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Jenis Makanan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Jenis Minuman</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Porsi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($jadwal as $j): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-800">
                            <?php echo date('d/m/Y', strtotime($j['tanggal'])); ?>
                            <?php if ($j['tanggal'] == date('Y-m-d')): ?>
                                <span class="ml-2 inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Hari Ini</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($j['nama_vendor']); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($j['jenis_makanan']); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($j['jenis_minuman']); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo number_format($j['porsi_ditentukan']); ?></td>
                        <td class="px-4 py-3">
                            <a href="detail_jadwal.php?id=<?php echo $j['id']; ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors text-sm">
                                <i class="fas fa-eye"></i>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

