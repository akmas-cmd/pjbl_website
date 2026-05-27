<?php
session_start();
require_once '../includes/koneksi.php';

// Ambil semua data dari database
$pahlawans = $pdo->query("SELECT id, nama, asal, foto FROM pahlawan ORDER BY id ASC")->fetchAll();
$tempats   = $pdo->query("SELECT id, nama, lokasi, foto FROM tempat_bersejarah ORDER BY id ASC")->fetchAll();
$tragedis  = $pdo->query("SELECT id, judul, deskripsi, foto FROM tragedi_bersejarah ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PatriotID - Kenang Jasa Pahlawan</title>
  <link rel="stylesheet" href="landingpage.css">
</head>
<body>

  <!-- Navbar -->
  <?php
  $halaman_aktif = 'landingpage';
require_once '../patriotid_navbar/navbar.php';
?>

  <!-- Hero -->
  <section class="hero" id="beranda">
    <div class="hero-text">
      <p>Jasa pahlawan adalah bekal masa depan.</p>
      <h2>Kenang Jasanya,<br>Kobarkan Semangatnya</h2>
      <p>Semangat juang para pahlawan adalah api abadi yang takkan pernah padam.</p>
      <div class="buttons">
        <a href="#" class="btn-red">Lebih Lanjut</a>
      </div>
    </div>
    <div class="hero-img">
      <img src="../soekarno.png" alt="Soekarno">
    </div>
  </section>

  <!-- Proklamasi -->
  <section class="proklamasi">
    <div class="prok-box">
      <div><h3>Waktu</h3><p>17 Agustus 1945</p></div>
      <div><h3>Tempat</h3><p>Jl. Pegangsaan Timur</p></div>
      <div><h3>Dibacakan oleh</h3><p>Ir. Soekarno</p></div>
    </div>
    <div class="prok-desc">
      <div class="text">
        <h2>Proklamasi <span>Hari Merdeka</span></h2>
        <p>Proklamasi Kemerdekaan Indonesia merupakan momen paling bersejarah yang menjadi tonggak lahirnya bangsa Indonesia.
        Pada tanggal 17 Agustus 1945, Ir. Soekarno membacakan teks proklamasi di kediamannya di Jakarta.</p>
        <a href="#" class="btn-red">Baca Selengkapnya</a>
      </div>
      <img src="../peta-indonesia.png" alt="Peta Indonesia">
    </div>
  </section>

  <!-- ══ Profil Pahlawan — DYNAMIC ══ -->
  <section class="profil" id="profil">
    <h2>Profil <span>Pahlawan</span></h2>
    <div class="pahlawan-container">
      <?php foreach ($pahlawans as $p):
        $fotoFile  = $p['foto'] ?? '';
        $baseName  = pathinfo($fotoFile, PATHINFO_FILENAME);
        $baseSlug  = str_replace('-2', '', $baseName);
        $fotoProfil = '../' . $baseSlug . '-profil.png';
      ?>
      <div class="pahlawan-card">
        <img src="<?= htmlspecialchars($fotoProfil) ?>"
             onerror="this.src='../placeholder-profil.png'"
             alt="<?= htmlspecialchars($p['nama']) ?>">
        <h3><?= htmlspecialchars($p['nama']) ?></h3>
        <p><?= htmlspecialchars($p['asal'] ?? '') ?></p>
        <a href="../patriotid_konten/konten.php?id=<?= $p['id'] ?>" class="arrow">Detail</a>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ══ Tempat Bersejarah — foto otomatis dari DB ══ -->
  <section class="tempat" id="tempat">
    <div class="gallery">
      <?php foreach ($tempats as $t):
        $fotoFile    = $t['foto'] ?? '';
        $baseName    = pathinfo($fotoFile, PATHINFO_FILENAME); // borobudur-2
        $baseSlug    = str_replace('-2', '', $baseName);       // borobudur
        $fotoLanding = '../' . $baseSlug . '.png';             // ../borobudur.png
      ?>
        <a href="../patriotid_konten/konten_tempat.php?id=<?= $t['id'] ?>">
          <img src="<?= htmlspecialchars($fotoLanding) ?>"
               onerror="this.src='../placeholder.png'"
               alt="<?= htmlspecialchars($t['nama']) ?>">
        </a>
      <?php endforeach; ?>
    </div>
    <div class="text">
      <h2>Tempat <span>Bersejarah</span> di Indonesia</h2>
      <ul>
        <?php foreach ($tempats as $t): ?>
          <li><?= htmlspecialchars($t['nama']) ?><?= !empty($t['lokasi']) ? ' (' . htmlspecialchars($t['lokasi']) . ')' : '' ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- ══ Tragedi Bersejarah — foto otomatis dari DB ══ -->
  <section class="tragedi" id="tragedi">
    <h2>Tragedi <span>Bersejarah</span></h2>
    <div class="tragedi-container">
      <?php foreach ($tragedis as $tr):
        $fotoFile    = $tr['foto'] ?? '';
        $baseName    = pathinfo($fotoFile, PATHINFO_FILENAME); // pki-2
        $baseSlug    = str_replace('-2', '', $baseName);       // pki
        $fotoLanding = '../' . $baseSlug . '.png';             // ../pki.png
        $deskripsiSingkat = mb_strimwidth($tr['deskripsi'] ?? '', 0, 100, '...');
      ?>
      <div class="card">
        <img src="<?= htmlspecialchars($fotoLanding) ?>"
             onerror="this.src='../placeholder.png'"
             alt="<?= htmlspecialchars($tr['judul']) ?>">
        <h3><?= htmlspecialchars($tr['judul']) ?></h3>
        <p><?= htmlspecialchars($deskripsiSingkat) ?></p>
        <a href="../patriotid_konten/konten_tragedi.php?id=<?= $tr['id'] ?>" class="btn-red">Selengkapnya</a>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Tentang -->
  <section class="tentang" id="tentang">
    <h2><span>PatriotID</span><br>Tentang Pahlawan Nusantara</h2>
    <div class="tentang-grid">
      <div class="card kiri-atas">
        <img src="../icon-sejarah.png" alt="Sejarah">
        <h3>Sejarah</h3>
        <p>Situs ini didedikasikan untuk mengabadikan jasa para pahlawan yang berjuang demi kemerdekaan bangsa.</p>
      </div>
      <div class="card kanan-atas">
        <img src="../icon-patriotisme.png" alt="Patriotisme">
        <h3>Patriotisme</h3>
        <p>Mendorong rasa cinta tanah air dan menumbuhkan semangat kepahlawanan.</p>
      </div>
      <div class="card tengah">
        <img src="../patung.png" alt="Patung Pahlawan">
      </div>
      <div class="card kiri-bawah">
        <img src="../icon-cable.png" alt="Cable Services">
        <h3>Cable Services</h3>
        <p>Stay connected with high-quality cable services that deliver reliable and fast internet, television.</p>
      </div>
      <div class="card kanan-bawah">
        <img src="../icon-edukasi.png" alt="Edukasi">
        <h3>Edukasi</h3>
        <p>Menyajikan biografi dan kontribusi pahlawan untuk menginspirasi generasi muda.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-left">
        <div class="footer-logo">
          <img src="../logo.png" alt="PatriotID Logo">
          <h3>PATRIOT<span>ID</span></h3>
        </div>
        <p>"Pahlawan bukan hanya mereka yang berperang, tapi juga mereka yang menjaga budaya bangsa."</p>
        <p class="footer-copy">2025 | All rights reserved</p>
      </div>
      <div class="footer-middle"></div>
      <div class="footer-right">
        <div class="footer-column">
          <h4>Tentang</h4>
          <ul>
            <li><a href="#">Projek</a></li>
            <li><a href="#">Kontak</a></li>
            <li><a href="#">Tentang kami</a></li>
          </ul>
        </div>
        <div class="footer-column">
          <h4>Ikuti Kami</h4>
          <ul class="social">
            <li><a href="#">𝕏 @patriotid</a></li>
            <li><a href="#">📷 @patriotid</a></li>
            <li><a href="#">📘 @patriotid</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>