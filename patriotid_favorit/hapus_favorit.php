<?php
session_start();
require_once '../includes/koneksi.php';

// Harus login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}

$user_id      = (int)$_SESSION['user_id'];
$tipe         = $_POST['tipe']         ?? '';
$referensi_id = (int)($_POST['referensi_id'] ?? 0);

// Validasi
$tipeValid = ['pahlawan', 'tempat', 'tragedi'];
if (in_array($tipe, $tipeValid) && $referensi_id > 0) {
    $stmt = $pdo->prepare("DELETE FROM favorit WHERE user_id = ? AND tipe = ? AND referensi_id = ?");
    $stmt->execute([$user_id, $tipe, $referensi_id]);
}

// Redirect kembali ke halaman favorit
header('Location: ../patriotid_favorit/favorit.php');
exit;