<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
$page_title = 'Daftar Menu';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();

$menus = $conn->query("
    SELECT m.*, u.nama_vendor, u.nama_lengkap as nama_vendor_lengkap
    FROM menu m
    LEFT JOIN users u ON m.vendor_id = u.id
    ORDER BY m.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Daftar Menu dari Vendor</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Vendor</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Jenis Makanan</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Jenis Minuman</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Porsi Maksimal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($menus)): ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        <p>Tidak ada menu</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($menus as $menu): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <?php 
                        echo date('d/m/Y', strtotime($menu['tanggal_mulai'])); 
                        if ($menu['tanggal_mulai'] != $menu['tanggal_selesai']) {
                            echo ' - ' . date('d/m/Y', strtotime($menu['tanggal_selesai']));
                        }
                        ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($menu['nama_vendor'] ?? $menu['nama_vendor_lengkap'] ?? 'N/A'); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($menu['jenis_makanan']); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($menu['jenis_minuman']); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo number_format($menu['porsi_maksimal']); ?></td>
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
                        <a href="detail_menu.php?id=<?php echo $menu['id']; ?>&from=daftar_menu_pegawai.php" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors text-sm">
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

