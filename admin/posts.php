<?php
require_once __DIR__ . '/_init.php';
if (isset($_GET['delete'])) {
    $id=(int)$_GET['delete'];
    if ($_SERVER['REQUEST_METHOD']==='POST' && verify_csrf()) {
        $stmt=$pdo->prepare('SELECT title FROM ds_posts WHERE id=?'); $stmt->execute([$id]); $title=$stmt->fetchColumn();
        if ($title) { $pdo->prepare('DELETE FROM ds_posts WHERE id=?')->execute([$id]); log_activity($pdo,'post_delete','Menghapus artikel: '.$title,'post',$id); $_SESSION['admin_flash']='Artikel berhasil dihapus.'; }
        header('Location: posts.php'); exit;
    }
}
$posts=$pdo->query('SELECT p.id,p.title,p.status,p.published_at,c.name category_name FROM ds_posts p JOIN ds_categories c ON c.id=p.category_id ORDER BY p.created_at DESC')->fetchAll();
admin_header('Kelola Artikel','posts'); admin_flash();
?>
<div class="admin-panel"><div class="panel-heading"><div><span class="eyebrow">CONTENT MANAGEMENT</span><h2>Daftar artikel</h2></div><a class="btn btn-primary" href="post-form.php">＋ Tambah Artikel</a></div><div class="table-wrap"><table class="admin-table"><thead><tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody><?php foreach($posts as $post): ?><tr><td><strong><?= e($post['title']) ?></strong></td><td><?= e($post['category_name']) ?></td><td><span class="status <?= $post['status']==='published'?'published':'draft' ?>"><?= e($post['status']) ?></span></td><td><?= e(date('d/m/Y',strtotime($post['published_at']))) ?></td><td class="actions"><a href="post-form.php?id=<?= (int)$post['id'] ?>">Edit</a><form method="post" action="posts.php?delete=<?= (int)$post['id'] ?>" onsubmit="return confirm('Hapus artikel ini?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button type="submit">Hapus</button></form><a href="../index.php?page=post&slug=<?= e($pdo->query('SELECT slug FROM ds_posts WHERE id='.(int)$post['id'])->fetchColumn()) ?>" target="_blank">Lihat</a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php admin_footer(); ?>
