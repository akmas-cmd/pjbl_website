<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}
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
  <header class="topbar">
    <div class="topbar-logo">
      <img src="../logo.png" alt="PatriotID Logo" />
      <span class="topbar-logo-text">PATRIOT<span>ID</span></span>
    </div>
    <nav class="topbar-nav">
      <a href="#" class="topbar-link active">Beranda</a>
      <a href="#" class="topbar-link">Pahlawan</a>
      <a href="#" class="topbar-link">Tempat Bersejarah</a>
      <a href="#" class="topbar-link">Tragedi Bersejarah</a>
      <a href="../patriotid_landingpage/landingpage.html#tentang" class="topbar-link">Tentang</a>
    </nav>
    <div class="topbar-actions">
      <a href="../patriotid_login/login.php"><img src="../usr.png" alt="User Icon"></a>
    </div>
  </header>

  <div class="layout">

    <!-- ── SIDEBAR ── -->
    <aside class="sidebar">
      <nav>
        <a href="../patriotid_dashboardadmin/dashboard.php" class="nav-item active">Dashboard utama</a>
        <a href="../patriotid_dashboardadmin/data_pahlawan.php" class="nav-item">Data Pahlawan</a>
        <a href="../patriotid_dashboardadmin/profil.php" class="nav-item">Profil</a>
        <a href="../patriotid_login/logout.php" class="nav-item">Keluar</a>
      </nav>
    </aside>

    <!-- ── MAIN ── -->
    <main class="main">
      <h1 class="page-title">Dashboard</h1>
      <h2 class="section-title">Ringkasan Utama</h2>

      <div class="cards-row">
        <div class="stat-card">
          <p class="stat-label">Total Pahlawan</p>
          <p class="stat-value">50</p>
        </div>
        <div class="stat-card">
          <p class="stat-label">Favorit user</p>
          <p class="stat-value">20</p>
        </div>
        <div class="quote-card">
          <p class="quote-text">"Merdeka atau mati!"</p>
          <p class="quote-sub">Semangat Pahlawan Nasional</p>
        </div>
      </div>

      <div class="online-card">
        <div class="online-header">
          <p class="online-header-text">User Online (3)</p>
        </div>
        <div class="user-row">
          <span class="dot online"></span>
          <span class="user-name"><strong>**Admin Pusat**</strong></span>
        </div>
        <div class="user-row">
          <span class="dot online"></span>
          <span class="user-name">Admin Sejarah Jakarta</span>
        </div>
        <div class="user-row">
          <span class="dot online"></span>
          <span class="user-name">Petugas Arsip 002</span>
        </div>
        <div class="user-row">
          <span class="dot offline"></span>
          <span class="user-name">User Non-Aktif</span>
        </div>
      </div>
    </main>

  </div>

</body>
</html>