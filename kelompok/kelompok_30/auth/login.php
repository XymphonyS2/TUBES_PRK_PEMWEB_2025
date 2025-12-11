<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        header('Location: ../index.php?error=Email dan password harus diisi');
        exit();
    }
    
    $conn = koneksiDatabase();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama'] = $user['nama_lengkap'];
            
            $role = $user['role'];
            header("Location: ../pages/dashboard_$role.php");
            exit();
        } else {
            header('Location: ../index.php?error=Password salah');
            exit();
        }
    } else {
        header('Location: ../index.php?error=Email tidak ditemukan');
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header('Location: ../index.php');
    exit();
}
?>

