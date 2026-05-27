<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}
require_once '../includes/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $pdo->prepare("DELETE FROM tragedi_bersejarah WHERE id = ?")->execute([$id]);
}
header('Location: data_pahlawan.php?tab=tragedi&status=hapus_ok');
exit;