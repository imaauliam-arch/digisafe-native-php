<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
if (!empty($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }
$error = '';
$adminCount = (int)$pdo->query('SELECT COUNT(*) FROM ds_admins')->fetchColumn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $error = 'Sesi login tidak valid. Muat ulang halaman.';
    else {
        $identity = trim($_POST['identity'] ?? ''); $password = $_POST['password'] ?? '';
        $stmt = $pdo->prepare('SELECT * FROM ds_admins WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$identity, $identity]); $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$admin['id']; $_SESSION['admin_name'] = $admin['name'];
            $pdo->prepare('UPDATE ds_admins SET last_login_at = NOW() WHERE id = ?')->execute([$admin['id']]);
            log_activity($pdo, 'admin_login', 'Admin berhasil login', 'admin', (int)$admin['id']);
            header('Location: dashboard.php'); exit;
        }
        $error = 'Username/email atau password tidak sesuai.';
    }
}
?><!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Login Admin | DIGISAFE</title>
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/admin.css">
    </head><body class="auth-body">
        
    <form class="auth-card" method="post">
        <a class="brand" href="../index.php">
            
        <span class="brand-mark">D</span>
        <span>DIGI<span class="brand-accent">SAFE</span></span></a>
        <span class="eyebrow">ADMIN ACCESS</span><h1>Selamat datang</h1>
        <p>Masuk untuk mengelola konten dan memantau aktivitas DIGISAFE.</p>
        
        <?php if(isset($_GET['setup'])): ?><div class="alert success">Akun admin berhasil dibuat. Silakan login.</div>
            <?php endif; ?><?php if($adminCount===0): ?><div class="alert success">Belum ada admin. <a href="setup.php">
                <u>Buat akun admin pertama</u></a>.</div><?php endif; ?><?php if($error): ?>
                    <div class="alert error"><?= e($error) ?>
                </div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label>Username atau email</label><input name="identity" required autocomplete="username">
                <label>Password</label><input type="password" name="password" required autocomplete="current-password">
                <button class="btn btn-primary" type="submit">Login →</button>
                <a class="auth-back" href="../index.php">← Kembali ke website</a>
            </form>
        </body>
</html>
