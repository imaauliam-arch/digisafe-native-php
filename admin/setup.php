<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
$count = (int)$pdo->query("SELECT COUNT(*) FROM ds_admins")->fetchColumn();
if ($count > 0) { header('Location: login.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $error = 'Sesi tidak valid. Muat ulang halaman.';
    else {
        $name = trim($_POST['name'] ?? ''); $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? ''); $password = $_POST['password'] ?? '';
        if ($name === '' || !preg_match('/^[a-zA-Z0-9_.-]{4,60}$/', $username) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 10) {
            $error = 'Lengkapi data. Username 4–60 karakter (huruf/angka/._-), password minimal 10 karakter.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO ds_admins (name, username, email, password_hash) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            log_activity($pdo, 'admin_setup', 'Akun administrator pertama dibuat', 'admin', (int)$pdo->lastInsertId());
            header('Location: login.php?setup=success'); exit;
        }
    }
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup Admin | DIGISAFE</title><link rel="stylesheet" href="../assets/css/style.css"><link rel="stylesheet" href="../assets/css/admin.css"></head><body class="auth-body"><form class="auth-card" method="post"><a class="brand" href="../index.php"><span class="brand-mark">D</span><span>DIGI<span class="brand-accent">SAFE</span></span></a><span class="eyebrow">INITIAL ADMIN SETUP</span><h1>Buat akun admin</h1><p>Halaman ini hanya tersedia sebelum akun admin pertama dibuat.</p><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Nama lengkap</label><input name="name" required maxlength="100"><label>Username</label><input name="username" required minlength="4" maxlength="60"><label>Email</label><input type="email" name="email" required maxlength="150"><label>Password (minimal 10 karakter)</label><input type="password" name="password" required minlength="10"><button class="btn btn-primary" type="submit">Buat akun admin →</button></form></body></html>
