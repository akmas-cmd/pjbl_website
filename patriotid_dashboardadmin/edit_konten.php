<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}
require_once '../includes/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: data_pahlawan.php');
    exit;
}

// Ambil data yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM pahlawan WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    header('Location: data_pahlawan.php');
    exit;
}

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
        // Cek slug unik (kecuali milik sendiri)
        $cek = $pdo->prepare("SELECT id FROM pahlawan WHERE slug = ? AND id != ?");
        $cek->execute([$slug, $id]);
        if ($cek->fetch()) {
            $error = 'Slug sudah digunakan oleh data lain.';
        } else {
            $upd = $pdo->prepare("
                UPDATE pahlawan SET
                  nama=?, slug=?, asal=?, era=?, deskripsi=?,
                  biografi=?, sub1_judul=?, sub1_isi=?,
                  sub2_judul=?, sub2_isi=?, foto=?
                WHERE id=?
            ");
            $upd->execute([$nama, $slug, $asal, $era, $deskripsi, $biografi, $sub1_judul, $sub1_isi, $sub2_judul, $sub2_isi, $foto, $id]);
            header('Location: data_pahlawan.php?status=edit_ok');
            exit;
        }
    }

    // Update $p dengan nilai POST agar form tidak kosong saat error
    $p = array_merge($p, $_POST);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PatriotID - Edit Pahlawan</title>
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
      <h1 class="page-title">Edit Data Pahlawan</h1>

      <?php if ($error): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="form-card">
        <form method="POST" action="">

          <div class="form-row-2">
            <div class="form-group">
              <label>Nama <span class="required">*</span></label>
              <input type="text" name="nama" value="<?= htmlspecialchars($p['nama']) ?>" required>
            </div>
            <div class="form-group">
              <label>Slug <span class="required">*</span></label>
              <input type="text" name="slug" value="<?= htmlspecialchars($p['slug']) ?>" required>
              <small>Huruf kecil, tanpa spasi</small>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Asal Daerah</label>
              <input type="text" name="asal" value="<?= htmlspecialchars($p['asal'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Era</label>
              <input type="text" name="era" value="<?= htmlspecialchars($p['era'] ?? '') ?>">
            </div>
          </div>

          <div class="form-group">
            <label>Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="2"><?= htmlspecialchars($p['deskripsi'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label>Biografi</label>
            <textarea name="biografi" rows="5"><?= htmlspecialchars($p['biografi'] ?? '') ?></textarea>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Sub Judul 1</label>
              <input type="text" name="sub1_judul" value="<?= htmlspecialchars($p['sub1_judul'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Sub Judul 2</label>
              <input type="text" name="sub2_judul" value="<?= htmlspecialchars($p['sub2_judul'] ?? '') ?>">
            </div>
          </div>

          <div class="form-group">
            <label>Isi Sub Judul 1</label>
            <textarea name="sub1_isi" rows="4"><?= htmlspecialchars($p['sub1_isi'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label>Isi Sub Judul 2</label>
            <textarea name="sub2_isi" rows="4"><?= htmlspecialchars($p['sub2_isi'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label>Nama File Foto</label>
            <input type="text" name="foto" value="<?= htmlspecialchars($p['foto'] ?? '') ?>">
            <small>Nama file foto (../nama-file.png)</small>
          </div>

          <div class="form-actions">
            <a href="data_pahlawan.php" class="btn-batal">← Batal</a>
            <button type="submit" class="btn-simpan">Simpan Perubahan</button>
          </div>

        </form>
      </div>
    </main>
  </div>

</body>
</html>