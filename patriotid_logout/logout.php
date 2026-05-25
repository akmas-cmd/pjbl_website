<?php
// ============================================================
//  PATRIOTID — Logout
//  Letakkan di: patriotid/logout.php
// ============================================================
session_start();
session_destroy();
header('Location: ../patriotid_login/login.php');
exit;