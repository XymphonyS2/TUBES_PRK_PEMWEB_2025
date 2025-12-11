<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = koneksiDatabase();
    
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $kontak = $_POST['kontak'] ?? '';
    
    if (empty($email) || empty($password) || empty($role)) {
        header('Location: ../pages/manajemen_akun.php?error=Data wajib tidak boleh kosong');
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header('Location: ../pages/manajemen_akun.php?error=Email sudah terdaftar');
        exit();
    }
    $stmt->close();
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    if ($role == 'dokter') {
        $gelar = $_POST['gelar'] ?? '';
        if (empty($nama_lengkap) || empty($gelar)) {
            header('Location: ../pages/manajemen_akun.php?error=Nama lengkap dan gelar wajib diisi');
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password, role, alamat, kontak, gelar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nama_lengkap, $email, $password_hash, $role, $alamat, $kontak, $gelar);
    } elseif ($role == 'vendor') {
        $nama_vendor = $_POST['nama_vendor'] ?? '';
        if (empty($nama_vendor)) {
            header('Location: ../pages/manajemen_akun.php?error=Nama vendor wajib diisi');
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password, role, alamat, kontak, nama_vendor) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $nama_lengkap = $nama_vendor;
        $stmt->bind_param("sssssss", $nama_lengkap, $email, $password_hash, $role, $alamat, $kontak, $nama_vendor);
    } elseif ($role == 'sekolah') {
        $nama_sekolah = $_POST['nama_sekolah'] ?? '';
        $jumlah_siswa = (int)($_POST['jumlah_siswa'] ?? 0);
        if (empty($nama_sekolah) || $jumlah_siswa <= 0) {
            header('Location: ../pages/manajemen_akun.php?error=Nama sekolah dan jumlah siswa wajib diisi');
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password, role, alamat, kontak, nama_sekolah, jumlah_siswa) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $nama_lengkap = $nama_sekolah; 
        $stmt->bind_param("sssssssi", $nama_lengkap, $email, $password_hash, $role, $alamat, $kontak, $nama_sekolah, $jumlah_siswa);
    } else {
        
        if (empty($nama_lengkap)) {
            header('Location: ../pages/manajemen_akun.php?error=Nama lengkap wajib diisi');
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password, role, alamat, kontak) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nama_lengkap, $email, $password_hash, $role, $alamat, $kontak);
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: ../pages/manajemen_akun.php?success=Akun berhasil dibuat');
        exit();
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header('Location: ../pages/manajemen_akun.php?error=Gagal membuat akun: ' . urlencode($error));
        exit();
    }
} else {
    header('Location: ../pages/manajemen_akun.php');
    exit();
}
?>

