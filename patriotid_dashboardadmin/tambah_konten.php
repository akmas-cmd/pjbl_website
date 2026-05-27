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
    $asal       = trim($_POST['asal'] ?? '');
    $era        = trim($_POST['era'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $biografi   = trim($_POST['biografi'] ?? '');
    $sub1_judul = trim($_POST['sub1_judul'] ?? '');
    $sub1_isi   = trim($_POST['sub1_isi'] ?? '');
    $sub2_judul = trim($_POST['sub2_judul'] ?? '');
    $sub2_isi   = trim($_POST['sub2_isi'] ?? '');
    $foto       = trim($_POST['foto'] ?? '');

    if (empty($nama) || empty($slug)) {
        $error = 'Nama dan Slug wajib diisi.';
    } else {
        // Cek slug unik
        $cek = $pdo->prepare("SELECT id FROM pahlawan WHERE slug = ?");
        $cek->execute([$slug]);
        if ($cek->fetch()) {
            $error = 'Slug sudah digunakan, gunakan slug lain.';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO pahlawan (nama, slug, asal, era, deskripsi, biografi, sub1_judul, sub1_isi, sub2_judul, sub2_isi, foto)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nama, $slug, $asal, $era, $deskripsi, $biografi, $sub1_judul, $sub1_isi, $sub2_judul, $sub2_isi, $foto]);
            header('Location: data_pahlawan.php?status=tambah_ok');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PatriotID - Tambah Pahlawan</title>
  <link rel="stylesheet" href="data_pahlawan.css" />
</head>
<body>

  <header class="topbar">
    <div class="topbar-logo">
      <img src="../logo.png" alt="PatriotID Logo" />
      <span class="topbar-logo-text">PATRIOT<span>ID</span></span>
    </div>
    <nav class="topbar-nav">
      <a href="#" class="topbar-link">Beranda</a>
      <a href="#" class="topbar-link">Pahlawan</a>
      <a href="#" class="topbar-link">Tempat Bersejarah</a>
      <a href="#" class="topbar-link">Tragedi Bersejarah</a>
    </nav>
    <div class="topbar-actions">
      <a href="../patriotid_login/login.php"><img src="../usr.png" alt="User Icon"></a>
    </div>
  </header>

  <div class="layout">
    <aside class="sidebar">
      <nav>
        <a href="../patriotid_dashboardadmin/dashboard.php" class="nav-item">Dashboard utama</a>
        <a href="../patriotid_dashboardadmin/data_pahlawan.php" class="nav-item active">Data Pahlawan</a>
        <a href="../patriotid_dashboardadmin/profil.php" class="nav-item">Profil</a>
        <a href="../patriotid_logout/logout.php" class="nav-item">Keluar</a>
      </nav>
    </aside>

    <main class="main">
      <h1 class="page-title">Tambah Data Pahlawan</h1>

      <?php if ($error): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="form-card">
        <form method="POST" action="">

          <div class="form-row-2">
            <div class="form-group">
              <label>Nama <span class="required">*</span></label>
              <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" placeholder="contoh: Ir. Soekarno" required>
            </div>
            <div class="form-group">
              <label>Slug <span class="required">*</span></label>
              <input type="text" name="slug" id="slug" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>" placeholder="contoh: soekarno" required>
              <small>Huruf kecil, tanpa spasi, tanpa karakter khusus</small>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Asal Daerah</label>
              <input type="text" name="asal" value="<?= htmlspecialchars($_POST['asal'] ?? '') ?>" placeholder="contoh: Blitar">
            </div>
            <div class="form-group">
              <label>Era</label>
              <input type="text" name="era" value="<?= htmlspecialchars($_POST['era'] ?? '') ?>" placeholder="contoh: Proklamasi">
            </div>
          </div>

          <div class="form-group">
            <label>Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="2" placeholder="Deskripsi singkat untuk card di landing page"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label>Biografi</label>
            <textarea name="biografi" rows="5" placeholder="Paragraf biografi utama"><?= htmlspecialchars($_POST['biografi'] ?? '') ?></textarea>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Sub Judul 1</label>
              <input type="text" name="sub1_judul" value="<?= htmlspecialchars($_POST['sub1_judul'] ?? '') ?>" placeholder="contoh: Proklamator dan Presiden Pertama">
            </div>
            <div class="form-group">
              <label>Sub Judul 2</label>
              <input type="text" name="sub2_judul" value="<?= htmlspecialchars($_POST['sub2_judul'] ?? '') ?>" placeholder="contoh: Masa Kepresidenan">
            </div>
          </div>

          <div class="form-group">
            <label>Isi Sub Judul 1</label>
            <textarea name="sub1_isi" rows="4" placeholder="Isi paragraf sub judul 1"><?= htmlspecialchars($_POST['sub1_isi'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label>Isi Sub Judul 2</label>
            <textarea name="sub2_isi" rows="4" placeholder="Isi paragraf sub judul 2"><?= htmlspecialchars($_POST['sub2_isi'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label>Nama File Foto</label>
            <input type="text" name="foto" value="<?= htmlspecialchars($_POST['foto'] ?? '') ?>" placeholder="contoh: soekarno-2.png">
            <small>Nama file foto yang sudah ada di folder root (../nama-file.png)</small>
          </div>

          <div class="form-actions">
            <a href="data_pahlawan.php" class="btn-batal">← Batal</a>
            <button type="submit" class="btn-simpan">Simpan Data</button>
          </div>

        </form>
      </div>
    </main>
  </div>

  <script>
    // Auto-generate slug dari nama
    document.querySelector('[name="nama"]').addEventListener('input', function() {
      const slugField = document.getElementById('slug');
      if (slugField.dataset.manual !== 'true') {
        slugField.value = this.value
          .toLowerCase()
          .replace(/[^a-z0-9\s]/g, '')
          .trim()
          .replace(/\s+/g, '-');
      }
    });
    document.getElementById('slug').addEventListener('input', function() {
      this.dataset.manual = 'true';
    });
  </script>

</body>
</html>