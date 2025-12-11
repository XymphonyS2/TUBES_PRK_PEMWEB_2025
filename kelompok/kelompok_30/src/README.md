Daftar Anggota
1. Muhammad Hafiz Assyifa (2315061072)
2. Muhammad Raihan Jamil (2315061105)
3. Zaskia Jihan Nabila (2315061055)
4. Achmad Fauzan (2255061014)

Sistem Manajemen Makanan Sekolah

Sistem untuk mengelola distribusi makanan sekolah dengan 4 role: Vendor, Pegawai, Dokter, dan Sekolah.

Cara Instalasi
0. 📥 Clone/Download Repository

Opsi 1: Clone dengan Git
Jika pakai Laragon:
bashcd C:\laragon\www
git clone https://github.com/XymphonyS2/TUBES_PRK_PEMWEB_2025.git
cd TUBES_PRK_PEMWEB_2025

Jika pakai XAMPP:
bashcd C:\xampp\htdocs
git clone https://github.com/XymphonyS2/TUBES_PRK_PEMWEB_2025.git
cd TUBES_PRK_PEMWEB_2025

Opsi 2: Download ZIP
Klik tombol hijau "Code" di halaman repository
Pilih "Download ZIP"
Extract file ZIP ke:
Laragon: C:\laragon\www\ atau
XAMPP: C:\xampp\htdocs\

Rename folder hasil extract menjadi nama yang mudah diingat
Hasil akhir:
Laragon: C:\laragon\www\NAMA-FOLDER-PROJECT\
XAMPP: C:\xampp\htdocs\NAMA-FOLDER-PROJECT\

1. ⚙️ Persyaratan
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx) - atau gunakan Laragon/XAMPP

2. 🗄️ Setup Database
1. Buka phpMyAdmin atau MySQL CLI
2. Buat database baru dengan nama `mbg` (atau ubah di `config/database.php`)
3. Import file `database.sql`:
   
   Via MySQL CLI:
   ```bash
   mysql -u root -p mbg < database.sql
   ```
   
   Via phpMyAdmin:
   - Pilih database `mbg`
   - Klik tab "Import"
   - Pilih file `database.sql`
   - Klik "Go"

3. 🔧 Konfigurasi
Edit file `config/database.php` jika perlu mengubah kredensial database:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mbg');
```

4. 🚀 Menjalankan Aplikasi
Jika menggunakan Laragon:
1. Pastikan Apache dan MySQL sudah running
2. Pastikan folder project di letakkan di `C:\laragon\www\`
3. Akses di browser: `http://localhost/NAMA-FOLDER-PROJECT\`

Jika menggunakan XAMPP:
1. Pastikan Apache dan MySQL sudah running
2. astikan folder project di letakkan di `C:\xampp\htdocs\`
3. Akses di browser: `http://localhost/NAMA-FOLDER-PROJECT`

🔐 Login Default
Akun Pegawai:
- Email: `pegawai@admin.com`
- Password: `password`

⚠️ Penting: Segera ubah password default setelah login pertama kali!

📋 Alur Penggunaan
ALUR 1-4: Membuat Akun (Pegawai)

1. Login sebagai Pegawai
2. Klik menu "Manajemen Akun"
3. Pilih tab sesuai role yang ingin dibuat:
   - **Buat Akun Pegawai: Nama Lengkap, Email, Password, Alamat, Kontak
   - **Buat Akun Dokter: Nama Lengkap, Gelar, Email, Password, Alamat, Kontak
   - **Buat Akun Sekolah: Nama Sekolah, Jumlah Siswa, Email, Password, Alamat, Kontak
   - **Buat Akun Vendor: Nama Vendor, Email, Password, Alamat, Kontak
4. Isi formulir dan klik "Buat Akun"

ALUR 5: Vendor Membuat Menu → Dokter Review → Pegawai Setujui

1. Vendor membuat menu:
- Login sebagai Vendor
- Klik "Buat Menu"
- Isi: Tanggal Mulai, Tanggal Selesai, Jenis Makanan, Jenis Minuman, Komposisi, Porsi Maksimal
- Klik "Kirim ke Pemerintah"

2. Dokter review menu:
- Login sebagai Dokter
- Klik "Review Menu"
- Lihat detail menu
- Klik "Setujui" atau "Tolak" dengan alasan

3. Pegawai tentukan sekolah:
- Login sebagai Pegawai
- Klik "Tentukan Sekolah"
- Pilih menu yang sudah disetujui Dokter
- Pilih sekolah (pastikan jumlah siswa ≤ porsi menu)
- Tentukan porsi dan klik "Tentukan"

ALUR 6: Vendor Kirim Makanan

1. Login sebagai Vendor
2. Klik "Kirim Makanan"
3. Pilih jadwal pengiriman
4. Upload bukti pengiriman (foto) - bisa multiple files
5. Isi porsi dikirim dan catatan
6. Klik "Kirim"

ALUR 7: Sekolah Terima & Buat Keluhan

1. Sekolah terima makanan:
- Login sebagai Sekolah
- Klik "Terima Makanan"
- Lihat daftar pengiriman
- Klik "Terima" untuk konfirmasi

