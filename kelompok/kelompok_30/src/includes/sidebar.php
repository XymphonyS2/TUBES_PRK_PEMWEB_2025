<?php
$current_role = $_SESSION['role'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="flex h-screen overflow-hidden">
    <aside class="fixed inset-y-0 left-0 z-50 sidebar-gradient text-white flex flex-col flex-shrink-0 w-64">
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-utensils text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg">Sistem Makanan</h1>
                    <p class="text-xs text-white/60">Sekolah</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 py-6">
            <ul class="space-y-1 px-3">
            <?php if ($current_role == 'pegawai'): ?>
                <li>
                    <a href="dashboard_pegawai.php" class="nav-item <?php echo $current_page == 'dashboard_pegawai.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg">
                        <i class="fas fa-home w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="manajemen_akun.php" class="nav-item <?php echo $current_page == 'manajemen_akun.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-users w-5"></i>
                        <span class="font-medium">Manajemen Akun</span>
                    </a>
                </li>
                <li>
                    <a href="daftar_menu_pegawai.php" class="nav-item <?php echo $current_page == 'daftar_menu_pegawai.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-utensils w-5"></i>
                        <span class="font-medium">Daftar Menu</span>
                    </a>
                </li>
                <li>
                    <a href="tentukan_sekolah.php" class="nav-item <?php echo $current_page == 'tentukan_sekolah.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-school w-5"></i>
                        <span class="font-medium">Tentukan Sekolah</span>
                    </a>
                </li>
                <li>
                    <a href="laporan_pegawai.php" class="nav-item <?php echo $current_page == 'laporan_pegawai.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-file-alt w-5"></i>
                        <span class="font-medium">Laporan</span>
                    </a>
                </li>
                <li>
                    <a href="berikan_sp.php" class="nav-item <?php echo $current_page == 'berikan_sp.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-exclamation-triangle w-5"></i>
                        <span class="font-medium">Berikan SP</span>
                    </a>
                </li>
                
            <?php elseif ($current_role == 'dokter'): ?>
                <li>
                    <a href="dashboard_dokter.php" class="nav-item <?php echo $current_page == 'dashboard_dokter.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg">
                        <i class="fas fa-home w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="daftar_menu_dokter.php" class="nav-item <?php echo $current_page == 'daftar_menu_dokter.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-utensils w-5"></i>
                        <span class="font-medium">Review Menu</span>
                    </a>
                </li>
                
            <?php elseif ($current_role == 'vendor'): ?>
                <li>
                    <a href="dashboard_vendor.php" class="nav-item <?php echo $current_page == 'dashboard_vendor.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg">
                        <i class="fas fa-home w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="buat_menu.php" class="nav-item <?php echo $current_page == 'buat_menu.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-plus-circle w-5"></i>
                        <span class="font-medium">Buat Menu</span>
                    </a>
                </li>
                <li>
                    <a href="kirim_makanan.php" class="nav-item <?php echo $current_page == 'kirim_makanan.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-truck w-5"></i>
                        <span class="font-medium">Kirim Makanan</span>
                    </a>
                </li>
                <li>
                    <a href="laporan_vendor.php" class="nav-item <?php echo $current_page == 'laporan_vendor.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-file-alt w-5"></i>
                        <span class="font-medium">Laporan Menu</span>
                    </a>
                </li>
                <li>
                    <a href="daftar_sp.php" class="nav-item <?php echo $current_page == 'daftar_sp.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-exclamation-triangle w-5"></i>
                        <span class="font-medium">Daftar SP</span>
                    </a>
                </li>
                
            <?php elseif ($current_role == 'sekolah'): ?>
                <li>
                    <a href="dashboard_sekolah.php" class="nav-item <?php echo $current_page == 'dashboard_sekolah.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg">
                        <i class="fas fa-home w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="jadwal_menu.php" class="nav-item <?php echo $current_page == 'jadwal_menu.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-calendar-alt w-5"></i>
                        <span class="font-medium">Jadwal Menu</span>
                    </a>
                </li>
                <li>
                    <a href="terima_makanan.php" class="nav-item <?php echo $current_page == 'terima_makanan.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-inbox w-5"></i>
                        <span class="font-medium">Terima Makanan</span>
                    </a>
                </li>
                <li>
                    <a href="buat_keluhan.php" class="nav-item <?php echo $current_page == 'buat_keluhan.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:text-white">
                        <i class="fas fa-comment-dots w-5"></i>
                        <span class="font-medium">Buat Keluhan</span>
                    </a>
                </li>
            <?php endif; ?>
            </ul>
        </nav>

        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <div>
                    <?php 
                    $user = getUserData();
                    $nama = htmlspecialchars($user['nama_lengkap'] ?? 'User');
                    ?>
                    <p class="font-medium text-sm"><?php echo $nama; ?></p>
                    <p class="text-xs text-white/60"><?php echo ucfirst($current_role); ?></p>
                </div>
            </div>
            <a href="../logout.php" class="flex items-center justify-center gap-2 w-full py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors text-sm">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto ml-64 transition-all duration-300">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h2>
                <p class="text-xs sm:text-sm text-gray-500"><?php echo isset($page_subtitle) ? $page_subtitle : ''; ?></p>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">

