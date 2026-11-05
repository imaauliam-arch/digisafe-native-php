<?php
require_once __DIR__ . '/config/database.php';
session_start();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf() {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

$page = $_GET['page'] ?? 'home';
$allowedPages = ['home', 'about', 'contact', 'post', 'create-post'];
if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);
    $page = '404';
}

$ds_categories = $pdo->query("SELECT id, name FROM ds_categories ORDER BY name")->fetchAll();

if ($page === 'post') {
    $slug = $_GET['slug'] ?? '';
    $stmt = $pdo->prepare(
        "SELECT p.*, c.name AS category_name
         FROM ds_posts p JOIN ds_categories c ON c.id = p.category_id
         WHERE p.slug = ? AND p.status = 'published' LIMIT 1"
    );
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
    if (!$post) {
        http_response_code(404);
        $page = '404';
    } else {
        $tagStmt = $pdo->prepare(
            "SELECT t.name FROM ds_tags t JOIN ds_post_tags pt ON pt.tag_id = t.id WHERE pt.post_id = ? ORDER BY t.name"
        );
        $tagStmt->execute([$post['id']]);
        $postTags = $tagStmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
            if (!verify_csrf()) {
                $commentError = 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.';
            } else {
                $commenter = trim($_POST['commenter'] ?? '');
                $commentEmail = trim($_POST['comment_email'] ?? '');
                $commentBody = trim($_POST['comment_body'] ?? '');
                if ($commenter === '' || !filter_var($commentEmail, FILTER_VALIDATE_EMAIL) || $commentBody === '') {
                    $commentError = 'Nama, email valid, dan komentar wajib diisi.';
                } elseif (mb_strlen($commenter) > 100 || mb_strlen($commentBody) > 2000) {
                    $commentError = 'Nama maksimal 100 karakter dan komentar maksimal 2.000 karakter.';
                } else {
                    $insert = $pdo->prepare(
                        "INSERT INTO ds_comments (post_id, commenter, email, comment_body, status) VALUES (?, ?, ?, ?, 'approved')"
                    );
                    $insert->execute([$post['id'], $commenter, $commentEmail, $commentBody]);
                    header('Location: index.php?page=post&slug=' . urlencode($slug) . '#komentar');
                    exit;
                }
            }
        }
        $commentStmt = $pdo->prepare(
            "SELECT commenter, comment_body, created_at FROM ds_comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at DESC"
        );
        $commentStmt->execute([$post['id']]);
        $ds_comments = $commentStmt->fetchAll();
    }
}

