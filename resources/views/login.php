<?php $title = 'Login | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="auth-shell">
    <div class="container auth-card-wrap">
        <div class="auth-card card">
            <h1>Welcome back</h1>
            <form method="post" action="<?= APP_URL ?>/login">
                <?= csrfField(); ?>
                <label>
                    Phone or email
                    <input type="text" name="identifier" required>
                </label>
                <label>
                    Password
                    <input type="password" name="password" required>
                </label>
                <button type="submit" class="button button-primary full-width">Log in</button>
            </form>
            <p class="auth-subtext">New to Kasuwa360? <a href="<?= APP_URL ?>/register">Register here</a></p>
        </div>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
