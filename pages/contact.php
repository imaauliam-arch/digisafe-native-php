<section class="page-banner">
    <div class="container"><span class="eyebrow">TERHUBUNG DENGAN KAMI</span><h1>Kontak</h1><p>Punya pertanyaan, saran, atau topik keamanan digital yang ingin dibahas? Kirim pesan melalui formulir berikut.</p></div>
</section>
<section class="container section contact-layout">
    <div class="contact-info">
        <span class="eyebrow">LET'S TALK</span><h2>Kami senang mendengar pendapatmu.</h2>
        <p>Masukan dari pembaca membantu DIGISAFE berkembang dan menghadirkan materi yang lebih bermanfaat.</p>
        <div class="contact-detail"><span>✉</span><div><strong>Formulir pesan</strong><small>Pesan akan disimpan pada database website.</small></div></div>
        <div class="contact-detail"><span>🛡</span><div><strong>Privasi</strong><small>Jangan cantumkan kata sandi, PIN, atau kode OTP dalam pesan.</small></div></div>
    </div>
    <form class="contact-form" method="POST" action="index.php?page=contact">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <?php if (!empty($contactSuccess)): ?><div class="alert success"><?= e($contactSuccess) ?></div><?php endif; ?>
        <?php if (!empty($contactError)): ?><div class="alert error"><?= e($contactError) ?></div><?php endif; ?>
        <label for="name">Nama lengkap</label><input id="name" name="name" type="text" maxlength="100" required value="<?= e($_POST['name'] ?? '') ?>">
        <label for="email">Email</label><input id="email" name="email" type="email" maxlength="150" required value="<?= e($_POST['email'] ?? '') ?>">
        <label for="subject">Subjek</label><input id="subject" name="subject" type="text" maxlength="150" required value="<?= e($_POST['subject'] ?? '') ?>">
        <label for="message">Pesan</label><textarea id="message" name="message" rows="5" required><?= e($_POST['message'] ?? '') ?></textarea>
        <button class="btn btn-primary" type="submit">Kirim Pesan <span>→</span></button>
    </form>
</section>
