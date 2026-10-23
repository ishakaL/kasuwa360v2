<?php $title = 'Shop profile | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="page-shell">
    <div class="container form-shell">
        <div class="card form-card">
            <div class="page-head">
                <div>
                    <span class="eyebrow">Shop profile</span>
                    <h1><?= $shop ? 'Update shop details' : 'Create your shop'; ?></h1>
                </div>
            </div>
            <form method="post" action="<?= APP_URL ?>/vendor/shop">
                <?= csrfField(); ?>
                <div class="field-grid">
                    <label>
                        Shop name
                        <input type="text" name="shop_name" value="<?= e($shop['shop_name'] ?? ''); ?>" required>
                    </label>
                    <label>
                        Public contact phone
                        <input type="text" name="phone" value="<?= e($shop['phone'] ?? ''); ?>">
                    </label>
                    <label>
                        WhatsApp number
                        <input type="text" name="whatsapp" value="<?= e($shop['whatsapp'] ?? ''); ?>">
                    </label>
                    <label>
                        Email
                        <input type="email" name="email" value="<?= e($shop['email'] ?? ''); ?>">
                    </label>
                    <label>
                        Address
                        <input type="text" name="address" value="<?= e($shop['address'] ?? ''); ?>">
                    </label>
                    <label>
                        City
                        <input type="text" name="city" value="<?= e($shop['city'] ?? ''); ?>">
                    </label>
                    <label>
                        State
                        <input type="text" name="state" value="<?= e($shop['state'] ?? ''); ?>">
                    </label>
                    <label>
                        Market / area name
                        <input type="text" name="market_name" value="<?= e($shop['market_name'] ?? ''); ?>">
                    </label>
                    <label class="full-span">
                        Description
                        <textarea name="description" rows="4"><?= e($shop['description'] ?? ''); ?></textarea>
                    </label>
                    <label class="full-span">
                        Opening hours
                        <input type="text" name="opening_hours" value="<?= e($shop['opening_hours'] ?? ''); ?>">
                    </label>
                </div>
                <button type="submit" class="button button-primary">Save shop</button>
            </form>
        </div>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
