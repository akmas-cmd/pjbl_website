<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}
require_once '../includes/koneksi.php';

// ── Update last_active user yang sedang login ──
$pdo->prepare("UPDATE users SET last_active = NOW() WHERE id = ?")
    ->execute([$_SESSION['user_id']]);

// ── Total Pahlawan dari DB ──
$totalPahlawan = (int)$pdo->query("SELECT COUNT(*) FROM pahlawan")->fetchColumn();

$totalTempat = (int)$pdo->query("SELECT COUNT(*) FROM tempat_bersejarah")->fetchColumn();
$totalTragedi = (int)$pdo->query("SELECT COUNT(*) FROM tragedi_bersejarah")->fetchColumn();

// ── Total Favorit dari DB ──
$totalFavorit = (int)$pdo->query("SELECT COUNT(*) FROM favorit")->fetchColumn();
// ── User Online: last_active dalam 5 menit terakhir ──
$onlineUsers = $pdo->query("
    SELECT nama, role, last_active
    FROM users
    ORDER BY last_active DESC
")->fetchAll();

// Hitung berapa yang benar-benar online (< 5 menit)
$onlineCount = 0;
foreach ($onlineUsers as $u) {
    if ($u['last_active'] && (time() - strtotime($u['last_active'])) < 300) {
        $onlineCount++;
    }
}

$halaman_aktif = 'beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PatriotID - Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
</head>
<body>

  <!-- ── TOPBAR ── -->
  <?php require_once '../patriotid_navbar/navbar.php'; ?>

  <div class="layout">

    <!-- ── SIDEBAR ── -->
    <aside class="sidebar">
      <nav>
        <a href="../patriotid_dashboardadmin/dashboard.php"     class="nav-item active">Dashboard utama</a>
        <a href="../patriotid_dashboardadmin/data_pahlawan.php" class="nav-item">Data Pahlawan</a>
        <a href="../patriotid_dashboardadmin/profil.php"        class="nav-item">Profil</a>
        <a href="../patriotid_logout/logout.php"                class="nav-item">Keluar</a>
      </nav>
    </aside>

    <!-- ── MAIN ── -->
    <main class="main">
      <h1 class="page-title">Dashboard</h1>
      <h2 class="section-title">Ringkasan Utama</h2>

      <div class="cards-row">
  <!-- Total Pahlawan -->
  <div class="stat-card">
    <p class="stat-label">Total Pahlawan</p>
    <p class="stat-value"><?= $totalPahlawan ?></p>
  </div>

  <!-- Total Tempat -->
  <div class="stat-card">
    <p class="stat-label">Total Tempat</p>
    <p class="stat-value"><?= $totalTempat ?></p>
  </div>

  <!-- Total Tragedi -->
  <div class="stat-card">
    <p class="stat-label">Total Tragedi</p>
    <p class="stat-value"><?= $totalTragedi ?></p>
  </div>

  <!-- Favorit User -->
  <div class="stat-card">
    <p class="stat-label">Favorit User</p>
    <p class="stat-value"><?= $totalFavorit ?></p>
  </div>
</div>

      <!-- User Online — dari DB -->
      <div class="online-card">
        <div class="online-header">
          <p class="online-header-text">
            User Online (<?= $onlineCount ?>)
          </p>
        </div>

        <?php foreach ($onlineUsers as $u):
          $isOnline = $u['last_active'] && (time() - strtotime($u['last_active'])) < 300;
          $label    = $u['role'] === 'admin' ? '⭐ ' : '';
        ?>
        <div class="user-row">
          <span class="dot <?= $isOnline ? 'online' : 'offline' ?>"></span>
          <span class="user-name">
            <?= $label ?><?= htmlspecialchars($u['nama']) ?>
            <span class="user-role"><?= $u['role'] === 'admin' ? 'Admin' : 'User' ?></span>
          </span>
          <?php if ($u['last_active']): ?>
            <span class="last-seen">
              <?= $isOnline ? 'Online' : 'Terakhir: ' . date('d/m H:i', strtotime($u['last_active'])) ?>
            </span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php if (empty($onlineUsers)): ?>
          <div class="user-row">
            <span style="color:#aaa; font-size:13px;">Belum ada pengguna terdaftar.</span>
          </div>
        <?php endif; ?>
      </div>
    </main>

  </div>

  <!-- Auto-refresh setiap 60 detik untuk update status online -->
  <script>
    setTimeout(() => location.reload(), 60000);
  </script>

</body>
</html>