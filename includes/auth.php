<?php
// ============================================================
//  PATRIOTID — Auth Helper
//  Include di halaman yang hanya boleh diakses setelah login.
//  Contoh pemakaian: require_once '../includes/auth.php';
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}

// Variabel siap pakai di semua halaman yang include file ini
$user_id   = $_SESSION['user_id'];
$user_nama = $_SESSION['user_nama'];
$user_role = $_SESSION['user_role'];
