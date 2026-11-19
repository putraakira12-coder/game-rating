<?php
require_once __DIR__ . '/auth.php';

$admin = admin_login();
$active = $active ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Dashboard';
$flashMessages = ambil_flash();
?>
<!doctype html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="RatingTop — database peringkat game terbaik dari Steam, PlayStation, Nintendo, Play Store, App Store, dan Xbox.">
    <title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>
    <script>
        try {
            var savedTheme = localStorage.getItem('ratingtop-theme');
            if (savedTheme) document.documentElement.dataset.theme = savedTheme;
            if (localStorage.getItem('ratingtop-sidebar') === '1') document.documentElement.classList.add('sidebar-collapsed');
        } catch (err) {}
    </script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <div class="main-wrap">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="icon-button" id="sidebarToggle" aria-label="Buka atau tutup sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-title">
                    <span class="topbar-kicker"><?= APP_NAME ?></span>
                    <h1><?= e($pageTitle) ?></h1>
                </div>
            </div>

            <div class="topbar-right">
                <form class="top-search" method="get" action="<?= BASE_URL ?>/games.php">
                    <i class="bi bi-search"></i>
                    <input type="search" name="q" placeholder="Cari ID, game, genre, platform..." aria-label="Cari game">
                </form>
                <button type="button" class="icon-button theme-button" id="themeToggle" aria-label="Ganti tema" title="Ganti tema terang/gelap">
                    <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
                    <i class="bi bi-sun-fill theme-icon-light"></i>
                </button>
                <a class="topbar-admin" href="<?= BASE_URL ?>/profile.php" title="Buka profil admin">
                    <img src="<?= e(BASE_URL . '/' . ltrim($admin['foto'] ?? '', '/')) ?>" alt="Foto profil <?= e($admin['nama'] ?? '') ?>">
                    <span><strong><?= e(potong($admin['nama'] ?? 'Admin', 16)) ?></strong><small>@<?= e($admin['username'] ?? 'admin') ?></small></span>
                    <i class="bi bi-chevron-down"></i>
                </a>
            </div>
        </header>

        <?php if ($flashMessages): ?>
            <div class="flash-stack">
                <?php foreach ($flashMessages as $flash): ?>
                    <div class="flash flash-<?= e($flash['jenis']) ?>" role="alert">
                        <i class="bi bi-<?= $flash['jenis'] === 'sukses' ? 'check-circle-fill' : ($flash['jenis'] === 'gagal' ? 'exclamation-octagon-fill' : 'info-circle-fill') ?>"></i>
                        <span><?= e($flash['pesan']) ?></span>
                        <button type="button" class="flash-close" aria-label="Tutup">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <main class="content">
