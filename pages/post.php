<article class="container article-wrap">
    <a class="back-link" href="index.php">← Kembali ke Beranda</a>
    <div class="article-header">
        <span class="category"><?= e($post['category_name']) ?></span>
        <h1><?= e($post['title']) ?></h1>
        <p class="muted">Dipublikasikan <?= date('d F Y', strtotime($post['published_at'])) ?> · DIGISAFE</p>
    </div>
    <figure class="article-cover">
        <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['image_alt']) ?>">
        <figcaption>Sumber gambar: <a href="<?= e($post['image_source_url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($post['image_source_name']) ?></a></figcaption>
    </figure>
    <div class="article-body"><?= $post['content'] ?></div>
    <div class="tag-row"><strong>Tag:</strong>
        <?php foreach ($postTags as $tag): ?><span class="tag"><?= e($tag['name']) ?></span><?php endforeach; ?>
    </div>
    <section class="comments-section" id="komentar">
        <h2>Komentar (<?= count($ds_comments) ?>)</h2>
        <?php if (!empty($commentError)): ?><div class="alert error"><?= e($commentError) ?></div><?php endif; ?>
        <p class="comment-note">
            Komentar akan langsung ditampilkan. Pastikan komentar tetap sopan,
            relevan, dan tidak mengandung spam. Komentar yang melanggar etika
            dapat dihapus oleh admin.
        </p>
        <form class="contact-form comment-form" method="POST" action="index.php?page=post&slug=<?= e($post['slug']) ?>#komentar">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label for="commenter">Nama *</label><input id="commenter" name="commenter" required maxlength="100">
            <label for="comment_email">Email (tidak ditampilkan) *</label><input id="comment_email" name="comment_email" type="email" required maxlength="150">
            <label for="comment_body">Komentar *</label><textarea id="comment_body" name="comment_body" rows="4" required maxlength="2000"></textarea>
            <button class="btn btn-primary" type="submit" name="submit_comment">
                Kirim Komentar
            </button>
        </form>
        <div class="comment-list">
            <?php if (!$ds_comments): ?><p class="muted">Belum ada komentar. Jadilah yang pertama berkomentar.</p><?php endif; ?>
            <?php foreach ($ds_comments as $comment): ?>
            <article class="comment-item"><div class="comment-avatar"><?= e(mb_strtoupper(mb_substr($comment['commenter'], 0, 1))) ?></div><div><strong><?= e($comment['commenter']) ?></strong><small><?= date('d M Y, H:i', strtotime($comment['created_at'])) ?></small><p><?= nl2br(e($comment['comment_body'])) ?></p></div></article>
            <?php endforeach; ?>
        </div>
    </section>
</article>
