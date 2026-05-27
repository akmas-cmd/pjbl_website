<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}
require_once '../includes/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM pahlawan WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: data_pahlawan.php?status=hapus_ok');
exit;