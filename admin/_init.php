<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

function admin_header(string $title, string $active = ''): void {
    ?>
    <!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($title) ?> | DIGISAFE Admin</title><link rel="stylesheet" href="../assets/css/style.css"><link rel="stylesheet" href="../assets/css/admin.css"></head><body class="admin-body">
    <aside class="admin-sidebar"><a class="brand" href="dashboard.php"><span class="brand-mark">D</span><span>DIGI<span class="brand-accent">SAFE</span></span></a><p class="sidebar-label">ADMIN PANEL</p><nav class="admin-nav"><a class="<?= $active==='dashboard'?'active':'' ?>" href="dashboard.php">▦ Dashboard</a><a class="<?= $active==='posts'?'active':'' ?>" href="posts.php">▤ Artikel</a><a class="<?= $active==='comments'?'active':'' ?>" href="comments.php">▧ Komentar</a><a class="<?= $active==='messages'?'active':'' ?>" href="messages.php">✉ Pesan Kontak</a><a class="<?= $active==='activities'?'active':'' ?>" href="activities.php">◷ Monitoring Aktivitas</a></nav><div class="sidebar-bottom"><a href="../index.php" target="_blank">↗ Lihat Website</a><a href="logout.php">⇥ Keluar</a></div></aside>
    <main class="admin-main"><header class="admin-topbar"><div><span class="eyebrow">DIGISAFE MANAGEMENT</span><h1><?= e($title) ?></h1></div><div class="admin-user"><span class="admin-avatar"><?= e(strtoupper(substr($_SESSION['admin_name'] ?? 'A',0,1))) ?></span><span><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span></div></header><div class="admin-content">
    <?php
}
function admin_footer(): void { echo '</div></main></body></html>'; }
function admin_flash(): void {
    if (!empty($_SESSION['admin_flash'])) { echo '<div class="admin-alert">'.e($_SESSION['admin_flash']).'</div>'; unset($_SESSION['admin_flash']); }
}