2. Buat keluhan (jika ada masalah):

- Klik "Buat Keluhan"
- Pilih pengiriman yang bermasalah
- Pilih jenis keluhan: Basi, Berbau, Porsi Kurang, Komposisi Tidak Sesuai, Lain-lain
- Isi catatan
- Klik "Kirim Keluhan"

ALUR 8: Pegawai Hapus Akun

1. Login sebagai Pegawai
2. Klik "Manajemen Akun"
3. Scroll ke bawah ke bagian "Daftar Semua Akun"
4. Klik tombol "Hapus" di akun yang ingin dihapus
5. Konfirmasi penghapusan

ALUR 9: Pegawai Lihat Laporan & Berikan SP

1. Lihat laporan:
- Login sebagai Pegawai
- Klik "Laporan"
- Lihat semua laporan dari Vendor dan keluhan dari Sekolah

2. Berikan SP:
- Klik "Berikan SP"
- Pilih Vendor
- Pilih tingkat SP:
  - 1 = Ringan
  - 2 = Sedang
  - 3 = Pencabutan Izin
- Pilih jenis pelanggaran
- Isi pesan
- Klik "Berikan SP"

ALUR 10: Pengiriman dan Penerimaan Makanan

Role Vendor (Pengiriman):
- Login sebagai Vendor
- Masuk ke halaman "Kirim Makanan"
- Pilih jadwal yang tersedia
- Upload bukti pengiriman (bisa beberapa foto sekaligus)
- Isi jumlah porsi dan catatan
- Klik "Kirim"

Role Sekolah (Penerimaan):
- Login sebagai Sekolah
- Masuk ke halaman "Terima Makanan"
- Lihat daftar makanan yang dikirim
- Cek bukti pengiriman
- Klik "Terima" untuk konfirmasi penerimaan
- Jika ada masalah, buat keluhan

Melihat Jadwal Menu:
- Login sebagai Sekolah
- Klik "Jadwal Menu" untuk melihat list jadwal
- Klik "Detail" pada salah satu jadwal untuk melihat informasi lengkap (vendor, makanan, komposisi, dll)

✨ Fitur Per Role

👨‍🍳 VENDOR
- ✅ Buat menu dengan rentang tanggal
- ✅ Kirim makanan dengan bukti pengiriman (multiple files)
- ✅ Berikan laporan menu (foto struk, proses pembuatan)
- ✅ Lihat daftar SP yang diterima
- ✅ Lihat riwayat pengiriman

👔 PEGAWAI
- ✅ Buat akun (Pegawai, Dokter, Vendor, Sekolah)
- ✅ Lihat daftar menu dari Vendor
- ✅ Tentukan sekolah untuk menu yang disetujui
- ✅ Lihat laporan dan keluhan
- ✅ Berikan SP kepada Vendor
- ✅ Hapus akun

👨‍⚕️ DOKTER
- ✅ Lihat daftar menu dari Vendor
- ✅ Setujui/tolak menu berdasarkan nilai gizi
- ✅ Berikan catatan/alasan penolakan

🏫 SEKOLAH
- ✅ Lihat jadwal menu (hari ini dan kedepan)
- ✅ Lihat detail jadwal menu
- ✅ Terima makanan dari vendor
- ✅ Buat keluhan jika ada masalah
- ✅ Lihat riwayat penerimaan makanan

📁 Struktur Folder

```
NAMA-FOLDER-PROJECT/
├── assets/
│   └── js/
├── auth/
│   └── login.php
├── config/
│   ├── database.php
│   └── auth.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── sidebar.php
├── pages/
│   ├── vendor/
│   │   ├── dashboard_vendor.php
│   │   ├── buat_menu.php
│   │   └── kirim_makanan.php
│   ├── pegawai/
│   │   ├── dashboard_pegawai.php
│   │   └── manajemen_akun.php
│   ├── dokter/
│   │   ├── dashboard_dokter.php
│   │   └── review_menu.php
│   └── sekolah/
│       ├── dashboard_sekolah.php
│       ├── jadwal_menu.php
│       ├── detail_jadwal.php
│       └── terima_makanan.php
├── api/
│   └── ... (file API)
├── uploads/
│   ├── bukti_pengiriman/
│   ├── foto_struk/
│   └── foto_proses/
├── database.sql
├── index.php
└── README.md
```

🔧 Troubleshooting

Error: Koneksi database gagal
- ✅ Pastikan MySQL/MariaDB sudah running
- ✅ Cek kredensial di `config/database.php`
- ✅ Pastikan database `mbg` sudah dibuat dan di-import

Tidak bisa login
- ✅ Pastikan database sudah di-import dengan benar
- ✅ Password default untuk akun admin: `password`
- ✅ Pastikan PHP extension `mysqli` sudah enabled

Error 404 - Page Not Found
- ✅ Pastikan mod_rewrite Apache sudah enabled (jika pakai Apache)
- ✅ Cek struktur folder dan path file
- ✅ Pastikan akses URL sesuai dengan folder project