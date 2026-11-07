<?php
require_once __DIR__ . '/_init.php';
log_activity($pdo, 'admin_dashboard_view', 'Admin membuka dashboard', 'admin', (int)$_SESSION['admin_id']);
$stats = [
 'posts' => (int)$pdo->query("SELECT COUNT(*) FROM ds_posts")->fetchColumn(),
 'published' => (int)$pdo->query("SELECT COUNT(*) FROM ds_posts WHERE status='published'")->fetchColumn(),
 'comments' => (int)$pdo->query("SELECT COUNT(*) FROM ds_comments")->fetchColumn(),
 'messages' => (int)$pdo->query("SELECT COUNT(*) FROM ds_contact_messages")->fetchColumn(),
 'categories' => (int)$pdo->query("SELECT COUNT(*) FROM ds_categories")->fetchColumn()
];
$recent = $pdo->query('SELECT activity_type, description, created_at FROM ds_activity_logs ORDER BY id DESC LIMIT 8')->fetchAll();
admin_header('Dashboard','dashboard'); admin_flash();
?>
<div class="stats-grid"><div class="stat-card"><span>Total Artikel</span><strong><?= $stats['posts'] ?></strong><small>Semua artikel</small></div><div class="stat-card"><span>Artikel Terbit</span><strong><?= $stats['published'] ?></strong><small>Tampil untuk publik</small></div><div class="stat-card"><span>Kategori</span><strong><?= $stats['categories'] ?></strong><small>Kategori konten</small></div><div class="stat-card"><span>Komentar</span><strong><?= $stats['comments'] ?></strong><small>Masuk dari pengunjung</small></div><div class="stat-card"><span>Pesan Kontak</span><strong><?= $stats['messages'] ?></strong><small>Pesan tersimpan</small></div></div>
<div class="admin-panel"><div class="panel-heading"><div><span class="eyebrow">RINGKASAN</span><h2>Aktivitas terbaru</h2></div><a class="admin-link" href="activities.php">Lihat semua →</a></div><?php if(!$recent): ?><p class="empty-admin">Belum ada aktivitas tercatat.</p><?php else: ?><div class="table-wrap"><table class="admin-table"><thead><tr><th>Aktivitas</th><th>Detail</th><th>Waktu</th></tr></thead><tbody><?php foreach($recent as $row): ?><tr><td><span class="activity-pill"><?= e(str_replace('_',' ',$row['activity_type'])) ?></span></td><td><?= e($row['description']) ?></td><td><?= e(date('d/m/Y H:i',strtotime($row['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
<div class="quick-actions"><a href="post-form.php">＋ Tulis artikel baru</a><a href="activities.php">◷ Buka monitoring aktivitas</a><a href="../index.php" target="_blank">↗ Lihat website publik</a></div>
<?php admin_footer(); ?>
