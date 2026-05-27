<?php
session_start();
require_once '../includes/koneksi.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama             = trim($_POST['nama']             ?? '');
    $email            = trim($_POST['email']            ?? '');
    $password         = $_POST['password']              ?? '';
    $confirm_password = $_POST['confirm_password']      ?? '';

    // ── Validasi ──
    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field wajib diisi.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';

    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';

    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok.';

    } else {
        // Cek apakah email sudah terdaftar
        $cek = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $cek->execute([$email]);

        if ($cek->fetch()) {
            $error = 'Email sudah terdaftar. Gunakan email lain.';
        } else {
            // Hash password lalu simpan
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insert = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
            $insert->execute([$nama, $email, $hash]);

            // Langsung redirect ke login dengan pesan sukses
            header('Location: ../patriotid_login/login.php?status=daftar_ok');
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
  <title>PatriotID - Daftar</title>
  <link rel="stylesheet" href="daftar.css" />
</head>
<body>

  <div class="auth-container">

    <div class="left-section">
      <img src="../garuda.png" alt="Garuda Pancasila" class="garuda">
    </div>

    <div class="right-section">
      <div class="form-box">
        <img src="../logo.png" alt="Logo PatriotID" class="logo">
        <h2>Daftar</h2>
        <p>Selamat Datang di PatriotID</p>

        <?php if ($error): ?>
          <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">

          <div class="input-group">
            <label for="nama">Nama</label>
            <input
              type="text"
              id="nama"
              name="nama"
              placeholder="Masukkan nama lengkap"
              value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
              required
            >
          </div>

          <div class="input-group">
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Masukkan email"
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              required
            >
          </div>

          <div class="input-row">
            <div class="input-group">
              <label for="pass1">Password</label>
              <div class="input-wrapper">
                <input
                  type="password"
                  id="pass1"
                  name="password"
                  placeholder="Masukkan password"
                  required
                >
                <span class="eye-icon" onclick="togglePass('pass1', this)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="#aaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </span>
              </div>
            </div>

            <div class="input-group">
              <label for="pass2">Confirm Password</label>
              <div class="input-wrapper">
                <input
                  type="password"
                  id="pass2"
                  name="confirm_password"
                  placeholder="Ulangi password"
                  required
                >
                <span class="eye-icon" onclick="togglePass('pass2', this)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="#aaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </span>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-daftar">Daftar</button>

          <div class="social-login">
            <button type="button" class="btn-google">
              <img src="../google.png" alt="Google" width="16"> Google
            </button>
            <button type="button" class="btn-facebook">
              <img src="../facebook.png" alt="Facebook" width="16"> Facebook
            </button>
          </div>

          <p class="register-link">
            Sudah punya akun? <a href="../patriotid_login/login.php">Masuk</a>
          </p>

        </form>
      </div>
    </div>

  </div>

  <script>
    function togglePass(id, icon) {
      const input = document.getElementById(id);
      if (input.type === 'password') {
        input.type = 'text';
        icon.style.opacity = '1';
      } else {
        input.type = 'password';
        icon.style.opacity = '0.5';
      }
    }
  </script>

</body>
</html>