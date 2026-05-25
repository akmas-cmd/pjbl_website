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
  <title>PatriotID - Data Pahlawan</title>
  <link rel="stylesheet" href="data_pahlawan.css" />
</head>
<body>

  <aside class="sidebar">
    <div class="logo">
      <img src="assets/logo.png" alt="PatriotID Logo" />
      <span class="logo-text">PATRIOT<span>ID</span></span>
    </div>
    <nav>
      <a href="../patriotid_dashboardadmin/dashboard.php" class="nav-item">Dashboard utama</a>
      <a href="../patriotid_dashboardadmin/data_pahlawan.php" class="nav-item active">Data Pahlawan</a>
      <a href="../patriotid_dashboardadmin/profil.php" class="nav-item">Profil</a>
      <a href="../patriotid_logout/logout.php" class="nav-item">Keluar</a>
    </nav>
  </aside>

  <main class="main">
    <h1 class="page-title">Data Pahlawan</h1>
    <h2 class="section-title">Daftar Pahlawan Nusantara</h2>

    <div class="table-card">
      <div class="table-header">
        <span class="table-header-text">Data Lengkap</span>
        <button class="btn-tambah">Tambah Data</button>
      </div>

      <table class="data-table">
        <thead>
          <tr>
            <th>No.</th>
            <th>Nama</th>
            <th>Asal</th>
            <th>Era</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Ir. Soekarno</td>
            <td>Blitar</td>
            <td>Proklamasi</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-hapus">Hapus</button>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Jenderal Sudirman</td>
            <td>Yogyakarta</td>
            <td>Perjuangan</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-hapus">Hapus</button>
            </td>
          </tr>
          <tr>
            <td>3</td>
            <td>R.A. Kartini</td>
            <td>Jepara</td>
            <td>Perjuangan</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-hapus">Hapus</button>
            </td>
          </tr>
           <tr>
            <td>4</td>
            <td>Ki Hajar Dewantara</td>
            <td>Yogyakarta</td>
            <td>Perjuangan</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-hapus">Hapus</button>
            </td>
          </tr>
           <tr>
            <td>5</td>
            <td>Pangeran Diponegoro</td>
            <td>Yogyakarta</td>
            <td>Perjuangan</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-hapus">Hapus</button>
            </td>
          </tr>
           <tr>
            <td>6</td>
            <td>Kapiten Pattimura</td>
            <td>Jawa Barat</td>
            <td>Perjuangan</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-hapus">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

</body>
</html>