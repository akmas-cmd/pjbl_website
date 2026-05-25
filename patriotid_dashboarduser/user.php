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
  <title>PatriotID - Profil</title>
  <link rel="stylesheet" href="user.css" />
</head>
<body>

  <aside class="sidebar">
    <div class="logo">
      <img src="assets/logo.png" alt="PatriotID Logo" />
      <span class="logo-text">PATRIOT<span>ID</span></span>
    </div>
    <nav>
      <a href="../patriotid_dashboardadmin/profil.php" class="nav-item active">Profil</a>
      <a href="../patriotid_logout/logout.php" class="nav-item">Keluar</a>
    </nav>
  </aside>

  <main class="main">
    <h1 class="page-title">Profil</h1>

    <div class="profil-card">
      <!-- Header merah -->
      <div class="profil-card-header">
        <h2 class="profil-card-title">Data Akun</h2>
      </div>

      <!-- Body form -->
      <div class="profil-card-body">
        <!-- Avatar -->
        <div class="avatar-wrapper">
          <label for="avatar-input" class="avatar-label">
            <img src="assets/avatar.png" alt="Foto Profil" class="avatar-img"
                 onerror="this.src=''; this.style.display='none'; this.parentElement.querySelector('.avatar-fallback').style.display='flex';" />
            <div class="avatar-fallback">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
              </svg>
            </div>
            <div class="avatar-overlay">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                <circle cx="12" cy="13" r="4"/>
              </svg>
            </div>
          </label>
          <input type="file" id="avatar-input" accept="image/*" style="display:none;"
                 onchange="previewAvatar(event)" />
        </div>

        <script>
          function previewAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
              const img = document.querySelector('.avatar-img');
              const fallback = document.querySelector('.avatar-fallback');
              img.src = e.target.result;
              img.style.display = 'block';
              fallback.style.display = 'none';
            };
            reader.readAsDataURL(file);
          }
        </script>

        <!-- Nama Lengkap -->
        <div class="form-group">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" class="form-input" placeholder="" />
        </div>

        <!-- Email -->
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" class="form-input" placeholder="" />
          <p class="form-hint">Emial tidak bisa diubah</p>
        </div>

        <!-- Kata Sandi -->
        <div class="form-group">
          <label class="form-label">Kata Sandi</label>
          <input type="password" class="form-input" placeholder="" />
          <a href="#" class="link-ubah">Ubah Kata Sandi</a>
        </div>
      </div>
    </div>
  </main>

</body>
</html>