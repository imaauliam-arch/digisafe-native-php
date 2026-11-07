
<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// ==========================================
// PENGATURAN HALAMAN
// ==========================================

$page = $_GET['page'] ?? 'home';

$allowedPages = ['home', 'about', 'contact', 'post'];

if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);
    $page = '404';
}

// Ambil semua kategori
$ds_categories = $pdo->query(
    'SELECT id, name, slug FROM ds_categories ORDER BY name'
)->fetchAll();


// ==========================================
// HALAMAN BERANDA
// ==========================================

if ($page === 'home') {
    $search = trim($_GET['q'] ?? '');
    $categoryId = (int) ($_GET['category'] ?? 0);

    $conditions = ["p.status = 'published'"];
    $params = [];

    // Pencarian artikel
    if ($search !== '') {
        $conditions[] = '(p.title LIKE ? OR p.excerpt LIKE ? OR p.content LIKE ?)';

        $like = '%' . $search . '%';

        array_push($params, $like, $like, $like);

        log_activity(
            $pdo,
            'search',
            'Pengunjung mencari: ' . $search,
            'search'
        );
    }

    // Filter kategori
    if ($categoryId > 0) {
        $conditions[] = 'p.category_id = ?';
        $params[] = $categoryId;

        $catName = '';

        foreach ($ds_categories as $cat) {
            if ((int) $cat['id'] === $categoryId) {
                $catName = $cat['name'];
                break;
            }
        }

        log_activity(
            $pdo,
            'category_filter',
            'Pengunjung memfilter kategori: ' . ($catName ?: 'ID ' . $categoryId),
            'category',
            $categoryId
        );
    }

    // Ambil artikel
    $sql = "
        SELECT p.*, c.name AS category_name
        FROM ds_posts p
        JOIN ds_categories c ON c.id = p.category_id
        WHERE " . implode(' AND ', $conditions) . "
        ORDER BY p.published_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $ds_posts = $stmt->fetchAll();

    if ($search === '' && $categoryId === 0) {
        log_activity(
            $pdo,
            'page_view',
            'Pengunjung membuka halaman Beranda',
            'page'
        );
    }
}


// ==========================================
// HALAMAN DETAIL ARTIKEL
// ==========================================

