CREATE DATABASE IF NOT EXISTS mbg;
USE mbg;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('vendor', 'pegawai', 'dokter', 'sekolah') NOT NULL,
    alamat TEXT,
    kontak VARCHAR(50),
    gelar VARCHAR(50) DEFAULT NULL COMMENT 'Untuk Dokter',
    nama_sekolah VARCHAR(255) DEFAULT NULL COMMENT 'Untuk Sekolah',
    nama_vendor VARCHAR(255) DEFAULT NULL COMMENT 'Untuk Vendor',
    jumlah_siswa INT DEFAULT NULL COMMENT 'Untuk Sekolah',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    jenis_makanan VARCHAR(255) NOT NULL,
    jenis_minuman VARCHAR(255) NOT NULL,
    komposisi TEXT NOT NULL,
    porsi_maksimal INT NOT NULL,
    status ENUM('pending', 'ditolak', 'disetujui') DEFAULT 'pending',
    pesan_dokter TEXT DEFAULT NULL,
    dokter_id INT DEFAULT NULL COMMENT 'Dokter yang approve/reject',
    tanggal_persetujuan DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    sekolah_id INT NOT NULL,
    tanggal DATE NOT NULL,
    porsi_ditentukan INT NOT NULL,
    pegawai_id INT NOT NULL COMMENT 'Pegawai yang menentukan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE,
    FOREIGN KEY (sekolah_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pegawai_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_jadwal (menu_id, sekolah_id, tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pengiriman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jadwal_id INT NOT NULL,
    vendor_id INT NOT NULL,
    sekolah_id INT NOT NULL,
    tanggal_pengiriman DATE NOT NULL,
    porsi_dikirim INT NOT NULL,
    bukti_pengiriman TEXT DEFAULT NULL COMMENT 'JSON array of file paths',
    catatan TEXT DEFAULT NULL,
    status ENUM('dikirim', 'diterima') DEFAULT 'dikirim',
    tanggal_diterima DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jadwal_id) REFERENCES jadwal(id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (sekolah_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS keluhan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pengiriman_id INT NOT NULL,
    sekolah_id INT NOT NULL,
    jenis_keluhan ENUM('basi', 'berbau', 'porsi_kurang', 'komposisi_tidak_sesuai', 'lain_lain') NOT NULL,
    catatan TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pengiriman_id) REFERENCES pengiriman(id) ON DELETE CASCADE,
    FOREIGN KEY (sekolah_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS laporan_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    vendor_id INT NOT NULL,
    foto_struk_belanja TEXT DEFAULT NULL COMMENT 'JSON array of file paths',
    foto_proses_pembuatan TEXT DEFAULT NULL COMMENT 'JSON array of file paths',
    keterangan TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    pegawai_id INT NOT NULL,
    tingkat_sp ENUM('1', '2', '3') NOT NULL COMMENT '1=Ringan, 2=Sedang, 3=Pencabutan Izin',
    jenis_pelanggaran ENUM('tidak_higienis', 'kecurangan_pendataan', 'lainnya') NOT NULL,
    pesan TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pegawai_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (nama_lengkap, email, password, role, alamat, kontak) 
VALUES ('Admin Pegawai', 'pegawai@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pegawai', 'Alamat Admin', '081234567890')
ON DUPLICATE KEY UPDATE nama_lengkap = nama_lengkap;
-- Password default: password