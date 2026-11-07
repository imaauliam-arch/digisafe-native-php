<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">LITERASI DIGITAL • KEAMANAN AKUN</span>
            <h1>Jelajahi dunia digital dengan <span>lebih aman.</span></h1>
            <p>Kenali ancaman, lindungi informasi pribadi, dan bangun kebiasaan digital yang lebih bijak melalui panduan sederhana dari DIGISAFE.</p>
            <a class="btn btn-primary" href="#artikel">Jelajahi Artikel <span>↗</span></a>
        </div>
        <div class="hero-visual" role="img" aria-label="Ilustrasi simbol keamanan digital berupa perisai dan gembok">
            <div class="orbit orbit-one"></div><div class="orbit orbit-two"></div>
            <div class="shield"><span>✓</span></div>
            <div class="float-card card-top">🔒 Data terlindungi</div>
            <div class="float-card card-bottom">✦ Stay safe online</div>
        </div>
    </div>
</section>

<section class="container intro-strip">
    <div><span class="eyebrow">KENAPA DIGISAFE?</span><h2>Keamanan digital dimulai dari kebiasaan kecil.</h2></div>
    <p>Satu klik yang ceroboh dapat membuka celah bagi pencurian data. Mulai dari memahami kata sandi hingga mengenali penipuan online, setiap pengetahuan membantu kita mengambil keputusan yang lebih aman.</p>
</section>

<section class="container section" id="artikel">
    <div class="section-heading">
        <div><span class="eyebrow">BACA & PELAJARI</span><h2>Jelajahi artikel</h2></div>
        <span class="muted"><?= count($ds_posts) ?> artikel ditemukan</span>
    </div>
    <form class="article-tools" method="get" action="index.php#artikel">
        <input type="hidden" name="page" value="home">
        <input type="search" name="q" placeholder="Cari artikel, misalnya phishing..." value="<?= e($search ?? '') ?>" aria-label="Cari artikel">
        <select name="category" aria-label="Filter kategori"><option value="0">Semua kategori</option><?php foreach($ds_categories as $cat): ?><option value="<?= (int)$cat['id'] ?>" <?= ((int)($categoryId ?? 0)===(int)$cat['id'])?'selected':'' ?>><?= e($cat['name']) ?></option><?php endforeach; ?></select>
        <button class="btn btn-primary" type="submit">Cari Artikel</button>
        <?php if(!empty($search)||!empty($categoryId)): ?><a class="clear-filter" href="index.php#artikel">Reset</a><?php endif; ?>
    </form>
    <div class="post-grid">
        <?php foreach ($ds_posts as $item): ?>
        <article class="post-card">
            <a class="post-image-link" href="index.php?page=post&slug=<?= e($item['slug']) ?>">
                <img src="<?= e($item['featured_image']) ?>" alt="<?= e($item['image_alt']) ?>">
            </a>
            <div class="post-content">
                <span class="category"><?= e($item['category_name']) ?></span>
                <h3><a href="index.php?page=post&slug=<?= e($item['slug']) ?>"><?= e($item['title']) ?></a></h3>
                <p><?= e($item['excerpt']) ?></p>
                <a class="read-more" href="index.php?page=post&slug=<?= e($item['slug']) ?>">Baca selengkapnya <span>→</span></a>
            </div>
        </article>
        <?php endforeach; ?>
        <?php if (!$ds_posts): ?><div class="empty-state"><h3>Artikel tidak ditemukan</h3><p>Coba kata kunci atau kategori lain.</p></div><?php endif; ?>
    </div>
</section>
