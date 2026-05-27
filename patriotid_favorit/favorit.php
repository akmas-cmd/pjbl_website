<?php
session_start();
require_once '../includes/koneksi.php';

$isLogin  = isset($_SESSION['user_id']);
$user_id  = $isLogin ? (int)$_SESSION['user_id'] : 0;
$filter   = $_GET['filter'] ?? 'semua';

$favoritList = [];
$counts      = ['semua' => 0, 'pahlawan' => 0, 'tempat' => 0, 'tragedi' => 0];

if ($isLogin) {
    // ── JOIN ke 3 tabel sekaligus via UNION ──
    $stmt = $pdo->prepare("
        SELECT
            f.tipe,
            f.referensi_id,
            p.nama        AS nama,
            p.asal        AS sub,
            p.foto        AS foto
        FROM favorit f
        LEFT JOIN pahlawan p ON f.referensi_id = p.id
        WHERE f.user_id = ? AND f.tipe = 'pahlawan'

        UNION ALL

        SELECT
            f.tipe,
            f.referensi_id,
            t.nama        AS nama,
            t.lokasi      AS sub,
            t.foto        AS foto
        FROM favorit f
        LEFT JOIN tempat_bersejarah t ON f.referensi_id = t.id
        WHERE f.user_id = ? AND f.tipe = 'tempat'

        UNION ALL

        SELECT
            f.tipe,
            f.referensi_id,
            tr.judul      AS nama,
            ''            AS sub,
            tr.foto       AS foto
        FROM favorit f
        LEFT JOIN tragedi_bersejarah tr ON f.referensi_id = tr.id
        WHERE f.user_id = ? AND f.tipe = 'tragedi'

        ORDER BY referensi_id DESC
    ");
    $stmt->execute([$user_id, $user_id, $user_id]);
    $rows = $stmt->fetchAll();

    foreach ($rows as $r) {
        $counts['semua']++;
        $counts[$r['tipe']]++;
        $favoritList[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Favorit Saya | PatriotID</title>
  <link rel="stylesheet" href="favorit.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

  <?php
  $halaman_aktif = 'landingpage';
require_once '../patriotid_navbar/navbar.php';
?>

  <section class="page-header">
    <h2>Favorit <span>Saya</span></h2>
    <p>Koleksi pahlawan, tempat bersejarah, dan tragedi bersejarah yang kamu simpan.</p>
    <a href="../patriotid_landingpage/landingpage.php" class="back-btn">← Kembali ke Beranda</a>
  </section>

  <main class="main-content">

    <?php if (!$isLogin): ?>
      <div class="login-notice">
        <p>🔒 Kamu harus <a href="../patriotid_login/login.php">login</a> untuk melihat daftar favorit.</p>
      </div>
    <?php else: ?>

    <!-- Tab Filter -->
    <div class="filter-tabs">
      <a href="?filter=semua"    class="tab-btn <?= $filter==='semua'    ? 'active' : '' ?>">
        Semua <span class="tab-count"><?= $counts['semua'] ?></span>
      </a>
      <a href="?filter=pahlawan" class="tab-btn <?= $filter==='pahlawan' ? 'active' : '' ?>">
        Pahlawan <span class="tab-count"><?= $counts['pahlawan'] ?></span>
      </a>
      <a href="?filter=tempat"   class="tab-btn <?= $filter==='tempat'   ? 'active' : '' ?>">
        Tempat Bersejarah <span class="tab-count"><?= $counts['tempat'] ?></span>
      </a>
      <a href="?filter=tragedi"  class="tab-btn <?= $filter==='tragedi'  ? 'active' : '' ?>">
        Tragedi Bersejarah <span class="tab-count"><?= $counts['tragedi'] ?></span>
      </a>
    </div>

    <!-- Grid Favorit -->
    <div class="favorites-grid">
      <?php
        $tampil = array_filter($favoritList, fn($r) =>
            $filter === 'semua' || $r['tipe'] === $filter
        );
      ?>

      <?php if (empty($tampil)): ?>
        <div class="empty-state">
          <div class="empty-icon">🤍</div>
          <h3>Belum ada favorit</h3>
          <p>Kamu belum menambahkan apapun ke daftar favorit. Yuk, jelajahi konten PatriotID!</p>
          <a href="../patriotid_landingpage/landingpage.php" class="btn-explore">Jelajahi Sekarang</a>
        </div>
      <?php else: ?>
        <?php foreach ($tampil as $r):
          $labelBadge = ['pahlawan' => 'Pahlawan', 'tempat' => 'Tempat', 'tragedi' => 'Tragedi'];
          $classBadge = ['pahlawan' => 'badge-pahlawan', 'tempat' => 'badge-tempat', 'tragedi' => 'badge-tragedi'];

          // ── Tentukan link detail per tipe ──
          if ($r['tipe'] === 'pahlawan') {
              $href = '../patriotid_konten/konten.php?id='        . $r['referensi_id'];
          } elseif ($r['tipe'] === 'tempat') {
              $href = '../patriotid_konten/konten_tempat.php?id=' . $r['referensi_id'];
          } elseif ($r['tipe'] === 'tragedi') {
              $href = '../patriotid_konten/konten_tragedi.php?id='. $r['referensi_id'];
          } else {
              $href = '#';
          }

          $nama = htmlspecialchars($r['nama'] ?? '-');
          $sub  = htmlspecialchars($r['sub']  ?? '');

          // ── Foto: strip suffix -2 seperti di landingpage ──
          $fotoFile = $r['foto'] ?? '';
          $baseName = pathinfo($fotoFile, PATHINFO_FILENAME);
          $baseSlug = str_replace('-2', '', $baseName);
          $foto     = '../' . $baseSlug . '.png';
        ?>
        <div class="fav-card type-<?= $r['tipe'] ?>">
          <span class="badge <?= $classBadge[$r['tipe']] ?>"><?= $labelBadge[$r['tipe']] ?></span>

          <!-- Tombol hapus dari favorit -->
          <form method="POST" action="../patriotid_favorit/hapus_favorit.php" class="form-unfav">
            <input type="hidden" name="tipe"         value="<?= $r['tipe'] ?>">
            <input type="hidden" name="referensi_id" value="<?= $r['referensi_id'] ?>">
            <button type="submit" class="btn-unfav" title="Hapus dari favorit">❤️</button>
          </form>

          <img class="card-img"
               src="<?= htmlspecialchars($foto) ?>"
               onerror="this.src='../placeholder.png'"
               alt="<?= $nama ?>">

          <div class="card-body">
            <h3><?= $nama ?></h3>
            <?php if ($sub): ?><p><?= $sub ?></p><?php endif; ?>
            <a href="<?= $href ?>" class="btn-detail">Lihat Detail →</a>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?php endif; // end isLogin ?>
  </main>

</body>
</html>