if ($page === 'create-post' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $createError = 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $alt = trim($_POST['image_alt'] ?? '');
        $sourceUrl = trim($_POST['image_source_url'] ?? '');
        $sourceName = trim($_POST['image_source_name'] ?? 'Diunggah oleh pengguna');
        $rawTags = trim($_POST['ds_tags'] ?? '');

        $validCategory = $pdo->prepare("SELECT id FROM ds_categories WHERE id = ?");
        $validCategory->execute([$categoryId]);
        $errors = [];
        if ($title === '' || mb_strlen($title) > 200) $errors[] = 'Judul wajib diisi (maksimal 200 karakter).';
        if ($author === '' || mb_strlen($author) > 100) $errors[] = 'Nama penulis wajib diisi (maksimal 100 karakter).';
        if ($excerpt === '') $errors[] = 'Ringkasan wajib diisi.';
        if ($content === '') $errors[] = 'Isi artikel wajib diisi.';
        if (!$validCategory->fetch()) $errors[] = 'Pilih kategori yang tersedia.';
        if ($alt === '') $errors[] = 'Alt text gambar wajib diisi.';
        if ($rawTags === '') $errors[] = 'Masukkan minimal dua tag, pisahkan dengan koma.';
        if ($sourceUrl !== '' && !filter_var($sourceUrl, FILTER_VALIDATE_URL)) $errors[] = 'URL sumber gambar tidak valid.';
        if (empty($_FILES['featured_image']) || $_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Pilih gambar unggulan untuk artikel.';
        }

        $ds_tags = array_values(array_unique(array_filter(array_map('trim', explode(',', $rawTags)))));
        if (count($ds_tags) < 2) $errors[] = 'Setiap artikel harus memiliki minimal dua tag.';
        foreach ($ds_tags as $tag) {
            if (mb_strlen($tag) > 80) $errors[] = 'Setiap tag maksimal 80 karakter.';
        }

        $imagePath = '';
        if (!$errors) {
            $file = $_FILES['featured_image'];
            if ($file['size'] > 3 * 1024 * 1024) {
                $errors[] = 'Ukuran gambar maksimal 3 MB.';
            } else {
                $imageInfo = @getimagesize($file['tmp_name']);
                $mime = $imageInfo['mime'] ?? '';
                $allowedMimes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];
                if (!$imageInfo || !isset($allowedMimes[$mime])) {
                    $errors[] = 'Format gambar harus JPG, PNG, atau WEBP.';
                } else {
                    $uploadDir = __DIR__ . '/uploads';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $fileName = bin2hex(random_bytes(16)) . '.' . $allowedMimes[$mime];
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $fileName)) {
                        $imagePath = 'uploads/' . $fileName;
                    } else {
                        $errors[] = 'Gagal menyimpan gambar. Periksa izin folder uploads.';
                    }
                }
            }
        }

        if (!$errors) {
            try {
                $pdo->beginTransaction();
                $slugBase = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title)), '-'));
                $slug = ($slugBase ?: 'artikel') . '-' . bin2hex(random_bytes(3));
                $insertPost = $pdo->prepare(
                    "INSERT INTO ds_posts (category_id, title, slug, excerpt, content, author_name, featured_image, image_alt, image_source_url, image_source_name, status, published_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', NOW())"
                );
                $insertPost->execute([$categoryId, $title, $slug, $excerpt, nl2br(e($content)), $author, $imagePath, $alt, $sourceUrl ?: '#', $sourceName]);
                $postId = (int)$pdo->lastInsertId();

                foreach ($ds_tags as $tagName) {
                    $tagSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $tagName)), '-'));
                    $tagSlug = $tagSlug ?: 'tag-' . bin2hex(random_bytes(3));
                    $tagInsert = $pdo->prepare("INSERT INTO ds_tags (name, slug) VALUES (?, ?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
                    $tagInsert->execute([$tagName, $tagSlug]);
                    $tagId = (int)$pdo->lastInsertId();
                    $link = $pdo->prepare("INSERT IGNORE INTO ds_post_tags (post_id, tag_id) VALUES (?, ?)");
                    $link->execute([$postId, $tagId]);
                }
                $pdo->commit();
                header('Location: index.php?page=post&slug=' . urlencode($slug));
                exit;
            } catch (Throwable $ex) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $createError = 'Artikel belum dapat disimpan. Periksa database dan coba kembali.';
            }
        } else {
            $createError = implode(' ', $errors);
        }
    }
}

if ($page === 'contact' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $contactError = 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
            $contactError = 'Mohon lengkapi semua kolom dan gunakan alamat email yang valid.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO ds_contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $contactSuccess = 'Pesan berhasil dikirim. Terima kasih sudah menghubungi DIGISAFE!';
        }
    }
}

$pageTitle = match ($page) {
    'about' => 'Tentang Saya — DIGISAFE',
    'contact' => 'Kontak — DIGISAFE',
    'create-post' => 'Kirim Artikel — DIGISAFE',
    'post' => isset($post) ? $post['title'] . ' — DIGISAFE' : 'Artikel tidak ditemukan — DIGISAFE',
    '404' => 'Halaman tidak ditemukan — DIGISAFE',
    default => 'DIGISAFE — Cerdas dan Aman di Dunia Digital'
};

require __DIR__ . '/includes/header.php';

if ($page === 'home') {
    $ds_posts = $pdo->query(
        "SELECT p.*, c.name AS category_name FROM ds_posts p
         JOIN ds_categories c ON c.id = p.category_id
         WHERE p.status = 'published' ORDER BY p.published_at DESC"
    )->fetchAll();
    require __DIR__ . '/pages/home.php';
} elseif ($page === 'about') {
    require __DIR__ . '/pages/about.php';
} elseif ($page === 'contact') {
    require __DIR__ . '/pages/contact.php';
} elseif ($page === 'post') {
    require __DIR__ . '/pages/post.php';
} elseif ($page === 'create-post') {
    require __DIR__ . '/pages/create-post.php';
} else {
    require __DIR__ . '/pages/404.php';
}
require __DIR__ . '/includes/footer.php';
