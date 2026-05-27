<?php
if (!isset($_SESSION['user_id'])) {
    $link = '../patriotid_login/login.php';
} elseif ($_SESSION['user_role'] === 'admin') {
    $link = '../patriotid_dashboardadmin/dashboard.php';
} elseif ($_SESSION['user_role'] === 'user') {
    $link = '../patriotid_dashboarduser/user.php';
} else {
    $link = '../patriotid_login/login.php';
}

$halaman_aktif = $halaman_aktif ?? '';
?>
<link rel="stylesheet" href="../patriotid_navbar/navbar.css">

<header class="topbar">
  <div class="topbar-logo">
    <img src="../logo.png" alt="PatriotID Logo" />
    <span class="topbar-logo-text">PATRIOT<span>ID</span></span>
  </div>
  <nav class="topbar-nav">
    <a href="../patriotid_landingpage/landingpage.php#beranda"
       class="topbar-link <?= $halaman_aktif === 'beranda' ? 'active' : '' ?>">Beranda</a>
    <a href="../patriotid_landingpage/landingpage.php#profil"
       class="topbar-link <?= $halaman_aktif === 'pahlawan' ? 'active' : '' ?>">Pahlawan</a>
    <a href="../patriotid_landingpage/landingpage.php#tempat"
       class="topbar-link <?= $halaman_aktif === 'tempat' ? 'active' : '' ?>">Tempat Bersejarah</a>
    <a href="../patriotid_landingpage/landingpage.php#tragedi"
       class="topbar-link <?= $halaman_aktif === 'tragedi' ? 'active' : '' ?>">Tragedi Bersejarah</a>
    <a href="../patriotid_landingpage/landingpage.php#tentang"
       class="topbar-link <?= $halaman_aktif === 'tentang' ? 'active' : '' ?>">Tentang</a>
    <a href="../patriotid_favorit/favorit.php"
       class="topbar-link <?= $halaman_aktif === 'favorit' ? 'active' : '' ?>">Favorit</a>
  </nav>
  <div class="topbar-actions">
    <a href="<?= $link ?>">
      <img src="../usr.png" alt="User Icon">
    </a>
  </div>
</header>