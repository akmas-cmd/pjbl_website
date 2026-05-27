<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}
require_once '../includes/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama'] ?? '');
    $slug       = trim($_POST['slug'] ?? '');
    $lokasi     = trim($_POST['lokasi'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $sub1_judul = trim($_POST['sub1_judul'] ?? '');
    $sub1_isi   = trim($_POST['sub1_isi'] ?? '');
    $sub2_judul = trim($_POST['sub2_judul'] ?? '');
    $sub2_isi   = trim($_POST['sub2_isi'] ?? '');
    $sub3_judul = trim($_POST['sub3_judul'] ?? '');
    $sub3_isi   = trim($_POST['sub3_isi'] ?? '');
    $foto       = trim($_POST['foto'] ?? '');

    if (empty($nama) || empty($slug)) {
        $error = 'Nama dan Slug wajib diisi.';
    } else {
        $cek = $pdo->prepare("SELECT id FROM tempat_bersejarah WHERE slug = ?");
        $cek->execute([$slug]);
        if ($cek->fetch()) {
            $error = 'Slug sudah digunakan, gunakan slug lain.';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO tempat_bersejarah
                (nama, slug, lokasi, deskripsi, sub1_judul, sub1_isi, sub2_judul, sub2_isi, sub3_judul, sub3_isi, foto)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nama, $slug, $lokasi, $deskripsi, $sub1_judul, $sub1_isi, $sub2_judul, $sub2_isi, $sub3_judul, $sub3_isi, $foto]);
            header('Location: data_pahlawan.php?tab=tempat&status=tambah_ok');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PatriotID - Tambah Tempat Bersejarah</title>
  <link rel="stylesheet" href="data_pahlawan.css"/>
</head>
<body>
  <header class="topbar">
    <div class="topbar-logo">
      <img src="../logo.png" alt="PatriotID Logo"/>
      <span class="topbar-logo-text">PATRIOT<span>ID</span></span>
    </div>
    <nav class="topbar-nav">
      <a href="../patriotid_landingpage/landingpage.php" class="topbar-link">Beranda</a>
    </nav>
    <div class="topbar-actions">
      <a href="../patriotid_login/login.php"><img src="../usr.png" alt="User"/></a>
    </div>
  </header>

  <div class="layout">
    <aside class="sidebar">
      <nav>
        <a href="dashboard.php"     class="nav-item">Dashboard utama</a>
        <a href="data_pahlawan.php" class="nav-item active">Data Pahlawan</a>
        <a href="profil.php"        class="nav-item">Profil</a>
        <a href="../patriotid_logout/logout.php" class="nav-item">Keluar</a>
      </nav>
    </aside>

    <main class="main">
      <h1 class="page-title">Tambah Tempat Bersejarah</h1>

      <?php if ($error): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="form-card">
        <form method="POST">

          <div class="form-row-2">
            <div class="form-group">
              <label>Nama <span class="required">*</span></label>
              <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" placeholder="contoh: Candi Borobudur" required>
            </div>
            <div class="form-group">
              <label>Slug <span class="required">*</span></label>
              <input type="text" name="slug" id="slug" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>" placeholder="contoh: candi-borobudur" required>
              <small>Huruf kecil, tanpa spasi</small>
            </div>
          </div>

          <div class="form-group">
            <label>Lokasi</label>
            <input type="text" name="lokasi" value="<?= htmlspecialchars($_POST['lokasi'] ?? '') ?>" placeholder="contoh: Magelang, Jawa Tengah">
          </div>

          <div class="form-group">
            <label>Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="2" placeholder="Deskripsi singkat untuk card di landing page"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Sub Judul 1</label>
              <input type="text" name="sub1_judul" value="<?= htmlspecialchars($_POST['sub1_judul'] ?? '') ?>" placeholder="contoh: Sejarah Pembangunan">
            </div>
            <div class="form-group">
              <label>Isi Sub Judul 1</label>
              <textarea name="sub1_isi" rows="3" placeholder="Isi paragraf sub judul 1"><?= htmlspecialchars($_POST['sub1_isi'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Sub Judul 2</label>
              <input type="text" name="sub2_judul" value="<?= htmlspecialchars($_POST['sub2_judul'] ?? '') ?>" placeholder="contoh: Masa Kejayaan">
            </div>
            <div class="form-group">
              <label>Isi Sub Judul 2</label>
              <textarea name="sub2_isi" rows="3"><?= htmlspecialchars($_POST['sub2_isi'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Sub Judul 3</label>
              <input type="text" name="sub3_judul" value="<?= htmlspecialchars($_POST['sub3_judul'] ?? '') ?>" placeholder="contoh: Kondisi Saat Ini">
            </div>
            <div class="form-group">
              <label>Isi Sub Judul 3</label>
              <textarea name="sub3_isi" rows="3"><?= htmlspecialchars($_POST['sub3_isi'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="form-group">
            <label>Nama File Foto</label>
            <input type="text" name="foto" value="<?= htmlspecialchars($_POST['foto'] ?? '') ?>" placeholder="contoh: borobudur-2.png">
            <small>Nama file foto yang sudah ada di folder root</small>
          </div>

          <div class="form-actions">
            <a href="data_pahlawan.php?tab=tempat" class="btn-batal">← Batal</a>
            <button type="submit" class="btn-simpan">Simpan Data</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script>
    document.querySelector('[name="nama"]').addEventListener('input', function() {
      const s = document.getElementById('slug');
      if (!s.dataset.manual) {
        s.value = this.value.toLowerCase().replace(/[^a-z0-9\s]/g,'').trim().replace(/\s+/g,'-');
      }
    });
    document.getElementById('slug').addEventListener('input', function() { this.dataset.manual = true; });
  </script>
</body>
</html>