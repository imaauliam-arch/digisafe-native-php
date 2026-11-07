<?php
require_once __DIR__ . '/_init.php';

// Proses aksi admin.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id > 0 && $action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM ds_comments WHERE id = ?");
        $stmt->execute([$id]);

        log_activity(
            $pdo,
            'comment_deleted',
            'Admin menghapus komentar #' . $id,
            'comment',
            $id
        );

        $_SESSION['admin_flash'] = 'Komentar berhasil dihapus.';
        header('Location: comments.php');
        exit;
    }

    if ($id > 0 && $action === 'safe') {
        $stmt = $pdo->prepare("
            UPDATE ds_comments
            SET is_flagged = 0, flag_reason = NULL
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        log_activity(
            $pdo,
            'comment_unflagged',
            'Admin menandai komentar #' . $id . ' sebagai aman',
            'comment',
            $id
        );

        $_SESSION['admin_flash'] = 'Tanda peringatan komentar dihapus.';
        header('Location: comments.php');
        exit;
    }
}

// Ambil semua komentar.
$rows = $pdo->query("
    SELECT c.*, p.title AS post_title
    FROM ds_comments c
    JOIN ds_posts p ON p.id = c.post_id
    ORDER BY c.is_flagged DESC, c.created_at DESC
")->fetchAll();

admin_header('Moderasi Komentar', 'comments');
admin_flash();
?>

<div class="admin-panel">
    <div class="panel-heading">
        <div>
            <span class="eyebrow">COMMUNITY</span>
            <h2>Komentar Pengunjung</h2>
            <p>Periksa komentar dan tangani komentar yang melanggar etika.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pengunjung</th>
                    <th>Komentar</th>
                    <th>Artikel</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!$rows): ?>
                    <tr>
                        <td colspan="5">Belum ada komentar.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td>
                            <strong><?= e($r['commenter']) ?></strong>
                            <small class="table-sub">
                                <?= e($r['email']) ?>
                            </small>
                        </td>

                        <td>
                            <?= nl2br(e($r['comment_body'])) ?>

                            <?php if (!empty($r['is_flagged'])): ?>
                                <div style="margin-top:8px;color:#c0392b;font-weight:600;">
                                    ⚠ Peringatan otomatis:
                                    <?= e($r['flag_reason'] ?? 'Perlu diperiksa admin.') ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td><?= e($r['post_title']) ?></td>

                        <td>
                            <?php if (!empty($r['is_flagged'])): ?>
                                <span style="color:#c0392b;font-weight:600;">
                                    Perlu diperiksa
                                </span>
                            <?php else: ?>
                                <span style="color:#16834a;font-weight:600;">
                                    Aman
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="actions">
                            <?php if (!empty($r['is_flagged'])): ?>
                                <form method="POST" action="comments.php">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id"
                                           value="<?= (int) $r['id'] ?>">
                                    <input type="hidden" name="action" value="safe">

                                    <button type="submit">
                                        Tandai Aman
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form method="POST" action="comments.php"
                                  onsubmit="return confirm('Yakin ingin menghapus komentar ini?')">
                                <input type="hidden" name="csrf_token"
                                       value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id"
                                       value="<?= (int) $r['id'] ?>">
                                <input type="hidden" name="action" value="delete">

                                <button type="submit"
                                        style="background:#c0392b;color:white;">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php admin_footer(); ?>