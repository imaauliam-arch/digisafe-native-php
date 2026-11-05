<?php
// Konfigurasi koneksi aman: kredensial lokal dipisahkan dari file yang diunggah ke GitHub.
$localConfig = __DIR__ . '/database.local.php';

if (!is_file($localConfig)) {
    exit('File konfigurasi database belum dibuat. Salin config/database.local.example.php menjadi config/database.local.php, lalu isi kredensial MySQL lokal.');
}

$config = require $localConfig;

$host = $config['host'] ?? 'localhost';
$dbname = $config['dbname'] ?? 'db_digisafe';
$username = $config['username'] ?? '';
$password = $config['password'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Jangan tampilkan detail koneksi atau kredensial kepada pengunjung website.
    error_log('DIGISAFE database connection error: ' . $e->getMessage());
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi database dan pastikan MySQL aktif.');
}
