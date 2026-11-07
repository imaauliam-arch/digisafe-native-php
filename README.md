# DIGISAFE — Digital Literacy & Account Security

Website edukasi literasi digital dengan tema **Keamanan Akun Digital**, dikembangkan menggunakan PHP Native dan MySQL sebagai proyek mata kuliah Literasi Digital untuk Sistem Informasi.

DIGISAFE bertujuan memberikan informasi dan edukasi kepada masyarakat mengenai pentingnya menjaga keamanan akun digital, mengenali ancaman siber, serta menerapkan perilaku yang aman dan bijak ketika beraktivitas di internet.

---

## Identitas Pengembang

| Keterangan | Detail |
|---|---|
| Nama | Ima Aulia Maghviroh |
| NIM | 09010626013 |
| Program Studi | Sistem Informasi |
| Fakultas | Sains dan Teknologi |
| Mata Kuliah | Literasi Digital untuk Sistem Informasi (SIF125105) |
| Dosen Pengampu | Ahmad Yusuf, M.Kom |

---

## Deskripsi Proyek

DIGISAFE merupakan website literasi digital yang menyediakan artikel edukatif seputar keamanan akun dan aktivitas digital. Website ini dirancang dengan dua jenis akses, yaitu pengunjung dan administrator.

Pengunjung dapat membaca artikel, melakukan pencarian berdasarkan kata kunci, memfilter artikel berdasarkan kategori, memberikan komentar, serta mengirimkan pesan melalui formulir kontak.

Administrator memiliki akses khusus untuk mengelola konten artikel, memantau aktivitas website, memoderasi komentar, dan membaca pesan yang dikirimkan pengunjung.

Website dibangun menggunakan PHP Native tanpa framework agar proses pengembangan dapat menerapkan pemahaman dasar pemrograman web, pengelolaan database, serta implementasi sistem CRUD.

---

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| PHP Native | Backend dan pemrosesan data |
| MySQL | Database website |
| HTML5 | Struktur halaman website |
| CSS3 | Desain dan tata letak antarmuka |
| JavaScript | Interaksi pada sisi frontend |
| PDO | Koneksi dan pengelolaan query database |
| Apache (XAMPP) | Web server lokal |
| Git & GitHub | Version control dan penyimpanan source code |

---

## Fitur Website

### 1. Fitur Pengunjung

Pengunjung dapat menggunakan website tanpa harus membuat akun atau melakukan login.

- **Beranda:** Menampilkan artikel-artikel literasi digital.
- **Detail Artikel:** Membaca isi artikel secara lengkap.
- **Pencarian Artikel:** Mencari artikel berdasarkan judul, ringkasan, maupun isi artikel.
- **Filter Kategori:** Menampilkan artikel berdasarkan kategori tertentu.
- **Komentar:** Mengirimkan komentar pada artikel tanpa harus login.
- **Notifikasi Komentar:** Komentar langsung ditampilkan setelah berhasil dikirim.
- **Peringatan Otomatis:** Sistem mendeteksi kata-kata kasar, tautan berlebihan, dan pola karakter mencurigakan pada komentar.
- **Kontak:** Mengirimkan pesan atau pertanyaan melalui formulir kontak.
- **Tentang Saya:** Menampilkan informasi mengenai pengembang website.

### 2. Fitur Administrator

Administrator harus melakukan login untuk mengakses halaman pengelolaan website.

- **Login Admin:** Autentikasi untuk membatasi akses ke halaman administrator.
- **Dashboard:** Menampilkan ringkasan informasi pengelolaan website.
- **Manajemen Artikel:** Menambahkan, mengedit, dan menghapus artikel.
- **Manajemen Kategori dan Tag:** Menggunakan kategori dan tag untuk mengelompokkan artikel.
- **Moderasi Komentar:** Melihat komentar pengunjung, memeriksa komentar yang ditandai sistem, menandai komentar sebagai aman, dan menghapus komentar yang melanggar etika.
- **Manajemen Pesan:** Melihat pesan yang dikirimkan melalui formulir kontak.
- **Monitoring Aktivitas:** Melihat catatan aktivitas pengunjung dan administrator.
- **Logout:** Mengakhiri sesi administrator.

