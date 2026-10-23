<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kasuwa360 is a local multi-vendor marketplace for Nigerian buyers and sellers.">
    <title><?= e($title ?? 'Kasuwa360'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="<?= APP_URL ?>">
                <span class="brand-mark">K</span>
                <span>Kasuwa360</span>
            </a>
            <nav class="main-nav">
                <a href="<?= APP_URL ?>/">Home</a>
                <a href="<?= APP_URL ?>/marketplace">Marketplace</a>
                <?php if (isLoggedIn() && isVendor()): ?>
                    <a href="<?= APP_URL ?>/dashboard">Dashboard</a>
                <?php endif; ?>
                <?php if (isLoggedIn() && isAdmin()): ?>
                    <a href="<?= APP_URL ?>/admin/dashboard">Admin</a>
                <?php endif; ?>
            </nav>
            <div class="header-actions">
                <?php if (isLoggedIn()): ?>
                    <span class="user-pill">Hi, <?= e(currentUser()['full_name'] ?? 'Member'); ?></span>
                    <a class="button button-secondary" href="<?= APP_URL ?>/logout">Logout</a>
                <?php else: ?>
                    <a class="button button-secondary" href="<?= APP_URL ?>/login">Login</a>
                    <a class="button button-primary" href="<?= APP_URL ?>/register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php $flashes = getFlashMessages(); if (!empty($flashes)): ?>
        <div class="container flash-stack">
            <?php foreach ($flashes as $flash): ?>
                <div class="flash flash-<?= e($flash['type'] ?? 'info'); ?>"><?= e($flash['message'] ?? ''); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
