<?php
// ============================================================
//  PATRIOTID — Koneksi Database
//  File ini di-include di semua halaman yang butuh database.
//  Letakkan file ini di folder: patriotid/includes/koneksi.php
// ============================================================

$host   = 'localhost';
$dbname = 'patriotid_db';
$user   = 'root';
$pass   = '';           // Kosong = default XAMPP, ganti jika kamu set password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<p style='color:red;font-family:sans-serif;padding:20px'>
        <strong>Koneksi database gagal!</strong><br>
        Pastikan XAMPP sudah aktif dan database <em>patriotid_db</em> sudah dibuat.<br><br>
        Error: " . $e->getMessage() . "
    </p>");
}
