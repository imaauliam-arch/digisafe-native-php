CREATE DATABASE IF NOT EXISTS db_digisafe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_digisafe;

CREATE TABLE IF NOT EXISTS ds_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ds_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    excerpt TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    author_name VARCHAR(100) NOT NULL DEFAULT 'Admin DIGISAFE',
    featured_image VARCHAR(500) NOT NULL,
    image_alt VARCHAR(255) NOT NULL,
    image_source_url VARCHAR(500) NOT NULL,
    image_source_name VARCHAR(100) NOT NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'published',
    published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_category FOREIGN KEY (category_id) REFERENCES ds_categories(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ds_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ds_post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    CONSTRAINT fk_post_tags_post FOREIGN KEY (post_id) REFERENCES ds_posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_post_tags_tag FOREIGN KEY (tag_id) REFERENCES ds_tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ds_contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO ds_categories (id, name, slug) VALUES
(1,'Keamanan Akun','keamanan-akun'),
(2,'Ancaman Digital','ancaman-digital'),
(3,'Privasi Digital','privasi-digital'),
(4,'Etika Bermedia','etika-bermedia')
ON DUPLICATE KEY UPDATE name=VALUES(name), slug=VALUES(slug);

INSERT INTO ds_tags (name, slug) VALUES
('password','password'),('autentikasi dua faktor','autentikasi-dua-faktor'),
('perlindungan akun','perlindungan-akun'),('phishing','phishing'),
('tautan palsu','tautan-palsu'),('anti-penipuan','anti-penipuan'),
('jejak digital','jejak-digital'),('privasi','privasi'),('media sosial','media-sosial')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO ds_posts
(id, category_id, title, slug, excerpt, content, featured_image, image_alt, image_source_url, image_source_name, status, published_at)
VALUES
(1,1,'Cara Membuat Kata Sandi yang Kuat dan Sulit Ditebak','cara-membuat-kata-sandi-kuat','Panduan praktis membuat kata sandi unik, panjang, dan tidak mudah ditebak untuk melindungi akun digital.','<p>Kata sandi atau password merupakan salah satu lapisan perlindungan pertama ketika seseorang menggunakan layanan digital. Email, media sosial, aplikasi belajar, hingga layanan perbankan biasanya meminta kata sandi untuk memastikan bahwa akun diakses oleh pemiliknya. Sayangnya, masih banyak pengguna yang memilih kombinasi sederhana seperti tanggal lahir, nama sendiri, angka berurutan, atau kata “password”. Kombinasi seperti itu relatif mudah ditebak, terutama jika informasi pribadi pengguna tersedia di media sosial.</p>
<h2>Mengapa kata sandi harus kuat?</h2>
<p>Kata sandi yang lemah dapat membuat akun lebih rentan terhadap percobaan login otomatis maupun tebakan berdasarkan informasi pribadi. Risiko menjadi lebih besar ketika satu kata sandi digunakan untuk banyak akun. Jika satu layanan mengalami kebocoran data, pelaku dapat mencoba kombinasi email dan kata sandi yang sama pada layanan lain. Kebiasaan menggunakan satu password untuk semua akun membuat dampak kebocoran menjadi lebih luas.</p>
<h2>Langkah membuat kata sandi yang lebih aman</h2>
<p>Pertama, gunakan kata sandi yang panjang. Frasa sandi yang terdiri dari beberapa kata acak dapat lebih mudah diingat sekaligus lebih sulit ditebak dibandingkan kata pendek. Kedua, hindari informasi yang mudah diketahui orang lain, seperti nama, tanggal lahir, nama sekolah, atau nomor telepon. Ketiga, buat kata sandi berbeda untuk setiap akun penting, khususnya email utama dan akun keuangan. Keempat, gunakan pengelola kata sandi tepercaya apabila kesulitan mengingat banyak kombinasi unik.</p>
<p>Jangan membagikan kata sandi kepada teman atau mengirimkannya melalui pesan yang tidak aman. Pihak resmi penyedia layanan tidak seharusnya meminta password atau kode OTP melalui pesan pribadi. Jika tersedia, aktifkan autentikasi dua faktor agar proses masuk membutuhkan verifikasi tambahan. Dengan begitu, kata sandi yang diketahui orang lain belum tentu cukup untuk mengambil alih akun.</p>
<h2>Periksa dan perbarui kebiasaan</h2>
<p>Segera ganti kata sandi jika ada indikasi akun diakses orang lain, perangkat hilang, atau layanan mengumumkan kebocoran data. Gunakan perangkat pribadi ketika mengganti informasi keamanan dan pastikan alamat situs benar. Hindari memasukkan password melalui tautan mencurigakan yang dikirim lewat pesan. Biasakan memeriksa aktivitas login dan keluarkan sesi yang tidak dikenal melalui pengaturan keamanan akun.</p>
<p>Kesimpulannya, kata sandi kuat bukan sekadar kombinasi simbol yang rumit. Perlindungan yang baik juga membutuhkan kata sandi unik, kerahasiaan, verifikasi tambahan, serta kebiasaan memeriksa aktivitas akun. Langkah kecil tersebut dapat mengurangi risiko penyalahgunaan identitas digital.</p>','https://images.unsplash.com/photo-1614064641938-3bbee52942c?auto=format&fit=crop&w=1200&q=85','Ilustrasi gembok sebagai simbol perlindungan kata sandi dan keamanan akun digital.','https://unsplash.com/photos/a-close-up-of-a-padlock-on-a-black-background-1614064641938','Unsplash','published','2026-11-05 08:00:00'),
(2,2,'Waspada Phishing: Kenali Pesan dan Tautan Penipuan','waspada-phishing','Pelajari ciri pesan phishing dan cara memeriksa tautan sebelum memberikan informasi pribadi.','<p>Phishing adalah salah satu bentuk penipuan digital yang berusaha membuat korban menyerahkan informasi sensitif, misalnya nama pengguna, kata sandi, nomor kartu, atau kode verifikasi. Pelaku biasanya menyamar sebagai pihak yang terlihat tepercaya, seperti bank, marketplace, layanan pengiriman, institusi pendidikan, atau penyedia layanan digital. Pesan dapat dikirim melalui email, SMS, aplikasi percakapan, maupun media sosial.</p>
<h2>Bagaimana phishing bekerja?</h2>
<p>Pelaku sering membuat situasi yang membuat calon korban merasa takut atau terburu-buru. Contohnya, pesan menyatakan bahwa akun akan diblokir, paket tertahan, hadiah harus segera diklaim, atau transaksi mencurigakan perlu dikonfirmasi. Pesan tersebut biasanya menyertakan tautan menuju halaman tiruan yang tampilannya menyerupai situs resmi. Ketika korban memasukkan data, informasi tersebut dapat diterima oleh pelaku.</p>
<h2>Ciri-ciri pesan yang perlu dicurigai</h2>
<p>Perhatikan alamat pengirim dan alamat situs secara teliti. Nama tampilan pengirim bisa dibuat mirip dengan nama perusahaan resmi, tetapi alamat email atau domainnya berbeda. Waspadai ejaan yang tidak wajar, sapaan terlalu umum, lampiran yang tidak diminta, serta permintaan data rahasia. Tautan dengan alamat sangat panjang, salah eja, atau domain tambahan yang tidak dikenal juga perlu diperiksa. Namun, pesan yang terlihat rapi dan bebas kesalahan tetap bisa merupakan phishing.</p>
<p>Tekanan waktu merupakan tanda lain yang patut diwaspadai. Pelaku mungkin meminta korban bertindak dalam beberapa menit agar tidak sempat memeriksa kebenaran pesan. Jangan langsung menekan tautan hanya karena pesan menggunakan logo atau desain yang terlihat profesional. Logo dan identitas visual dapat disalin dengan mudah.</p>
<h2>Langkah aman sebelum mengeklik</h2>
<p>Jangan membuka tautan dari pesan yang tidak diminta. Jika pesan mengatasnamakan bank atau layanan tertentu, buka aplikasi resmi atau ketik alamat situs secara manual melalui browser. Hubungi layanan pelanggan dari kanal resmi yang tercantum pada situs atau aplikasi, bukan nomor yang diberikan oleh pesan mencurigakan. Jangan pernah memberikan kata sandi, PIN, atau OTP kepada siapa pun.</p>
<p>Jika telanjur memasukkan informasi, segera ubah kata sandi melalui situs atau aplikasi resmi, keluarkan sesi login yang tidak dikenal, aktifkan autentikasi dua faktor, dan hubungi penyedia layanan terkait. Apabila informasi keuangan terlibat, hubungi bank atau penyedia pembayaran secepatnya. Simpan bukti pesan untuk membantu proses pelaporan.</p>
<p>Phishing memanfaatkan kepercayaan dan kelengahan manusia, bukan hanya kelemahan teknologi. Karena itu, kebiasaan berhenti sejenak, memeriksa sumber, dan mengonfirmasi melalui kanal resmi merupakan perlindungan yang sangat penting.</p>','https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&fit=crop&w=1200&q=85','Ilustrasi seseorang menggunakan laptop untuk memeriksa keamanan aktivitas digital.','https://unsplash.com/photos/person-using-laptop-1563013544-824ae1b704d3','Unsplash','published','2026-11-05 08:00:00'),
(3,3,'Jejak Digital: Pikirkan Sebelum Membagikan Sesuatu','jejak-digital-dan-privasi','Memahami jejak digital, dampaknya terhadap privasi, dan cara mengelola informasi pribadi di internet.','<p>Jejak digital adalah rekaman aktivitas seseorang ketika menggunakan internet dan perangkat digital. Jejak tersebut dapat terbentuk saat mengunggah foto, menulis komentar, menyukai konten, mengisi formulir, mencari informasi, atau menggunakan aplikasi. Sebagian jejak dibuat secara aktif oleh pengguna, misalnya ketika membagikan cerita di media sosial. Sebagian lainnya terbentuk secara pasif melalui data penggunaan, cookie, lokasi yang diizinkan, dan informasi teknis perangkat.</p>
<h2>Mengapa jejak digital perlu diperhatikan?</h2>
<p>Informasi yang dibagikan secara online dapat disalin, disimpan, dan disebarkan kembali oleh orang lain. Menghapus unggahan dari akun pribadi tidak selalu berarti salinannya hilang dari internet. Foto, komentar, atau informasi pribadi yang pernah dipublikasikan dapat muncul kembali di kemudian hari. Hal ini dapat memengaruhi privasi, reputasi, hubungan sosial, bahkan kesempatan pendidikan dan pekerjaan.</p>
<p>Contohnya, seseorang mengunggah foto kartu identitas karena ingin menunjukkan keberhasilan membuat dokumen baru. Padahal, foto tersebut mungkin memperlihatkan nomor identitas, alamat, tanggal lahir, atau informasi lain yang seharusnya tidak diketahui publik. Contoh lain adalah membagikan lokasi rumah secara real time atau mengunggah jadwal aktivitas harian secara terbuka. Informasi kecil yang terlihat tidak berbahaya dapat digabungkan untuk memperkirakan kebiasaan dan keberadaan seseorang.</p>
<h2>Cara mengelola jejak digital</h2>
<p>Pertama, pikirkan tujuan dan dampak sebelum mengunggah konten. Tanyakan kepada diri sendiri apakah informasi tersebut aman diketahui orang lain dan apakah unggahan itu masih pantas dilihat beberapa tahun mendatang. Kedua, atur privasi akun media sosial. Batasi siapa yang dapat melihat unggahan, daftar teman, nomor telepon, dan informasi profil. Ketiga, hindari membagikan data sensitif seperti alamat rumah, dokumen identitas, kode OTP, dan detail keuangan.</p>
<p>Keempat, periksa izin aplikasi. Aplikasi tidak selalu membutuhkan akses ke kamera, mikrofon, kontak, atau lokasi untuk menjalankan fungsi utamanya. Berikan izin sesuai kebutuhan dan cabut akses yang tidak relevan. Kelima, tinjau unggahan lama dan hapus informasi yang tidak lagi perlu ditampilkan. Periksa juga akun yang sudah tidak digunakan dan tutup akun tersebut melalui prosedur resmi jika memang tidak diperlukan.</p>
<h2>Berkomunikasi secara bertanggung jawab</h2>
<p>Jejak digital juga berkaitan dengan etika. Hindari menyebarkan informasi yang belum terverifikasi, menghina orang lain, atau membagikan foto seseorang tanpa izin. Hargai privasi orang lain sebagaimana kita ingin privasi sendiri dihargai. Jika menemukan konten yang merugikan, gunakan fitur pelaporan pada platform terkait dan jangan ikut menyebarkannya.</p>
<p>Jejak digital tidak harus selalu dianggap buruk. Portofolio, karya, kegiatan positif, dan kontribusi dalam komunitas dapat menjadi rekam jejak yang bermanfaat. Kuncinya adalah mengelola informasi dengan sadar. Dengan berpikir sebelum membagikan, memeriksa pengaturan privasi, dan berkomunikasi secara etis, kita dapat membangun identitas digital yang lebih aman dan bertanggung jawab.</p>','https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=85','Ilustrasi aktivitas seseorang menggunakan perangkat digital dan terhubung dengan internet.','https://unsplash.com/photos/person-using-laptop-1516321318423','Unsplash','published','2026-11-05 08:00:00')
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), content=VALUES(content), featured_image=VALUES(featured_image), image_alt=VALUES(image_alt), image_source_url=VALUES(image_source_url), image_source_name=VALUES(image_source_name);

INSERT IGNORE INTO ds_post_tags (post_id, tag_id) SELECT 1, id FROM ds_tags WHERE name='password';
INSERT IGNORE INTO ds_post_tags (post_id, tag_id) SELECT 1, id FROM ds_tags WHERE name='autentikasi dua faktor';
INSERT IGNORE INTO ds_post_tags (post_id, tag_id) SELECT 1, id FROM ds_tags WHERE name='perlindungan akun';
INSERT IGNORE INTO ds_post_tags (post_id, tag_id) SELECT 2, id FROM ds_tags WHERE name='phishing';
INSERT IGNORE INTO ds_post_tags (post_id, tag_id) SELECT 2, id FROM ds_tags WHERE name='tautan palsu';
INSERT IGNORE INTO ds_post_tags (post_id, tag_id) SELECT 2, id FROM ds_tags WHERE name='anti-penipuan';
INSERT IGNORE INTO ds_post_tags (post_id, tag_id) SELECT 3, id FROM ds_tags WHERE name='jejak digital';
INSERT IGNORE INTO ds_post_tags (post_id, tag_id) SELECT 3, id FROM ds_tags WHERE name='privasi';
INSERT IGNORE INTO ds_post_tags (post_id, tag_id) SELECT 3, id FROM ds_tags WHERE name='media sosial';

-- Untuk melihat pesan yang dikirim melalui formulir Kontak:
-- SELECT * FROM ds_contact_messages ORDER BY created_at DESC;


CREATE TABLE IF NOT EXISTS ds_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    commenter VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    comment_body TEXT NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES ds_posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel ds_comments menyimpan komentar pengunjung pada masing-masing artikel.
-- Tag yang dikirim pengguna akan dibuat otomatis pada tabel ds_tags dan dihubungkan melalui ds_post_tags.