### 3. Sistem Peringatan Komentar

DIGISAFE menerapkan pemeriksaan komentar otomatis untuk membantu menjaga kualitas diskusi.

Alur sistem:

1. Pengunjung mengirimkan komentar pada artikel.
2. Sistem memvalidasi nama, email, dan isi komentar.
3. Sistem memeriksa kemungkinan kata kasar, tautan berlebihan, dan pengulangan karakter yang mencurigakan.
4. Komentar langsung disimpan dan ditampilkan.
5. Jika terdeteksi mencurigakan, komentar diberi tanda agar dapat diperiksa administrator.
6. Administrator dapat menandai komentar sebagai aman atau menghapusnya jika melanggar etika.

**Catatan:** Pemeriksaan komentar menggunakan aturan dan pola sederhana, bukan kecerdasan buatan. Oleh karena itu, hasil deteksinya dapat mengalami kesalahan dan tetap membutuhkan pemeriksaan administrator.

---

## Struktur Folder

```text
digisafe_native_php/
│
├── admin/
│   ├── _init.php
│   ├── activities.php
│   ├── comments.php
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── messages.php
│   ├── post-form.php
│   ├── posts.php
│   └── setup.php
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   ├── images/
│   └── js/
│
├── config/
│   ├── database.php
│   └── database.local.example.php
│
├── database/
│   ├── db_digisafe.sql
│   └── migration_admin_monitoring.sql
│
├── includes/
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── pages/
│   ├── 404.php
│   ├── about.php
│   ├── contact.php
│   ├── home.php
│   └── post.php
│
├── uploads/
│
├── index.php
└── README.md
```

**Keterangan:**

- `admin/` — Halaman administrator untuk mengelola konten dan aktivitas website.
- `assets/` — File pendukung frontend seperti CSS, JavaScript, dan gambar.
- `config/` — Konfigurasi koneksi database.
- `database/` — File SQL untuk membuat database dan menjalankan migrasi.
- `includes/` — Komponen dan fungsi PHP yang digunakan bersama.
- `pages/` — Halaman utama yang ditampilkan kepada pengunjung.
- `uploads/` — Penyimpanan gambar artikel yang diunggah administrator.
- `index.php` — Entry point dan pengatur navigasi halaman website.

---

## Database

Nama database yang digunakan:

```sql
db_digisafe
```

Database terdiri dari beberapa tabel utama:

| Tabel | Fungsi |
|---|---|
| `ds_categories` | Menyimpan kategori artikel. |
| `ds_posts` | Menyimpan artikel dan informasi publikasinya. |
| `ds_tags` | Menyimpan daftar tag artikel. |
| `ds_post_tags` | Menghubungkan artikel dengan tag. |
| `ds_comments` | Menyimpan komentar pengunjung dan status pemeriksaannya. |
| `ds_contact_messages` | Menyimpan pesan dari formulir kontak. |
| `ds_admins` | Menyimpan akun administrator. |
| `ds_activity_logs` | Menyimpan catatan aktivitas website. |

Pada tabel `ds_comments`, sistem juga menggunakan kolom `is_flagged` dan `flag_reason` untuk menandai komentar yang terdeteksi mencurigakan.

---

## Cara Instalasi dan Menjalankan Website

### Persyaratan

Pastikan perangkat sudah memiliki:

- XAMPP.
- PHP 8.0 atau lebih baru.
- MySQL atau MariaDB.
- Browser seperti Google Chrome atau Microsoft Edge.
- Visual Studio Code (opsional, untuk mengedit source code).

### Langkah 1 — Menyiapkan Project

1. Clone repository DIGISAFE atau unduh source code dari GitHub.
2. Letakkan folder project di dalam direktori:

```text
C:\xampp\htdocs\
```

3. Pastikan nama folder project adalah:

```text
digisafe_native_php
```

### Langkah 2 — Menjalankan XAMPP

