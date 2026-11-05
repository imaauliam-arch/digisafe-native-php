<?php
$pageTitle = $pageTitle ?? 'DIGISAFE — Cerdas dan Aman di Dunia Digital';
$currentPage = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DIGISAFE membagikan edukasi keamanan akun dan literasi digital.">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="index.php"><span class="brand-mark">D</span><span>DIGI<span class="brand-accent">SAFE</span></span></a>
        <nav aria-label="Navigasi utama">
            <a class="<?= $currentPage === 'home' ? 'active' : '' ?>" href="index.php">Beranda</a>
            <a class="<?= $currentPage === 'about' ? 'active' : '' ?>" href="index.php?page=about">Tentang Saya</a>
            <a class="<?= $currentPage === 'create-post' ? 'active' : '' ?>" href="index.php?page=create-post">Kirim Artikel</a>
            <a class="<?= $currentPage === 'contact' ? 'active' : '' ?>" href="index.php?page=contact">Kontak</a>
        </nav>
    </div>
</header>
<main>
