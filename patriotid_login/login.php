<?php
session_start();

require_once '../includes/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($input) || empty($password)) {
        $error = 'Email/nomor telepon dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR no_telepon = ? LIMIT 1");
        $stmt->execute([$input, $input]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil — simpan ke session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header('Location: ../patriotid_dashboardadmin/dashboard.php');
            } else {
                header('Location: ../patriotid_dashboarduser/user.php');
            }
            exit;
        } else {
            $error = 'Email/nomor telepon atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | PatriotID</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="container">
    <div class="left-section">
      <img src="../garuda.png" alt="Garuda Pancasila" class="garuda">
    </div>

    <div class="right-section">
      <div class="form-box">
        <img src="../logo.png" alt="Logo PatriotID" class="logo">
        <h2>Login</h2>
        <p>Selamat Datang di PatriotID</p>

        <?php if ($error): ?>
          <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="input-group">
            <label for="email">Email atau Nomor Telepon</label>
            <input
              type="text"
              id="email"
              name="email"
              placeholder="Masukkan email atau nomor telepon"
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              required
            >
          </div>

          <div class="input-group">
            <label for="password">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Masukkan password"
              required
            >
          </div>

          <a href="#" class="forgot">Lupa Password?</a>

          <button type="submit" class="btn-login">Login</button>

          <div class="social-login">
            <button type="button" class="btn-google"><img src="../google.png" alt="Google Icon"> Google</button>
            <button type="button" class="btn-facebook"><img src="../facebook.png" alt="Facebook Icon"> Facebook</button>
          </div>

          <p class="register-link">
            Belum punya akun? <a href="../patriotid_daftar/daftar.php">Daftar</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</body>
</html>