if ($page === 'post') {
    $slug = $_GET['slug'] ?? '';

    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name
        FROM ds_posts p
        JOIN ds_categories c ON c.id = p.category_id
        WHERE p.slug = ?
        AND p.status = 'published'
        LIMIT 1
    ");

    $stmt->execute([$slug]);

    $post = $stmt->fetch();

    if (!$post) {
        http_response_code(404);
        $page = '404';
    } else {

        // Catat aktivitas membaca artikel
        log_activity(
            $pdo,
            'article_view',
            'Pengunjung membaca artikel: ' . $post['title'],
            'post',
            (int) $post['id']
        );

        // Ambil tag artikel
        $tagStmt = $pdo->prepare("
            SELECT t.name
            FROM ds_tags t
            JOIN ds_post_tags pt ON pt.tag_id = t.id
            WHERE pt.post_id = ?
            ORDER BY t.name
        ");

        $tagStmt->execute([$post['id']]);

        $postTags = $tagStmt->fetchAll();


        // ==================================
        // KIRIM KOMENTAR
        // ==================================

        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['submit_comment'])
        ) {
            if (!verify_csrf()) {

                $commentError = 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.';

            } else {

                $commenter = trim($_POST['commenter'] ?? '');
                $commentEmail = trim($_POST['comment_email'] ?? '');
                $commentBody = trim($_POST['comment_body'] ?? '');

                // Validasi data
                if (
                    $commenter === '' ||
                    !filter_var($commentEmail, FILTER_VALIDATE_EMAIL) ||
                    $commentBody === ''
                ) {
                    $commentError = 'Nama, email valid, dan komentar wajib diisi.';

                } elseif (
                    mb_strlen($commenter) > 100 ||
                    mb_strlen($commentBody) > 2000
                ) {
                    $commentError = 'Nama maksimal 100 karakter dan komentar maksimal 2.000 karakter.';

                } else {

                    // Periksa komentar secara otomatis
                    $warning = check_comment_warning($commentBody);

                    // Semua komentar langsung ditampilkan
                    // Komentar mencurigakan tetap tampil tetapi ditandai
                    $insert = $pdo->prepare("
                        INSERT INTO ds_comments
                        (
                            post_id,
                            commenter,
                            email,
                            comment_body,
                            status,
                            is_flagged,
                            flag_reason
                        )
                        VALUES (?, ?, ?, ?, 'approved', ?, ?)
                    ");

                    $insert->execute([
                        $post['id'],
                        $commenter,
                        $commentEmail,
                        $commentBody,
                        $warning !== null ? 1 : 0,
                        $warning
                    ]);

                    $commentId = (int) $pdo->lastInsertId();


                    // Catat aktivitas komentar
                    log_activity(
                        $pdo,
                        'comment_submit',
                        'Pengunjung mengirim komentar pada artikel: ' . $post['title'],
                        'comment',
                        $commentId
                    );


                    // Catat jika komentar terdeteksi mencurigakan
                    if ($warning !== null) {
                        log_activity(
                            $pdo,
                            'comment_flagged',
                            'Komentar #' . $commentId . ' ditandai otomatis: ' . $warning,
                            'comment',
                            $commentId
                        );

                        $_SESSION['comment_notice'] =
                            'Komentar berhasil dikirim. Sistem mendeteksi kemungkinan pelanggaran etika, tetapi komentar tetap ditampilkan dan dapat diperiksa admin.';

                    } else {

                        $_SESSION['comment_notice'] =
                            'Komentar berhasil dikirim dan langsung ditampilkan. Terima kasih sudah berdiskusi dengan sopan!';
                    }


                    // Kembali ke artikel
                    header(
                        'Location: index.php?page=post&slug=' .
                        urlencode($slug) .
                        '#komentar'
                    );

                    exit;
                }
            }
        }


        // ==================================
        // AMBIL KOMENTAR
        // ==================================

        // Komentar yang ditandai tetap tampil
        // selama belum dihapus oleh admin
        $commentStmt = $pdo->prepare("
            SELECT commenter, comment_body, created_at
            FROM ds_comments
            WHERE post_id = ?
            AND status = 'approved'
            ORDER BY created_at DESC
        ");

        $commentStmt->execute([$post['id']]);

        $ds_comments = $commentStmt->fetchAll();
    }
}


// ==========================================
// HALAMAN TENTANG SAYA
// ==========================================

if ($page === 'about') {
    log_activity(
        $pdo,
        'page_view',
        'Pengunjung membuka halaman Tentang Saya',
        'page'
    );
}


// ==========================================
// HALAMAN KONTAK
// ==========================================

if ($page === 'contact') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (!verify_csrf()) {

            $contactError = 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.';

        } else {

            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if (
                $name === '' ||
                !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                $subject === '' ||
                $message === ''
            ) {

                $contactError = 'Mohon lengkapi semua kolom dan gunakan alamat email yang valid.';

            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO ds_contact_messages
                    (name, email, subject, message)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $name,
                    $email,
                    $subject,
                    $message
                ]);

                $messageId = (int) $pdo->lastInsertId();

                log_activity(
                    $pdo,
                    'contact_submit',
                    'Pengunjung mengirim pesan kontak: ' . $subject,
                    'contact_message',
                    $messageId
                );

                $contactSuccess = 'Pesan berhasil dikirim. Terima kasih sudah menghubungi DIGISAFE!';
            }
        }

    } else {

        log_activity(
            $pdo,
            'page_view',
            'Pengunjung membuka halaman Kontak',
            'page'
        );
    }
}


// ==========================================
// JUDUL HALAMAN
// ==========================================

$pageTitle = match ($page) {

    'about' => 'Tentang Saya — DIGISAFE',

    'contact' => 'Kontak — DIGISAFE',

    'post' => isset($post)
        ? $post['title'] . ' — DIGISAFE'
        : 'Artikel tidak ditemukan — DIGISAFE',

    '404' => 'Halaman tidak ditemukan — DIGISAFE',

    default => 'DIGISAFE — Cerdas dan Aman di Dunia Digital'
};


// ==========================================
// TAMPILKAN HALAMAN
// ==========================================

require __DIR__ . '/includes/header.php';

if ($page === 'home') {

    require __DIR__ . '/pages/home.php';

} elseif ($page === 'about') {

    require __DIR__ . '/pages/about.php';

} elseif ($page === 'contact') {

    require __DIR__ . '/pages/contact.php';

} elseif ($page === 'post') {

    require __DIR__ . '/pages/post.php';

} else {

    require __DIR__ . '/pages/404.php';
}

require __DIR__ . '/includes/footer.php';
