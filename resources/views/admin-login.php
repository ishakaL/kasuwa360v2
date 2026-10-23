<?php $title = 'Admin login | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="auth-shell">
    <div class="container auth-card-wrap">
        <div class="auth-card card">
            <h1>Admin access</h1>
            <form method="post" action="<?= APP_URL ?>/admin/login">
                <?= csrfField(); ?>
                <label>
                    Email or phone
                    <input type="text" name="identifier" required>
                </label>
                <label>
                    Password
                    <input type="password" name="password" required>
                </label>
                <button type="submit" class="button button-primary full-width">Login as admin</button>
            </form>
        </div>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
