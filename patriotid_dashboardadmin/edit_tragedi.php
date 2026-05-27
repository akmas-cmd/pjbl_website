<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}
require_once '../includes/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: data_pahlawan.php?tab=tragedi'); exit; }

$stmt = $pdo->prepare("SELECT * FROM tragedi_bersejarah WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$t = $stmt->fetch();
if (!$t) { header('Location: data_pahlawan.php?tab=tragedi'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul      = trim($_POST['judul'] ?? '');
    $slug       = trim($_POST['slug'] ?? '');
    $lokasi     = trim($_POST['lokasi'] ?? '');
    $tahun      = trim($_POST['tahun'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $sub1_judul = trim($_POST['sub1_judul'] ?? '');
    $sub1_isi   = trim($_POST['sub1_isi'] ?? '');
    $sub2_judul = trim($_POST['sub2_judul'] ?? '');
    $sub2_isi   = trim($_POST['sub2_isi'] ?? '');
    $sub3_judul = trim($_POST['sub3_judul'] ?? '');
    $sub3_isi   = trim($_POST['sub3_isi'] ?? '');
    $foto       = trim($_POST['foto'] ?? '');

    if (empty($judul) || empty($slug)) {
        $error = 'Judul dan Slug wajib diisi.';
    } else {
        $cek = $pdo->prepare("SELECT id FROM tragedi_bersejarah WHERE slug = ? AND id != ?");
        $cek->execute([$slug, $id]);
        if ($cek->fetch()) {
            $error = 'Slug sudah digunakan oleh data lain.';
        } else {
            $upd = $pdo->prepare("
                UPDATE tragedi_bersejarah SET
                  judul=?, slug=?, lokasi=?, tahun=?, deskripsi=?,
                  sub1_judul=?, sub1_isi=?, sub2_judul=?, sub2_isi=?,
                  sub3_judul=?, sub3_isi=?, foto=?
                WHERE id=?
            ");
            $upd->execute([$judul, $slug, $lokasi, $tahun, $deskripsi, $sub1_judul, $sub1_isi, $sub2_judul, $sub2_isi, $sub3_judul, $sub3_isi, $foto, $id]);
            header('Location: data_pahlawan.php?tab=tragedi&status=edit_ok');
            exit;
        }
    }

    $t = array_merge($t, $_POST);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PatriotID - Edit Tragedi Bersejarah</title>
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
      <h1 class="page-title">Edit Tragedi Bersejarah</h1>

      <?php if ($error): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="form-card">
        <form method="POST" action="">

          <div class="form-row-2">
            <div class="form-group">
              <label>Judul <span class="required">*</span></label>
              <input type="text" name="judul" value="<?= htmlspecialchars($t['judul']) ?>" required>
            </div>
            <div class="form-group">
              <label>Slug <span class="required">*</span></label>
              <input type="text" name="slug" value="<?= htmlspecialchars($t['slug']) ?>" required>
              <small>Huruf kecil, tanpa spasi</small>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Lokasi</label>
              <input type="text" name="lokasi" value="<?= htmlspecialchars($t['lokasi'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Tahun</label>
              <input type="text" name="tahun" value="<?= htmlspecialchars($t['tahun'] ?? '') ?>">
            </div>
          </div>

          <div class="form-group">
            <label>Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="2"><?= htmlspecialchars($t['deskripsi'] ?? '') ?></textarea>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Sub Judul 1</label>
              <input type="text" name="sub1_judul" value="<?= htmlspecialchars($t['sub1_judul'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Isi Sub Judul 1</label>
              <textarea name="sub1_isi" rows="3"><?= htmlspecialchars($t['sub1_isi'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Sub Judul 2</label>
              <input type="text" name="sub2_judul" value="<?= htmlspecialchars($t['sub2_judul'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Isi Sub Judul 2</label>
              <textarea name="sub2_isi" rows="3"><?= htmlspecialchars($t['sub2_isi'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Sub Judul 3</label>
              <input type="text" name="sub3_judul" value="<?= htmlspecialchars($t['sub3_judul'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Isi Sub Judul 3</label>
              <textarea name="sub3_isi" rows="3"><?= htmlspecialchars($t['sub3_isi'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="form-group">
            <label>Nama File Foto</label>
            <input type="text" name="foto" value="<?= htmlspecialchars($t['foto'] ?? '') ?>">
            <small>Nama file foto yang sudah ada di folder root (../nama-file.png)</small>
          </div>

          <div class="form-actions">
            <a href="data_pahlawan.php?tab=tragedi" class="btn-batal">← Batal</a>
            <button type="submit" class="btn-simpan">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>