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
        
        <nav class="nav-menu">
            <a href="index.php">Beranda</a>
            <a href="index.php?page=about">Tentang Saya</a>
            <a href="index.php?page=contact">Kontak</a>

            <a href="admin/login.php" class="nav-admin" aria-label="Login Administrator" title="Login Administrator">
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="18" height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>Admin</span>
            </a>
        </nav>
    </div>
</header>
<main>
