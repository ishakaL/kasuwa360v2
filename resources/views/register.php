<?php $title = 'Register | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="auth-shell">
    <div class="container auth-card-wrap">
        <div class="auth-card card">
            <h1>Create your account</h1>
            <form method="post" action="<?= APP_URL ?>/register">
                <?= csrfField(); ?>
                <div class="field-grid">
                    <label>
                        Full name
                        <input type="text" name="full_name" required>
                    </label>
                    <label>
                        Phone number
                        <input type="text" name="phone" required>
                    </label>
                    <label>
                        Email (optional)
                        <input type="email" name="email">
                    </label>
                    <label>
                        Account type
                        <select name="role">
                            <option value="buyer">Buyer / shopper</option>
                            <option value="vendor">Vendor</option>
                        </select>
                    </label>
                    <label>
                        Password
                        <input type="password" name="password" required>
                    </label>
                    <label>
                        Confirm password
                        <input type="password" name="confirm_password" required>
                    </label>
                </div>
                <button type="submit" class="button button-primary full-width">Register</button>
            </form>
            <p class="auth-subtext">Already have an account? <a href="<?= APP_URL ?>/login">Log in</a></p>
        </div>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