1. Buka XAMPP Control Panel.
2. Jalankan Apache.
3. Jalankan MySQL.

### Langkah 3 — Membuat Database

1. Buka phpMyAdmin melalui:

```text
http://localhost/phpmyadmin
```

2. Pilih menu **Import**.
3. Pilih file:

```text
database/db_digisafe.sql
```

4. Jalankan proses import.
5. Pastikan database `db_digisafe` berhasil dibuat.

### Langkah 4 — Mengatur Koneksi Database

1. Salin file:

```text
config/database.local.example.php
```

2. Ubah nama salinannya menjadi:

```text
config/database.local.php
```

3. Sesuaikan konfigurasi koneksi database lokal, termasuk nama database, username, dan password MySQL.

4. Pastikan file konfigurasi lokal tidak diunggah ke repository publik.

### Langkah 5 — Menjalankan Website

Buka browser dan akses:

```text
http://localhost/digisafe_native_php/
```

### Langkah 6 — Mengakses Administrator

Halaman administrator dapat diakses melalui:

```text
http://localhost/digisafe_native_php/admin/login.php
```

Untuk instalasi awal, gunakan halaman setup administrator sesuai konfigurasi project:

```text
http://localhost/digisafe_native_php/admin/setup.php
```

Ikuti instruksi setup untuk menyiapkan akun administrator. Setelah selesai, login melalui halaman admin.

---

## Keamanan Dasar

Beberapa mekanisme keamanan dasar yang diterapkan dalam pengembangan DIGISAFE:

- **PDO Prepared Statements:** Mengurangi risiko SQL Injection pada query yang menggunakan input pengguna.
- **HTML Escaping:** Menggunakan `htmlspecialchars()` untuk membantu mencegah Cross-Site Scripting (XSS) ketika menampilkan data pengguna.
- **CSRF Token:** Memvalidasi permintaan formulir untuk membantu mencegah Cross-Site Request Forgery.
- **Validasi Input:** Memeriksa data formulir sebelum disimpan ke database.
- **Validasi Upload:** Membatasi jenis gambar yang diunggah berdasarkan pemeriksaan MIME dan ukuran file.
- **Nama File Acak:** Mengurangi risiko konflik nama file pada direktori upload.
- **Proteksi Direktori Upload:** Menggunakan `.htaccess` untuk membantu mencegah eksekusi file PHP pada direktori upload di server Apache yang mendukung aturan tersebut.
- **Autentikasi Administrator:** Membatasi akses ke halaman pengelolaan website.

Mekanisme tersebut merupakan perlindungan dasar dan bukan jaminan keamanan menyeluruh. Untuk penggunaan pada server publik, diperlukan pengujian keamanan tambahan, konfigurasi HTTPS, pembatasan percobaan login, rate limiting, validasi upload yang lebih ketat, serta pengelolaan hak akses server.

---

## Pengembangan Selanjutnya

Beberapa pengembangan yang dapat dilakukan untuk meningkatkan fungsionalitas DIGISAFE:

- Menambahkan fitur pagination pada daftar artikel.
- Mengembangkan sistem pencarian yang lebih relevan.
- Meningkatkan deteksi spam dan komentar tidak pantas.
- Menambahkan statistik aktivitas pengunjung pada dashboard.
- Mengembangkan sistem backup database.
- Meningkatkan aksesibilitas dan responsivitas antarmuka.
- Mengembangkan kebijakan privasi dan perlindungan data pengguna.

---

## Repository

Source code project DIGISAFE tersedia di GitHub:

**[DIGISAFE — GitHub Repository](https://github.com/imaauliam-arch/digisafe-native-php)**

---

## Lisensi dan Tujuan Penggunaan

DIGISAFE dikembangkan sebagai proyek pembelajaran akademik untuk memenuhi tugas mata kuliah Literasi Digital untuk Sistem Informasi.

Seluruh fitur dan implementasi dalam proyek ini ditujukan untuk pembelajaran pengembangan website menggunakan PHP Native, MySQL, HTML, CSS, dan JavaScript.
