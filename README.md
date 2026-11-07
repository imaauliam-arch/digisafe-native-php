# DIGISAFE — Native PHP & MySQL

Nama : Ima Aulia Maghviroh
NIM : 09010626013
Mata kuliah : Literasi Digital untuk Sistem Informasi (SIF125105)
Dosen pengampu :  Ahmad Yusuf, M.Kom

Website literasi digital bertema **Keamanan Akun Digital**, dibuat menggunakan PHP Native, MySQL, HTML, dan CSS tanpa framework.

## Struktur folder

```text
digisafe_native_php/
├── assets/css/style.css
├── config/database.php
├── config/database.local.example.php
├── database/db_digisafe.sql
├── includes/header.php
├── includes/footer.php
├── pages/
│   ├── home.php
│   ├── about.php
│   ├── contact.php
│   ├── create-post.php
│   ├── post.php
│   └── 404.php
├── uploads/              # gambar yang diunggah pengunjung
├── index.php
└── README.md
```

## Fitur pengunjung

1. Melihat beranda, Tentang Saya, Kontak, dan detail artikel.
2. Mengirim artikel sendiri melalui menu **Kirim Artikel**.
3. Mengunggah gambar unggulan JPG, PNG, atau WEBP (maksimal 3 MB).
4. Menentukan kategori dan membuat tag sendiri (minimal dua tag, dipisahkan koma).
5. Memberi komentar pada artikel. Nama dan komentar ditampilkan, email tidak ditampilkan.
6. Mengirim pesan melalui formulir Kontak.

Artikel yang dikirim pengunjung langsung berstatus published pada contoh pembelajaran ini. Untuk website publik sungguhan, sebaiknya artikel dan komentar masuk status pending agar diperiksa admin terlebih dahulu.

## Cara menjalankan di XAMPP

1. Ekstrak folder `digisafe_native_php` ke `C:\\xampp\\htdocs\\`.
2. Jalankan Apache dan MySQL.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih Import dan masukkan `database/db_digisafe.sql`.
5. Salin `config/database.local.example.php` menjadi `config/database.local.php`, lalu isi password akun database lokal.
6. Buka `http://localhost/digisafe_native_php/`.

## Database

Database `db_digisafe` memiliki tabel:
- `ds_categories`: kategori artikel.
- `ds_posts`: artikel, penulis, dan metadata gambar.
- `ds_tags`: daftar tag.
- `ds_post_tags`: relasi many-to-many artikel dan tag.
- `ds_comments`: komentar pada artikel.
- `ds_contact_messages`: pesan kontak.

## Keamanan dasar

- PDO prepared statements untuk query input pengguna.
- HTML escaping saat menampilkan data pengguna.
- Token CSRF untuk formulir kirim artikel, komentar, dan kontak.
- Upload hanya menerima JPG, PNG, dan WEBP berdasarkan MIME hasil pemeriksaan file; maksimal 3 MB; nama file acak.
- Folder uploads memblokir eksekusi file PHP melalui `.htaccess`.

Untuk deployment publik, tambahkan login/akun pengguna, moderasi admin, rate limiting anti-spam, pembatasan ukuran konten, kebijakan privasi, dan konfigurasi server yang aman. Proteksi `.htaccess` berlaku pada Apache yang mengizinkan konfigurasi direktori.
