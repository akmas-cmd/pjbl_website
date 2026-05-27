<?php
session_start();
require_once '../includes/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ../patriotid_landingpage/landingpage.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pahlawan WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    echo "<p>Data pahlawan tidak ditemukan.</p>";
    exit;
}

$prev = $pdo->prepare("SELECT id FROM pahlawan WHERE id < ? ORDER BY id DESC LIMIT 1");
$prev->execute([$id]);
$prevRow = $prev->fetch();

$next = $pdo->prepare("SELECT id FROM pahlawan WHERE id > ? ORDER BY id ASC LIMIT 1");
$next->execute([$id]);
$nextRow = $next->fetch();

// Link user icon
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
  <title>Profil Pahlawan - <?= htmlspecialchars($p['nama']) ?> | PatriotID</title>
  <link rel="stylesheet" href="konten.css" />
</head>
<body>

  <!-- ── TOPBAR (gaya Dashboard) ── -->
  <?php
  $halaman_aktif = 'landingpage';
require_once '../patriotid_navbar/navbar.php';
?>

  <!-- Konten Utama -->
  <main class="hero">
    <div class="text-section">

      <h2><?= htmlspecialchars($p['nama']) ?></h2>

      <?php if (!empty($p['biografi'])): ?>
        <p><?= nl2br(htmlspecialchars($p['biografi'])) ?></p>
      <?php endif; ?>

      <?php if (!empty($p['sub1_judul'])): ?>
        <h3><?= htmlspecialchars($p['sub1_judul']) ?></h3>
        <p><?= nl2br(htmlspecialchars($p['sub1_isi'])) ?></p>
      <?php endif; ?>

      <?php if (!empty($p['sub2_judul'])): ?>
        <h3><?= htmlspecialchars($p['sub2_judul']) ?></h3>
        <p><?= nl2br(htmlspecialchars($p['sub2_isi'])) ?></p>
      <?php endif; ?>

      <div class="btn-row">
  <a href="../patriotid_landingpage/landingpage.php#profil" class="btn-back">← Kembali</a>

  <?php if ($prevRow): ?>
    <a href="konten.php?id=<?= $prevRow['id'] ?>" class="btn-nav">‹ Sebelumnya</a>
  <?php endif; ?>

  <?php if ($nextRow): ?>
    <a href="konten.php?id=<?= $nextRow['id'] ?>" class="btn-nav">Berikutnya ›</a>
  <?php endif; ?>

  <?php if (isset($_SESSION['user_id'])): ?>
    <button id="btn-fav" class="btn-fav" onclick="toggleFavorit('pahlawan', <?= $id ?>)">🤍</button>
  <?php else: ?>
    <a href="../patriotid_login/login.php" class="btn-fav" title="Login untuk menyimpan favorit">🤍</a>
  <?php endif; ?>
</div>

<div class="toast-fav" id="toast-fav"></div>

      <div class="toast-fav" id="toast-fav"></div>
    </div>

    <div class="image-section">
      <?php if (!empty($p['foto'])): ?>
        <img src="../<?= htmlspecialchars($p['foto']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" />
      <?php else: ?>
        <div class="no-foto">Foto tidak tersedia</div>
      <?php endif; ?>

      <div class="info-badge">
        <?php if (!empty($p['asal'])): ?>
          <span>📍 <?= htmlspecialchars($p['asal']) ?></span>
        <?php endif; ?>
        <?php if (!empty($p['era'])): ?>
          <span>⚔️ Era <?= htmlspecialchars($p['era']) ?></span>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <script>
// Cek status favorit saat halaman dibuka
document.addEventListener('DOMContentLoaded', () => {
  <?php if (isset($_SESSION['user_id'])): ?>
  fetch('../patriotid_favorit/cek_favorit.php?tipe=pahlawan&referensi_id=<?= $id ?>')
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