<?php
session_start();
require_once '../includes/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['favorit' => false]);
    exit;
}

$tipe = $_GET['tipe'] ?? '';
$referensi_id = (int)($_GET['referensi_id'] ?? 0);

if (empty($tipe) || $referensi_id <= 0) {
    echo json_encode(['favorit' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM favorit WHERE user_id = ? AND tipe = ? AND referensi_id = ?");
$stmt->execute([$_SESSION['user_id'], $tipe, $referensi_id]);

echo json_encode(['favorit' => $stmt->rowCount() > 0]);