<?php
session_start();
require_once '../includes/koneksi.php';

// Harus login
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Login diperlukan']);
    exit;
}

$user_id      = (int)$_SESSION['user_id'];
$tipe         = $_POST['tipe']         ?? '';
$referensi_id = (int)($_POST['referensi_id'] ?? 0);

// Validasi tipe
$tipeValid = ['pahlawan', 'tempat', 'tragedi'];
if (!in_array($tipe, $tipeValid) || $referensi_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit;
}

// Cek apakah sudah difavoritkan
$cek = $pdo->prepare("SELECT id FROM favorit WHERE user_id = ? AND tipe = ? AND referensi_id = ?");
$cek->execute([$user_id, $tipe, $referensi_id]);

if ($cek->fetch()) {
    // Sudah ada → hapus (toggle off)
    $del = $pdo->prepare("DELETE FROM favorit WHERE user_id = ? AND tipe = ? AND referensi_id = ?");
    $del->execute([$user_id, $tipe, $referensi_id]);

    header('Content-Type: application/json');
    echo json_encode(['status' => 'removed', 'message' => 'Dihapus dari favorit']);
} else {
    // Belum ada → tambah (toggle on)
    $ins = $pdo->prepare("INSERT INTO favorit (user_id, tipe, referensi_id) VALUES (?, ?, ?)");
    $ins->execute([$user_id, $tipe, $referensi_id]);

    header('Content-Type: application/json');
    echo json_encode(['status' => 'added', 'message' => 'Ditambahkan ke favorit']);
}