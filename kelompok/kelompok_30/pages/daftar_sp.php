<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['vendor']);
$page_title = 'Daftar SP';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();
$vendor_id = $_SESSION['user_id'];

// Get SP for this vendor
$sp_list = $conn->query("
    SELECT sp.*, p.nama_lengkap as nama_pegawai
    FROM sp sp
    JOIN users p ON sp.pegawai_id = p.id
    WHERE sp.vendor_id = $vendor_id
    ORDER BY sp.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Daftar SP (Surat Peringatan) Saya</h2>
    
    <?php if (empty($sp_list)): ?>
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Tidak ada SP yang diterima</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($sp_list as $sp): ?>
            <div class="border-2 rounded-xl p-6 card-hover <?php echo $sp['tingkat_sp'] == '3' ? 'border-red-300 bg-red-50' : ($sp['tingkat_sp'] == '2' ? 'border-orange-300 bg-orange-50' : 'border-yellow-300 bg-yellow-50'); ?>">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">
                            <?php
                            $tingkat = ['1' => 'SP 1 - Ringan', '2' => 'SP 2 - Sedang', '3' => 'SP 3 - Pencabutan Izin'];
                            echo $tingkat[$sp['tingkat_sp']];
                            ?>
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Diberikan oleh: <span class="font-medium"><?php echo htmlspecialchars($sp['nama_pegawai']); ?></span>
                            pada <span class="font-medium"><?php echo date('d/m/Y H:i', strtotime($sp['created_at'])); ?></span>
                        </p>
                    </div>
                    <span class="px-4 py-2 rounded-xl text-sm font-bold 
                        <?php echo $sp['tingkat_sp'] == '3' ? 'bg-red-600 text-white' : ($sp['tingkat_sp'] == '2' ? 'bg-orange-600 text-white' : 'bg-yellow-600 text-white'); ?>">
                        Tingkat <?php echo $sp['tingkat_sp']; ?>
                    </span>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pelanggaran</label>
                    <p class="text-gray-800 bg-white rounded-xl p-3 border border-gray-200">
                        <?php
                        $jenis = [
                            'tidak_higienis' => 'Tidak Higienis dalam Mengelola Makanan',
                            'kecurangan_pendataan' => 'Kecurangan dalam Pendataan',
                            'lainnya' => 'Lainnya'
                        ];
                        echo $jenis[$sp['jenis_pelanggaran']] ?? ucfirst($sp['jenis_pelanggaran']);
                        ?>
                    </p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pesan</label>
                    <p class="text-gray-800 bg-white rounded-xl p-4 border border-gray-200 whitespace-pre-wrap min-h-[80px]"><?php echo htmlspecialchars($sp['pesan']); ?></p>
                </div>
                
                <div class="flex justify-end">
                    <a href="detail_sp.php?id=<?php echo $sp['id']; ?>&from=daftar_sp.php" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors">
                        <i class="fas fa-eye"></i>
                        <span>Lihat Detail</span>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

