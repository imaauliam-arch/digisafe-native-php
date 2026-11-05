<section class="page-banner">
    <div class="container"><span class="eyebrow">KONTRIBUSI PEMBACA</span><h1>Bagikan Artikelmu</h1><p>Pengunjung dapat mengirim tulisan literasi digital, menambahkan gambar unggulan, dan menentukan tag yang sesuai.</p></div>
</section>
<section class="container section form-page">
    <form class="contact-form upload-form" method="POST" enctype="multipart/form-data" action="index.php?page=create-post">
        <?php if (!empty($createError)): ?><div class="alert error"><?= e($createError) ?></div><?php endif; ?>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-intro"><h2>Formulir artikel</h2><p>Kolom bertanda * wajib diisi. Artikel akan langsung tampil setelah dikirim.</p></div>
        <label for="author">Nama penulis *</label><input id="author" name="author" required maxlength="100" value="<?= e($_POST['author'] ?? '') ?>">
        <label for="title">Judul artikel *</label><input id="title" name="title" required maxlength="200" value="<?= e($_POST['title'] ?? '') ?>">
        <label for="category_id">Kategori *</label>
        <select id="category_id" name="category_id" required><option value="">Pilih kategori</option>
            <?php foreach ($ds_categories as $category): ?><option value="<?= (int)$category['id'] ?>" <?= (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?>
        </select>
        <label for="excerpt">Ringkasan artikel *</label><textarea id="excerpt" name="excerpt" rows="2" required><?= e($_POST['excerpt'] ?? '') ?></textarea>
        <label for="content">Isi artikel *</label><textarea id="content" name="content" rows="10" required placeholder="Tuliskan artikel literasi digitalmu di sini..."><?= e($_POST['content'] ?? '') ?></textarea>
        <label for="ds_tags">Tag * (minimal 2, pisahkan dengan koma)</label><input id="ds_tags" name="ds_tags" required placeholder="contoh: keamanan akun, privasi digital" value="<?= e($_POST['ds_tags'] ?? '') ?>">
        <label for="featured_image">Gambar unggulan * (JPG/PNG/WEBP, maksimal 3 MB)</label><input id="featured_image" name="featured_image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
        <label for="image_alt">Alt text gambar *</label><input id="image_alt" name="image_alt" required maxlength="255" placeholder="Jelaskan isi gambar untuk aksesibilitas">
        <label for="image_source_name">Sumber gambar</label><input id="image_source_name" name="image_source_name" maxlength="100" value="Diunggah oleh pengguna">
        <label for="image_source_url">URL sumber gambar (jika menggunakan gambar dari internet)</label><input id="image_source_url" name="image_source_url" type="url" placeholder="https://...">
        <p class="form-hint">Pastikan kamu memiliki hak untuk menggunakan gambar yang diunggah. Jangan unggah data pribadi atau gambar milik orang lain tanpa izin.</p>
        <button class="btn btn-primary" type="submit">Publikasikan Artikel <span>→</span></button>
    </form>
</section>
