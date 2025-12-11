<?php
session_start();

function cekLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../index.php');
        exit();
    }
}

function cekRole($role_diperbolehkan) {
    cekLogin();
    if (!in_array($_SESSION['role'], $role_diperbolehkan)) {
        header('Location: ../unauthorized.php');
        exit();
    }
}

function getUserData() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    require_once __DIR__ . '/database.php';
    $conn = koneksiDatabase();
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $user;
}
