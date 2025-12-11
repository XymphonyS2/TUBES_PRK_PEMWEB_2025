<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
require_once __DIR__ . '/../config/database.php';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $conn = koneksiDatabase();
    $stmt = $conn->prepare("DELETE FROM jadwal WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

header('Location: tentukan_sekolah.php');
exit();
?>

