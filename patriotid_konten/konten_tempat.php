<?php
session_start();
require_once '../includes/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ../patriotid_landingpage/landingpage.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM tempat_bersejarah WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$t = $stmt->fetch();

if (!$t) {
    echo "<p>Data tempat tidak ditemukan.</p>";
    exit;
}

$prev = $pdo->prepare("SELECT id FROM tempat_bersejarah WHERE id < ? ORDER BY id DESC LIMIT 1");
$prev->execute([$id]);
$prevRow = $prev->fetch();

$next = $pdo->prepare("SELECT id FROM tempat_bersejarah WHERE id > ? ORDER BY id ASC LIMIT 1");
$next->execute([$id]);
$nextRow = $next->fetch();

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        $link = '../patriotid_dashboardadmin/dashboard.php';
    } elseif ($_SESSION['user_role'] === 'user') {
        $link = '../patriotid_dashboarduser/user.php';
    } else {
        $link = '../patriotid_login/login.php';
    }
} else {
    $link = '../patriotid_login/login.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($t['nama']) ?> | PatriotID</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="konten_tempat.css" />
</head>
<body>

  <?php
  $halaman_aktif = 'landingpage';
  require_once '../patriotid_navbar/navbar.php';
  ?>

  <main class="artikel">

    <div class="artikel-header">
      <h2><?= htmlspecialchars($t['nama']) ?></h2>
      <?php if (isset($_SESSION['user_id'])): ?>
        <button id="btn-fav" class="btn-fav" onclick="toggleFavorit('tempat', <?= $t['id'] ?>)">🤍</button>
      <?php else: ?>
        <a href="../patriotid_login/login.php" class="btn-fav" title="Login untuk menyimpan favorit">🤍</a>
      <?php endif; ?>
    </div>

    <?php if (!empty($t['lokasi'])): ?>
      <p class="lokasi-badge">📍 <?= htmlspecialchars($t['lokasi']) ?></p>
    <?php endif; ?>

    <?php if (!empty($t['deskripsi'])): ?>
      <p class="deskripsi-intro"><?= nl2br(htmlspecialchars($t['deskripsi'])) ?></p>
    <?php endif; ?>

    <div class="artikel-content">
      <div class="left">
        <?php if (!empty($t['sub1_judul'])): ?>
          <p><b><?= htmlspecialchars($t['sub1_judul']) ?></b><br>
          <?= nl2br(htmlspecialchars($t['sub1_isi'])) ?></p>
        <?php endif; ?>

        <?php if (!empty($t['sub2_judul'])): ?>
          <p><b><?= htmlspecialchars($t['sub2_judul']) ?></b><br>
          <?= nl2br(htmlspecialchars($t['sub2_isi'])) ?></p>
        <?php endif; ?>
      </div>

      <div class="right">
        <?php if (!empty($t['foto'])): ?>
          <img src="../<?= htmlspecialchars($t['foto']) ?>" alt="<?= htmlspecialchars($t['nama']) ?>">
        <?php endif; ?>

        <?php if (!empty($t['sub3_judul'])): ?>
          <p><b><?= htmlspecialchars($t['sub3_judul']) ?></b><br>
          <?= nl2br(htmlspecialchars($t['sub3_isi'])) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="btn-row">
      <a href="../patriotid_landingpage/landingpage.php#tempat" class="btn-back">← Kembali</a>

      <?php if ($prevRow): ?>
        <a href="konten_tempat.php?id=<?= $prevRow['id'] ?>" class="btn-nav">‹ Sebelumnya</a>
      <?php endif; ?>

      <?php if ($nextRow): ?>
        <a href="konten_tempat.php?id=<?= $nextRow['id'] ?>" class="btn-nav">Berikutnya ›</a>
      <?php endif; ?>
    </div>

    <div class="toast-fav" id="toast-fav"></div>

  </main>

  <footer>
    <div class="footer-container">
      <div class="footer-left">
        <div class="footer-logo">
          <img src="../logo.png" alt="PatriotID">
          <h3>PATRIOT<span>ID</span></h3>
        </div>
        <p>Pahlawan bukan hanya mereka yang berperang, tapi juga mereka yang menjaga budaya bangsa.</p>
        <p class="copy">2025 | All rights reserved</p>
      </div>
      <div class="footer-right">
        <div>
          <h4>Tentang</h4>
          <a href="#">Projek</a>
          <a href="#">Kontak</a>
          <a href="#">Tentang kami</a>
        </div>
        <div>
          <h4>Ikuti Kami</h4>
          <a href="#">X @patriotid</a>
          <a href="#">Instagram @patriotid</a>
          <a href="#">Facebook @patriotid</a>
        </div>
      </div>
    </div>
  </footer>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    <?php if (isset($_SESSION['user_id'])): ?>
    fetch('../patriotid_favorit/cek_favorit.php?tipe=tempat&referensi_id=<?= $t['id'] ?>')
      .then(r => r.json())
      .then(data => {
        const btn = document.getElementById('btn-fav');
        if (!btn) return;
        if (data.favorit) {
          btn.textContent = '❤️';
          btn.classList.add('aktif');
        }
      });
    <?php endif; ?>
  });

  function toggleFavorit(tipe, id) {
    fetch('../patriotid_favorit/tambah_favorit.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `tipe=${tipe}&referensi_id=${id}`
    })
    .then(r => r.json())
    .then(data => {
      const btn = document.getElementById('btn-fav');
      if (data.status === 'added') {
        btn.textContent = '❤️';
        btn.classList.add('aktif');
        tampilToast('❤️ Ditambahkan ke favorit!');
      } else {
        btn.textContent = '🤍';
        btn.classList.remove('aktif');
        tampilToast('🗑️ Dihapus dari favorit');
      }
    });
  }

  function tampilToast(pesan) {
    const t = document.getElementById('toast-fav');
    t.textContent = pesan;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
  }
  </script>

</body>
</html>